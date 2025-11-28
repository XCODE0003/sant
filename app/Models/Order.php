<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_SHIPPED = 'shipped';
    public const STATUS_DELIVERED = 'delivered';
    public const STATUS_CANCELLED = 'cancelled';

    public const STATUS_LABELS = [
        self::STATUS_PENDING => 'Новый',
        self::STATUS_CONFIRMED => 'Подтверждён',
        self::STATUS_SHIPPED => 'Отправлен',
        self::STATUS_DELIVERED => 'Доставлен',
        self::STATUS_CANCELLED => 'Отменён',
    ];

    public const STATUS_FLOW = [
        self::STATUS_PENDING,
        self::STATUS_CONFIRMED,
        self::STATUS_SHIPPED,
        self::STATUS_DELIVERED,
    ];

    public const PAYMENT_LABELS = [
        'cash' => 'Наличными',
        'card' => 'Банковская карта',
    ];

    public const DELIVERY_LABELS = [
        'courier' => 'Курьер',
        'pickup' => 'Самовывоз',
    ];

    protected $fillable = [
        'uuid',
        'number',
        'customer_first_name',
        'customer_last_name',
        'customer_phone',
        'customer_email',
        'products',
        'items_count',
        'total_price',
        'status',
        'payment_method',
        'delivery_method',
        'delivery_is_private_house',
        'delivery_city',
        'delivery_street',
        'delivery_house',
        'delivery_apartment',
        'delivery_entrance',
        'delivery_comment',
        'comment',
        'agreement',
        'payment_id',
        'payment_status',
        'payment_url',
        'payment_data',
    ];

    protected $casts = [
        'products' => 'array',
        'total_price' => 'decimal:2',
        'delivery_is_private_house' => 'boolean',
        'agreement' => 'boolean',
        'payment_data' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $order): void {
            if (! $order->uuid) {
                $order->uuid = (string) Str::uuid();
            }

            if (! $order->number) {
                $order->number = static::generateNumber();
            }
        });
    }

    public function getCustomerFullNameAttribute(): string
    {
        return trim($this->customer_first_name . ' ' . $this->customer_last_name);
    }

    public static function generateNumber(): string
    {
        return 'ORD-' . now()->format('Ymd') . '-' . Str::upper(Str::random(5));
    }

    public static function statusLabel(string $status): string
    {
        return Arr::get(self::STATUS_LABELS, $status, $status);
    }

    public static function paymentLabel(string $paymentMethod): string
    {
        return Arr::get(self::PAYMENT_LABELS, $paymentMethod, $paymentMethod);
    }

    public static function deliveryLabel(string $deliveryMethod): string
    {
        return Arr::get(self::DELIVERY_LABELS, $deliveryMethod, $deliveryMethod);
    }
}
