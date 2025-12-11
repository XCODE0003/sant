<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\TelegramService;
use App\Services\TinkoffService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct(
        private TinkoffService $tinkoffService
    ) {}

    /**
     * Обработка уведомлений от Т-Банка
     */
    public function notification(Request $request): Response
    {
        $data = $request->all();

        Log::info('Tinkoff notification received', $data);

        // Проверяем токен
        if (!$this->tinkoffService->verifyNotificationToken($data)) {
            Log::warning('Tinkoff notification token verification failed', $data);
            return response('Invalid token', 400);
        }

        $orderId = $data['OrderId'] ?? null;
        $paymentId = $data['PaymentId'] ?? null;
        $status = $data['Status'] ?? null;
        $success = $data['Success'] ?? false;

        if (!$orderId || !$paymentId) {
            Log::warning('Tinkoff notification missing required fields', $data);
            return response('Missing required fields', 400);
        }

        $order = Order::where('number', $orderId)->first();

        if (!$order) {
            Log::warning('Tinkoff notification: order not found', ['order_id' => $orderId]);
            return response('Order not found', 404);
        }

        // Обновляем данные платежа
        $order->update([
            'payment_id' => $paymentId,
            'payment_status' => $status,
            'payment_data' => $data,
        ]);

        // Обновляем статус заказа в зависимости от статуса платежа
        if ($success && $status === 'CONFIRMED') {
            $order->update(['status' => Order::STATUS_CONFIRMED]);
        } elseif ($status === 'REVERSED' || $status === 'CANCELED') {
            $order->update(['status' => Order::STATUS_CANCELLED]);
        }
        if($order->status === Order::STATUS_CONFIRMED) {
            (new TelegramService())->notifyPayment($order);
        }

        // Возвращаем OK
        return response('OK', 200);
    }

    /**
     * Страница успешной оплаты
     */
    public function success(Request $request)
    {
        $orderId = $request->get('OrderId');

        if ($orderId) {
            $order = Order::where('number', $orderId)->first();

            if ($order) {
                return redirect()
                    ->route('orders.show', ['order' => $order->uuid])
                    ->with('success', 'Платеж успешно проведен!');
            }
        }

        return redirect()->route('home')->with('success', 'Платеж успешно проведен!');
    }

    /**
     * Страница неуспешной оплаты
     */
    public function fail(Request $request)
    {
        $orderId = $request->get('OrderId');

        if ($orderId) {
            $order = Order::where('number', $orderId)->first();

            if ($order) {
                return redirect()
                    ->route('orders.show', ['order' => $order->uuid])
                    ->with('error', 'Оплата не была завершена. Попробуйте еще раз.');
            }
        }

        return redirect()->route('checkout')->with('error', 'Оплата не была завершена.');
    }
}