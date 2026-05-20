<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'price',
        'quantity',
        'total',
        'is_customized',
        'customization_data',
        'customization_image',
        'size_id',
        'size_name',
        'shape_option',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'total' => 'decimal:2',
            'is_customized' => 'boolean',
            'customization_data' => 'array',
        ];
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
