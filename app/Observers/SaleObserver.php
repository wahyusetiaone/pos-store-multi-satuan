<?php

namespace App\Observers;

use App\Models\Sale;
use App\Models\Finance;
use App\Models\AccountReceivable;
use Carbon\Carbon;

class SaleObserver
{
    /**
     * Handle the Sale "created" event.
     */
    public function created(Sale $sale): void
    {
        // Get today's date without time
        $today = Carbon::parse($sale->sale_date)->startOfDay();

        // Check if there's already a daily record for this store
        $dailyFinance = Finance::where('store_id', $sale->store_id)
            ->where('type', 'income')
            ->where('category', 'daily_sale')
            ->whereDate('date', $today)
            ->first();

        $saleAmount = $sale->total - $sale->discount;

        if ($dailyFinance) {
            // Update existing record
            $dailyFinance->update([
                'amount' => $dailyFinance->amount + $saleAmount
            ]);
        } else {
            // Create new daily record
            Finance::create([
                'store_id' => $sale->store_id,
                'date' => $today,
                'type' => 'income',
                'category' => 'daily_sale',
                'amount' => $saleAmount,
                'description' => 'Rekap Penjualan Tanggal ' . $today->format('d/m/Y'),
                'user_id' => $sale->user_id
            ]);
        }

        // Create individual sale record
        Finance::create([
            'store_id' => $sale->store_id,
            'date' => $sale->sale_date,
            'type' => 'income',
            'category' => 'sale',
            'amount' => $saleAmount,
            'description' => 'Penjualan #' . $sale->id,
            'user_id' => $sale->user_id
        ]);

        // If payment is less than grand total and customer exists, create receivable record
        if ($sale->paid < $sale->grand_total && $sale->customer_id) {
            $pendingPayment = $sale->grand_total - $sale->paid;
            AccountReceivable::create([
                'customer_id' => $sale->customer_id,
                'sale_id' => $sale->id,
                'pending_payment' => $pendingPayment,
                'debt_total' => $pendingPayment,
                'status' => 'pending'
            ]);
        }
    }

    /**
     * Handle the Sale "updated" event.
     */
    public function updated(Sale $sale): void
    {
        // If there's a change in payment amount and customer exists, update receivable record
        if ($sale->isDirty('paid') && $sale->customer_id) {
            $receivable = AccountReceivable::where('sale_id', $sale->id)->first();

            if ($receivable) {
                $pendingPayment = $sale->grand_total - $sale->paid;

                if ($pendingPayment <= 0) {
                    $receivable->update([
                        'status' => 'paid',
                        'pending_payment' => 0,
                        'debt_total' => $receivable->debt_total
                    ]);
                } else {
                    $receivable->update([
                        'pending_payment' => $pendingPayment,
                        'debt_total' => $receivable->debt_total
                    ]);
                }
            }
        }
    }
}
