<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentARHistory extends Model
{
    protected $table = 'payment_ar_histories';

    protected $fillable = [
        'customer_id',
        'accounts_receivable_id',
        'amount',
        'payment_method',
        'notes',
        'user_id'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function accountReceivable()
    {
        return $this->belongsTo(AccountReceivable::class, 'accounts_receivable_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
