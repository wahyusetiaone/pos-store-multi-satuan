<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $fillable = [
        'store_id',
        'user_id',
        'purchase_date',
        'supplier_id',
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
        return $this->hasMany(PurchaseItem::class,'purchase_id');
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
