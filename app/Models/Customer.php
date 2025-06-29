<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id', // dari migration alter table
        'name',
        'email',
        'phone',
        'address',
        'notes'
    ];

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function accountReceivables(): HasMany
    {
        return $this->hasMany(AccountReceivable::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
