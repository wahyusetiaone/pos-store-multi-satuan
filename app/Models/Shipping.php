<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shipping extends Model
{
    protected $fillable = [
        'store_id',
        'user_id',
        'number_shipping',
        'shipping_date',
        'supplier_id', // ubah dari supplier ke supplier_id
        'total',
        'status',
        'ship_date',
        'note'
    ];

    protected $casts = [
        'shipping_date' => 'datetime',
        'ship_date' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(ShippingItem::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
