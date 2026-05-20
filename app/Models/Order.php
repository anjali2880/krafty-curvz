<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'shipping_address',
        'notes',
        'customer_will_send_item',
        'item_description',
        'custom_note',
        'courier_agency_name',
        'tracking_number',
        'parcel_slip_path',
        'parcel_additional_notes',
        'parcel_details_submitted_at',
        'subtotal',
        'total',
        'status',
        'payment_method',
        'payment_status',
    ];

    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            if (empty($order->order_number)) {
                $order->order_number = 'KC-' . strtoupper(Str::random(8));
            }
        });
    }

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'total' => 'decimal:2',
            'customer_will_send_item' => 'boolean',
            'parcel_details_submitted_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getIsPaidAttribute(): bool
    {
        return $this->payment_status === 'paid';
    }

    public static function generateOrderNumber(): string
    {
        return 'KC-' . strtoupper(Str::random(8));
    }
}
