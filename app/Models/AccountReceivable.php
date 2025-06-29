<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountReceivable extends Model
{
    protected $table = 'accounts_receivables';
    protected $fillable = [
        'customer_id',
        'sale_id',
        'pending_payment',
        'debt_total',
        'status'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function paymentHistories()
    {
        return $this->hasMany(PaymentARHistory::class, 'accounts_receivable_id');
    }
}
