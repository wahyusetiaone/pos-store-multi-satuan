<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    protected $fillable = [
        'purchase_id',
        'product_id',
        'product_unit_id',
        'quantity',
        'price',
        'buy_price',
        'subtotal',
        'ppn',
    ];

    protected $casts = [
        'price' => 'integer',
        'buy_price' => 'integer',
        'subtotal' => 'integer'
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
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
