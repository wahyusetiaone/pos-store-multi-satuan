<?php

namespace App\Observers;

use App\Models\AccountReceivable;
use App\Models\Customer;

class AccountReceivableObserver
{
    public function created(AccountReceivable $receivable)
    {
        // Update customer's debt_total when new receivable is created
        $customer = Customer::find($receivable->customer_id);
        $customer->increment('debt_total', $receivable->pending_payment);
    }

    public function updated(AccountReceivable $receivable)
    {
        // If pending_payment changed, adjust customer's debt_total
        if ($receivable->isDirty('pending_payment')) {
            $customer = Customer::find($receivable->customer_id);
            $difference = $receivable->pending_payment - $receivable->getOriginal('pending_payment');
            $customer->increment('debt_total', $difference);
        }
    }
}
