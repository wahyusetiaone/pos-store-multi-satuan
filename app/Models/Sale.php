<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    protected $fillable = [
        'store_id',
        'customer_id',
        'customer_name',
        'sale_date',
        'payment_method',
        'order_type',
        'discount',
        'tax',
        'fixed_discount',
        'voucher_code',
        'voucher_discount',
        'total',
        'grand_total',
        'paid',
        'user_id'
    ];

    protected $casts = [
        'sale_date' => 'datetime',
        'discount' => 'float',
        'tax' => 'float',
        'fixed_discount' => 'float',
        'voucher_discount' => 'float',
        'total' => 'float',
        'grand_total' => 'float',
        'paid' => 'float'
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function accountReceivable(): HasMany
    {
        return $this->hasMany(AccountReceivable::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Calculate if sale has pending payment
    public function hasPendingPayment(): bool
    {
        return $this->paid < $this->grand_total;
    }

    // Get pending payment amount
    public function getPendingAmount(): float
    {
        return max(0, $this->grand_total - $this->paid);
    }
}
