<?php

namespace App\Http\Controllers;

use App\Models\Finance;
use App\Models\User;
use App\Models\Store;
use Illuminate\Http\Request;
use Carbon\Carbon;

class FinanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Finance::with(['user', 'store']);
        $stores = [];

        // Filter by store if user doesn't have global access
        if (!auth()->user()->hasGlobalAccess()) {
            $query->where('store_id', auth()->user()->current_store_id);
        } else {
            $stores = Store::where('is_active', true)->get();
            // Filter by store_id if selected
            if ($request->filled('store_id')) {
                $query->where('store_id', $request->store_id);
            }
        }

        // Filter by category
        if ($request->filled('category')) {
            if ($request->category !== 'all') {
                $query->where('category', $request->category);
            }
        }

        // Date filtering
        if ($request->filled('filter')) {
            switch ($request->filter) {
                case 'today':
                    $query->whereDate('date', Carbon::today());
                    break;
                case 'month':
                    $query->whereYear('date', Carbon::now()->year)
                          ->whereMonth('date', Carbon::now()->month);
                    break;
                case 'year':
                    $query->whereYear('date', Carbon::now()->year);
                    break;
            }
        } elseif ($request->filled(['start_date', 'end_date'])) {
            $query->whereBetween('date', [
                $request->start_date,
                $request->end_date
            ]);
        }

        $finances = $query->orderByDesc('id')->paginate(15);

        // Maintain filter in pagination
        $finances->appends($request->all());

        return view('finances.index', [
            'finances' => $finances,
            'currentCategory' => $request->category ?? 'all',
            'stores' => $stores,
            'currentStore' => $request->store_id,
            'startDate' => $request->start_date,
            'endDate' => $request->end_date,
            'currentFilter' => $request->filter
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::all();
        $stores = [];
        if (auth()->user()->hasGlobalAccess()) {
            $stores = Store::where('is_active', true)->get();
        }
        return view('finances.create', compact('users', 'stores'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'type' => 'required|in:income,expense',
            'category' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'description' => 'nullable|string',
            'user_id' => 'required|exists:users,id',
            'store_id' => auth()->user()->hasGlobalAccess() ? 'required|exists:stores,id' : 'prohibited'
        ]);

        // Set store_id based on user access
        if (auth()->user()->hasGlobalAccess()) {
            // Store ID from request for global access users
            $storeId = $request->store_id;
        } else {
            // Current store ID for non-global access users
            $storeId = auth()->user()->current_store_id;
        }

        // Add store_id to validated data
        $validated['store_id'] = $storeId;

        Finance::create($validated);
        return redirect()->route('finances.index')->with('success', 'Transaksi keuangan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Finance $finance)
    {
        $finance->load('user');
        return view('finances.show', compact('finance'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Finance $finance)
    {
        $users = User::all();
        return view('finances.edit', compact('finance', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Finance $finance)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'type' => 'required|in:income,expense',
            'category' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'description' => 'nullable|string',
            'user_id' => 'required|exists:users,id',
        ]);
        $finance->update($validated);
        return redirect()->route('finances.index')->with('success', 'Transaksi keuangan berhasil diubah.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Finance $finance)
    {
        $finance->delete();
        return redirect()->route('finances.index')->with('success', 'Transaksi keuangan berhasil dihapus.');
    }

    /**
     * Export the specified resource.
     */
    public function export(Finance $finance)
    {
        // Only allow export for daily_sale category
        if ($finance->category !== 'daily_sale') {
            return back()->with('error', 'Hanya rekap penjualan harian yang bisa diexport.');
        }

        // Get all sales for this store and date
        $sales = \App\Models\Sale::with(['items.product', 'store', 'user', 'customer'])
            ->where('store_id', $finance->store_id)
            ->whereDate('sale_date', $finance->date)
            ->get();

        $date = \Carbon\Carbon::parse($finance->date)->format('d-m-Y');
        $filename = "Laporan_Penjualan_{$finance->store->name}_{$date}.xlsx";

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\SaleExport($sales),
            $filename
        );
    }

    /**
     * Export filtered daily sales.
     */
    public function exportSelected(Request $request)
    {
        $query = Finance::with(['store'])->where('category', 'daily_sale');

        // Filter by store if user doesn't have global access
        if (!auth()->user()->hasGlobalAccess()) {
            $query->where('store_id', auth()->user()->current_store_id);
        } elseif ($request->filled('store_id')) {
            $query->where('store_id', $request->store_id);
        }

        // Date filtering
        if ($request->filled('filter')) {
            switch ($request->filter) {
                case 'today':
                    $query->whereDate('date', Carbon::today());
                    break;
                case 'month':
                    $query->whereYear('date', Carbon::now()->year)
                          ->whereMonth('date', Carbon::now()->month);
                    break;
                case 'year':
                    $query->whereYear('date', Carbon::now()->year);
                    break;
            }
        } elseif ($request->filled(['start_date', 'end_date'])) {
            $query->whereBetween('date', [
                $request->start_date,
                $request->end_date
            ]);
        }

        $finances = $query->get();

        // Get all sales for the filtered finances
        $sales = \App\Models\Sale::with(['items.product', 'store', 'user', 'customer'])
            ->whereIn('store_id', $finances->pluck('store_id'))
            ->whereIn(\DB::raw('DATE(sale_date)'), $finances->pluck('date'))
            ->get();

        $filename = "Laporan_Penjualan_";
        if ($request->filled('store_id')) {
            $store = Store::find($request->store_id);
            $filename .= $store->name . "_";
        }

        if ($request->filled('filter')) {
            $filename .= ucfirst($request->filter);
        } elseif ($request->filled(['start_date', 'end_date'])) {
            $filename .= $request->start_date . "_sd_" . $request->end_date;
        }

        $filename .= ".xlsx";

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\SaleExport($sales),
            $filename
        );
    }
}
