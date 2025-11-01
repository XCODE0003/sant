<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Support\Arr;
use Inertia\Inertia;
use Inertia\Response;

class OrderTrackingController extends Controller
{
    public function __invoke(Order $order): Response
    {
        $statusTimeline = collect(Order::STATUS_FLOW)
            ->map(function (string $status) use ($order) {
                $position = array_search($status, Order::STATUS_FLOW, true);
                $currentPosition = array_search($order->status, Order::STATUS_FLOW, true);

                return [
                    'value' => $status,
                    'label' => Order::statusLabel($status),
                    'completed' => is_int($currentPosition) && $currentPosition >= $position,
                    'current' => $order->status === $status,
                ];
            })
            ->values()
            ->all();

        return Inertia::render('OrderTracking', [
            'order' => [
                'uuid' => $order->uuid,
                'number' => $order->number,
                'status' => $order->status,
                'status_label' => Order::statusLabel($order->status),
                'payment_method' => $order->payment_method,
                'payment_method_label' => Order::paymentLabel($order->payment_method),
                'delivery_method' => $order->delivery_method,
                'delivery_method_label' => Order::deliveryLabel($order->delivery_method),
                'delivery_is_private_house' => (bool) $order->delivery_is_private_house,
                'delivery' => [
                    'city' => $order->delivery_city,
                    'street' => $order->delivery_street,
                    'house' => $order->delivery_house,
                    'apartment' => $order->delivery_apartment,
                    'entrance' => $order->delivery_entrance,
                    'comment' => $order->delivery_comment,
                ],
                'customer' => [
                    'first_name' => $order->customer_first_name,
                    'last_name' => $order->customer_last_name,
                    'phone' => $order->customer_phone,
                    'email' => $order->customer_email,
                ],
                'items' => collect($order->products ?? [])
                    ->map(function ($item) {
                        $item = is_array($item) ? $item : [];

                        $quantity = (int) Arr::get($item, 'quantity', 1);
                        $unitPrice = (float) Arr::get($item, 'final_price', Arr::get($item, 'price', 0));
                        $total = (float) Arr::get($item, 'total_price', $unitPrice * $quantity);

                        return [
                            'id' => Arr::get($item, 'id'),
                            'title' => Arr::get($item, 'title', 'Товар'),
                            'slug' => Arr::get($item, 'slug'),
                            'image' => Arr::get($item, 'image'),
                            'quantity' => $quantity,
                            'unit_price' => $unitPrice,
                            'total_price' => $total,
                        ];
                    })
                    ->values()
                    ->all(),
                'items_count' => $order->items_count,
                'total_price' => (float) $order->total_price,
                'created_at' => optional($order->created_at)->toIso8601String(),
                'updated_at' => optional($order->updated_at)->toIso8601String(),
            ],
            'statusTimeline' => $statusTimeline,
            'isCancelled' => $order->status === Order::STATUS_CANCELLED,
            'trackingUrl' => route('orders.show', ['order' => $order->uuid]),
        ]);
    }
}

