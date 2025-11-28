<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Services\TinkoffService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CheckoutController extends Controller
{
    public function __construct(
        private TinkoffService $tinkoffService
    ) {
    }
    public function index(): Response
    {
        return Inertia::render('Checkout');
    }

    public function store(StoreOrderRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $items = collect($data['items'] ?? [])
            ->map(function (array $item): array {
                $quantity = (int) ($item['quantity'] ?? 1);
                $finalPrice = (float) ($item['final_price'] ?? $item['price'] ?? 0);
                $originalPrice = (float) ($item['price'] ?? $finalPrice);

                return [
                    'id' => $item['id'],
                    'title' => $item['title'],
                    'slug' => $item['slug'] ?? null,
                    'quantity' => $quantity,
                    'price' => $originalPrice,
                    'final_price' => $finalPrice,
                    'total_price' => round($finalPrice * $quantity, 2),
                    'image' => $item['image'] ?? null,
                ];
            })
            ->values();

        $totalPrice = $items->sum('total_price');
        $itemsCount = $items->sum('quantity');

        $address = $data['address'] ?? [];
        $paymentMethod = $data['payment_method'] ?? 'cash';

        if ($paymentMethod !== 'cash') {
            $paymentMethod = 'card';
        }

        // Создаем заказ
        $order = Order::create([
            'customer_first_name' => $data['first_name'],
            'customer_last_name' => $data['last_name'],
            'customer_phone' => $data['phone'],
            'customer_email' => $data['email'],
            'products' => $items->toArray(),
            'items_count' => $itemsCount,
            'total_price' => $totalPrice,
            'status' => 'pending',
            'payment_method' => $paymentMethod,
            'delivery_method' => $data['delivery_method'],
            'delivery_is_private_house' => (bool) ($address['is_private_house'] ?? false),
            'delivery_city' => $address['city'] ?? 'Челябинск',
            'delivery_street' => $address['street'] ?? null,
            'delivery_house' => $address['house'] ?? null,
            'delivery_apartment' => $address['apartment'] ?? null,
            'delivery_entrance' => $address['entrance'] ?? null,
            'delivery_comment' => $address['comment'] ?? null,
            'comment' => $data['comment'] ?? null,
            'agreement' => (bool) $data['agreement'],
        ]);

        // Если оплата картой, инициируем платеж


        if ($paymentMethod === 'card') {
            try {
                $paymentData = $this->tinkoffService->initPayment([
                    'amount' => $totalPrice,
                    'order_id' => $order->number,
                    'description' => "Оплата заказа №{$order->number}",
                    'phone' => $data['phone'],
                    'email' => $data['email'],
                    'success_url' => route('payment.success', ['OrderId' => $order->number]),
                    'fail_url' => route('payment.fail', ['OrderId' => $order->number]),
                ]);

                // Сохраняем данные платежа
                $order->update([
                    'payment_id' => $paymentData['PaymentId'] ?? null,
                    'payment_status' => $paymentData['Status'] ?? null,
                    'payment_url' => $paymentData['PaymentURL'] ?? null,
                    'payment_data' => $paymentData,
                ]);

                dd($paymentData);
                if (!empty($paymentData['PaymentURL'])) {
                    return redirect()
                    ->route('orders.show', ['order' => $order->uuid])
                    ->with([
                        'success' => 'Заказ успешно оформлен! Мы свяжемся с вами в ближайшее время.',
                        'payment_url' => $paymentData['PaymentURL'],
                    ]);
                }
            } catch (\Exception $e) {
                \Log::error('Tinkoff payment init error', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                ]);

                dd($e);
                return redirect()
                    ->route('orders.show', ['order' => $order->uuid])
                    ->with('error', 'Ошибка инициализации платежа. Мы свяжемся с вами для уточнения деталей.');
            }
        }

        return redirect()
            ->route('orders.show', ['order' => $order->uuid])
            ->with([
                'success' => 'Заказ успешно оформлен! Мы свяжемся с вами в ближайшее время.',
            ]);
    }
}

