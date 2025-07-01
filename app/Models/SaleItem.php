<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    protected $fillable = [
        'sale_id',
        'variant_id',
        'product_id',
        'quantity',
        'quantity_conversion',
        'price',
        'discount',
        'subtotal',
        'unit_profit_loss'
    ];

    protected $casts = [
        'price' => 'integer',
        'discount' => 'integer',
        'subtotal' => 'integer',
        'unit_profit_loss' => 'integer'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($saleItem) {
            $product = $saleItem->product;
            $profit = (($saleItem->price / $saleItem->quantity_conversion) - $product->buy_price) * $saleItem->quantity - $saleItem->discount;
            $saleItem->unit_profit_loss = $profit;
        });
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(\App\Models\ProductVariant::class, 'variant_id');
    }
}
