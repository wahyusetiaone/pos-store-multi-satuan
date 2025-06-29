<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    protected $fillable = [
        'sale_id',
        'product_id',
        'quantity',
        'price',
        'discount',
        'subtotal',
        'unit_profit_loss'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($saleItem) {
            $product = $saleItem->product;
            $profit = ($saleItem->price - $product->buy_price) * $saleItem->quantity - $saleItem->discount;
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
}
