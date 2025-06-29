<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\AccountReceivable;
use App\Models\PaymentARHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentARHistoryController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,transfer',
            'notes' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            $customer = Customer::findOrFail($request->customer_id);
            $remainingPayment = $request->amount;

            // Get unpaid receivables ordered by oldest first
            $receivables = AccountReceivable::where('customer_id', $customer->id)
                ->where('status', 'pending')
                ->orderBy('created_at', 'asc')
                ->get();

            foreach ($receivables as $receivable) {
                if ($remainingPayment <= 0) break;

                $paymentForThisReceivable = min($remainingPayment, $receivable->pending_payment);

                // Create payment history record
                PaymentARHistory::create([
                    'customer_id' => $customer->id,
                    'accounts_receivable_id' => $receivable->id,
                    'amount' => $paymentForThisReceivable,
                    'payment_method' => $request->payment_method,
                    'notes' => $request->notes,
                    'user_id' => auth()->id()
                ]);

                // Update receivable
                $newPendingPayment = $receivable->pending_payment - $paymentForThisReceivable;
                $receivable->update([
                    'pending_payment' => $newPendingPayment,
                    'status' => $newPendingPayment <= 0 ? 'paid' : 'pending'
                ]);

                $remainingPayment -= $paymentForThisReceivable;
            }

            // Update customer's total debt
            $customer->debt_total -= $request->amount;
            $customer->save();

            DB::commit();

            return response()->json([
                'message' => 'Pembayaran berhasil disimpan',
                'remaining_debt' => $customer->fresh()->debt_total
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getHistory(AccountReceivable $accountReceivable)
    {
        $histories = $accountReceivable->paymentHistories()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($history) {
                return [
                    'date' => $history->created_at->format('d/m/Y H:i'),
                    'amount' => number_format($history->amount, 0, ',', '.'),
                    'payment_method' => $history->payment_method === 'cash' ? 'Tunai' : 'Transfer',
                    'notes' => $history->notes ?? '-',
                    'user' => $history->user->name
                ];
            });

        return response()->json($histories);
    }
}
