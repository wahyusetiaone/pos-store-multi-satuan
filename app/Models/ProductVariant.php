<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVariant extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'product_id',
        'product_unit_id',
        'name',
        'price',
        'qty',
        'status',
    ];

    protected $casts = [
        'price' => 'integer'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function productUnit()
    {
        return $this->belongsTo(ProductUnit::class);
    }

    public function getStockAttribute()
    {
        $product = $this->product;
        $productUnit = $this->productUnit;
        if (!$product || !$productUnit) {
            return 0;
        }
        // Ambil conversion factor dari tabel product_unit
        $pivot = ProductUnit::find($this->product_unit_id);
        $conversion = $pivot ? $pivot->conversion_factor : 1;
        if ($conversion <= 0 || $this->qty <= 0) {
            return 0;
        }
        // Rumus: (stock / conversion_factor) / qty, dibulatkan ke bawah
        return (int) floor(($product->stock / $conversion) / $this->qty);
    }
}
