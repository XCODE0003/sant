<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TinkoffService
{
    private string $terminalKey;
    private string $password;
    private string $baseUrl;

    public function __construct()
    {
        $this->terminalKey = config('services.tinkoff.terminal_key');
        $this->password = config('services.tinkoff.password');

        // Важно: у Tinkoff один URL и для теста, и для прода
        // тест определяется по TerminalKey = TinkoffTest
        $this->baseUrl = 'https://securepay.tinkoff.ru/v2';
    }

    /**
     * Инициализация платежа
     */
    public function initPayment(array $data): array
    {
        $payload = [
            'TerminalKey'     => $this->terminalKey,
            'Amount'          => (int) ($data['amount'] * 100),
            'OrderId'         => $data['order_id'],
            'Description'     => $data['description'] ?? 'Оплата заказа',
            'NotificationURL' => $data['notification_url'] ?? route('payment.notification'),
            'SuccessURL'      => $data['success_url'] ?? route('payment.success'),
            'FailURL'         => $data['fail_url'] ?? route('payment.fail'),
        ];

        if (!empty($data['customer_key'])) {
            $payload['CustomerKey'] = $data['customer_key'];
        }

        if (!empty($data['recurrent'])) {
            $payload['Recurrent'] = 'Y';
        }

        if (!empty($data['data'])) {
            $payload['DATA'] = $data['data'];
        }

        if (!empty($data['receipt'])) {
            $payload['Receipt'] = $data['receipt'];
        }

        // Генерация токена
        $payload['Token'] = $this->generateToken($payload);

        Log::info('Tinkoff Init Request', ['payload' => $payload]);

        $response = Http::post("{$this->baseUrl}/Init", $payload);
        dd($response->json());
        $json = $response->json();

        if (!$response->successful() || empty($json['Success'])) {
            Log::error('Tinkoff Init Failed', [
                'http_status' => $response->status(),
                'response'    => $json,
                'payload'     => $payload,
            ]);

            throw new \Exception($json['Message'] ?? 'Ошибка инициализации платежа');
        }

        return $json;
    }

    /**
     * Отмена платежа
     */
    public function cancelPayment(string $paymentId, ?int $amount = null): array
    {
        $payload = [
            'TerminalKey' => $this->terminalKey,
            'PaymentId'   => $paymentId,
        ];

        if ($amount !== null) {
            $payload['Amount'] = $amount;
        }

        $payload['Token'] = $this->generateToken($payload);

        $response = Http::post("{$this->baseUrl}/Cancel", $payload);
        $json = $response->json();

        if (!$response->successful() || empty($json['Success'])) {
            Log::error('Tinkoff Cancel Failed', [
                'response' => $json,
                'payload'  => $payload,
            ]);

            throw new \Exception($json['Message'] ?? 'Ошибка отмены платежа');
        }

        return $json;
    }

    /**
     * Получить статус платежа
     */
    public function getState(string $paymentId): array
    {
        $payload = [
            'TerminalKey' => $this->terminalKey,
            'PaymentId'   => $paymentId,
        ];

        $payload['Token'] = $this->generateToken($payload);

        $response = Http::post("{$this->baseUrl}/GetState", $payload);

        return $response->json();
    }

    /**
     * Генерация токена — строго по документации Tinkoff
     * Рекурсивно обходит все параметры, сортирует и конкатенирует
     */
    private function generateToken(array $data): string
    {
        unset($data['Token']);
        $data['Password'] = $this->password;

        $result = $this->flattenForToken($data);

        return hash('sha256', $result);
    }

    /**
     * Рекурсивная сортировка и конкатенация значений
     */
    private function flattenForToken(array $data): string
    {
        ksort($data);
        $string = '';

        foreach ($data as $value) {
            if (is_array($value)) {
                $string .= $this->flattenForToken($value);
            } else {
                $string .= (string) $value;
            }
        }

        return $string;
    }

    /**
     * Проверка токена в уведомлении (Callback)
     */
    public function verifyNotificationToken(array $data): bool
    {
        if (!isset($data['Token'])) {
            return false;
        }

        $receivedToken = $data['Token'];
        unset($data['Token'], $data['Receipt'], $data['Data'], $data['DATA']);

        $data['Password'] = $this->password;

        $token = hash('sha256', $this->flattenForToken($data));

        return hash_equals($token, $receivedToken);
    }
}
