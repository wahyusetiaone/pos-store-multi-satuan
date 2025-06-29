<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $fillable = [
        'store_id', // dari migration alter table
        'user_id',
        'purchase_date',
        'supplier',
        'total',
        'status',
        'ship_date',
        'note'
    ];

    protected $casts = [
        'purchase_date' => 'datetime',
        'ship_date' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
