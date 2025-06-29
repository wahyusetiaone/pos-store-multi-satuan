<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ProductUnit extends Pivot
{
    protected $table = 'product_unit';
    protected $fillable = [
        'product_id',
        'unit_id',
        'conversion_factor',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function getConversionFactorCashAttribute()
    {
        // Jika bilangan bulat, tampilkan tanpa koma, jika desimal, tampilkan sesuai aslinya
        return fmod($this->conversion_factor, 1) == 0
            ? number_format($this->conversion_factor, 0, '', '')
            : rtrim(rtrim(number_format($this->conversion_factor, 4, '.', ''), '0'), '.');
    }
}
