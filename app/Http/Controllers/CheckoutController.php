<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CheckoutController extends Controller
{
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
            $paymentMethod = 'cash';
        }

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

        return redirect()
            ->route('orders.show', ['order' => $order->uuid])
            ->with([
                'success' => 'Заказ успешно оформлен! Мы свяжемся с вами в ближайшее время.',
            ]);
    }
}

