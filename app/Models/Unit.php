<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unit extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description'
    ];

    /**
     * Get all sub units for this unit
     */
    public function subUnits(): HasMany
    {
        return $this->hasMany(SubUnit::class);
    }

    /**
     * Get all products using this unit
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * The products that belong to the unit (many-to-many with conversion_factor)
     */
    public function productsMany()
    {
        return $this->belongsToMany(Product::class, 'product_unit')
            ->withPivot('conversion_factor')
            ->using(ProductUnit::class)
            ->withTimestamps();
    }
}
