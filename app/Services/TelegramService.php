<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    private string $token;
    private array $admins;

    public function __construct()
    {
        $this->token  = config('services.telegram.token');
        $this->admins = config('services.telegram.admins') ?? [];
    }

    /**
     * Отправить сообщение одному пользователю
     */
    public function sendToChat(string $chatId, string $message): bool
    {
        if (!$this->token) {
            Log::error('Telegram: token missing');
            return false;
        }

        $response = Http::post("https://api.telegram.org/bot{$this->token}/sendMessage", [
            'chat_id'    => $chatId,
            'text'       => $message,
            'parse_mode' => 'HTML',
        ]);

        if (!$response->ok()) {
            Log::error('Telegram send error', [
                'chat_id' => $chatId,
                'message' => $message,
                'response' => $response->body(),
            ]);
            return false;
        }

        return true;
    }

    /**
     * Отправить сообщение всем администраторам
     */
    public function sendToAdmins(string $message): void
    {
        foreach ($this->admins as $adminChatId) {
            $this->sendToChat(trim($adminChatId), $message);
        }
    }

    /**
     * Удобный метод: отправить лог/ошибку
     */
    public function error(string $message): void
    {
        $this->sendToAdmins("❗ <b>Ошибка</b>\n\n" . $message);
    }

    /**
     * Уведомление об успешном событии
     */
    public function notify(string $message): void
    {
        $this->sendToAdmins("🔔 <b>Уведомление</b>\n\n" . $message);
    }
    public function notifyOrder(Order $order): void
    {
        $message = "🛒 <b>Новый заказ</b>\n\n";
        $message .= "<b>Номер:</b> {$order->number}\n";
        $message .= "<b>Имя:</b> {$order->customer_first_name}\n";
        $message .= "<b>Фамилия:</b> {$order->customer_last_name}\n";
        $message .= "<b>Телефон:</b> {$order->customer_phone}\n";
        $message .= "<b>Email:</b> {$order->customer_email}\n";
        $message .= "<b>Сумма заказа:</b> " . number_format((float) $order->total_price, 2, '.', ' ') . " ₽\n";
        $message .= "<b>Статус:</b> " . \App\Models\Order::statusLabel($order->status) . "\n";
        $message .= "<b>Оплата:</b> " . \App\Models\Order::paymentLabel($order->payment_method) . "\n";
        $message .= "<b>Доставка:</b> " . \App\Models\Order::deliveryLabel($order->delivery_method) . "\n";
        if ($order->delivery_city || $order->delivery_street || $order->delivery_house) {
            $message .= "<b>Адрес:</b> " .
                ($order->delivery_city ? $order->delivery_city . ', ' : '') .
                ($order->delivery_street ? $order->delivery_street . ', ' : '') .
                ($order->delivery_house ? 'д. ' . $order->delivery_house : '') .
                ($order->delivery_apartment ? ', кв. ' . $order->delivery_apartment : '') .
                ($order->delivery_entrance ? ', подъезд ' . $order->delivery_entrance : '') . "\n";
        }
        if ($order->delivery_comment) {
            $message .= "<b>Комментарий к доставке:</b> {$order->delivery_comment}\n";
        }
        if ($order->comment) {
            $message .= "<b>Комментарий к заказу:</b> {$order->comment}\n";
        }
        $this->sendToAdmins($message);

    }

    public function notifyPayment(Order $order): void
    {
        $message = "💰 <b>Платеж</b>\n\n";
        $message .= "<b>Номер:</b> {$order->number}\n";
        $message .= "<b>Статус:</b> " . \App\Models\Order::paymentLabel($order->payment_method) . "\n";
        $message .= "<b>Сумма:</b> " . number_format((float) $order->total_price, 2, '.', ' ') . " ₽\n";
        $message .= "<b>ID платежа:</b> {$order->payment_id}\n";
        $message .= "<b>Статус платежа:</b> {$order->payment_status}\n";
        $message .= "<b>URL платежа:</b> {$order->payment_url}\n";
        $message .= "<b>Данные платежа:</b> <code>" . json_encode($order->payment_data) . "</code>\n";
        $this->sendToAdmins($message);
    }
}
