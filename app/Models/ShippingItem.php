<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingItem extends Model
{
    protected $fillable = [
        'shipping_id',
        'product_id',
        'product_unit_id',
        'quantity',
        'qty_received',
        'note',
        'price',
        'buy_price',
        'subtotal',
        'ppn',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'buy_price' => 'decimal:2',
        'subtotal' => 'decimal:2'
    ];

    public function shipping()
    {
        return $this->belongsTo(Shipping::class);
    }

    public function productUnit()
    {
        return $this->belongsTo(ProductUnit::class, 'product_unit_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
