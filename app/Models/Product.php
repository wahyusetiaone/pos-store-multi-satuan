<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'store_id', // dari migration alter table
        'category_id',
        'default_unit_id',
        'name',
        'sku',
        'price',
        'buy_price',
        'stock',
        'description',
        'status'
    ];

    protected $casts = [
        'price' => 'integer',
        'buy_price' => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function defaultUnit()
    {
        return $this->belongsTo(Unit::class, 'default_unit_id');
    }

    public function units()
    {
        return $this->belongsToMany(Unit::class, 'product_unit')
            ->withPivot('conversion_factor')
            ->using(ProductUnit::class)
            ->withTimestamps();
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }
}
