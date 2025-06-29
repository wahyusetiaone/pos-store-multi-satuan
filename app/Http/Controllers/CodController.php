<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Store;

class CodController extends Controller
{
    public function index(Request $request)
    {
        $query = Sale::with(['customer', 'user', 'store'])
            ->where('order_type', 'cod');

        // Filter by store
        if (auth()->user()->hasGlobalAccess()) {
            if ($request->filled('store_id')) {
                $query->where('store_id', $request->store_id);
            }
            // Get stores for filter dropdown
            $stores = Store::where('is_active', true)->get();
        } else {
            $query->where('store_id', auth()->user()->current_store_id);
            $stores = collect(); // empty collection for non-global users
        }

        // Apply date filters
        switch ($request->filter) {
            case 'today':
                $query->whereDate('sale_date', now());
                break;
            case 'month':
                $query->whereYear('sale_date', now()->year)
                      ->whereMonth('sale_date', now()->month);
                break;
            case 'year':
                $query->whereYear('sale_date', now()->year);
                break;
            default:
                if ($request->filled(['start_date', 'end_date'])) {
                    $query->whereBetween('sale_date', [
                        $request->start_date . ' 00:00:00',
                        $request->end_date . ' 23:59:59'
                    ]);
                }
                break;
        }

        $sales = $query->orderByDesc('id')->paginate(15)->withQueryString();
        return view('cod.index', compact('sales', 'stores'));
    }
}
