<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class DebtController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::whereHas('accountReceivables', function($q) {
            $q->where('pending_payment', '>', 0);
        });

        // Filter by store if user doesn't have global access
        if (!auth()->user()->hasGlobalAccess()) {
            $query->where('store_id', auth()->user()->current_store_id);
        }

        // Search by name, phone, or email if provided
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $customers = $query->paginate(15);
        return view('debts.index', compact('customers'));
    }
}

