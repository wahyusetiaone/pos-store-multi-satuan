<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Customer;
use App\Models\Finance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $lastMonth = Carbon::now()->subMonth();

        // --- Inisialisasi Base Query Builders ---
        // Ini adalah base query builder untuk setiap model.
        // Kita akan meng-clone objek-objek ini setiap kali kita perlu
        // menambahkan filter spesifik di setiap perhitungan.
        $baseSaleQuery = Sale::query();
        $basePurchaseQuery = Purchase::query();
        $baseFinanceQuery = Finance::query();

        // --- Terapkan Filter Akses Data (store_id) ---
        // Filter ini diterapkan ke base query builder jika user tidak memiliki global access.
        // Filter ini akan selalu ada di setiap clone selanjutnya.
        if (!auth()->user()->hasGlobalAccess()) {
            $storeId = auth()->user()->current_store_id;
            $baseSaleQuery->where('store_id', $storeId);
            $basePurchaseQuery->where('store_id', $storeId);
            $baseFinanceQuery->where('store_id', $storeId);
        }
        // --- End of Access Data Filtering ---

        // --- Sales Statistics & Growth ---
        // Setiap perhitungan dimulai dengan meng-clone $baseSaleQuery
        $todaySales = (clone $baseSaleQuery)->whereDate('sale_date', $today)->sum('total');
        $monthSales = (clone $baseSaleQuery)->whereMonth('sale_date', $today->month)->sum('total');
        $lastMonthSales = (clone $baseSaleQuery)->whereMonth('sale_date', $lastMonth->month)->sum('total');
        $salesGrowth = $lastMonthSales > 0 ? (($monthSales - $lastMonthSales) / $lastMonthSales * 100) : 0;

        // --- Purchase Statistics & Growth ---
        // Setiap perhitungan dimulai dengan meng-clone $basePurchaseQuery
        $totalPurchasesThisMonth = (clone $basePurchaseQuery)->whereMonth('purchase_date', $today->month)->sum('total');
        $totalPurchasesLastMonth = (clone $basePurchaseQuery)->whereMonth('purchase_date', $lastMonth->month)->sum('total');
        $purchaseGrowth = $totalPurchasesLastMonth > 0 ? (($totalPurchasesThisMonth - $totalPurchasesLastMonth) / $totalPurchasesLastMonth * 100) : 0;

        // --- Monthly Income & Expense Data for Chart ---
        $monthlyIncomes = [];
        $monthlyExpenses = [];
        for ($i = 1; $i <= 12; $i++) {
            // Dalam loop, setiap query income/expense dimulai dengan meng-clone $baseFinanceQuery
            $monthlyIncomes[] = (clone $baseFinanceQuery)
                ->where('type', 'income')
                ->whereYear('date', $today->year)
                ->whereMonth('date', $i)
                ->sum('amount');

            $monthlyExpenses[] = (clone $baseFinanceQuery)
                ->where('type', 'expense')
                ->whereYear('date', $today->year)
                ->whereMonth('date', $i)
                ->sum('amount');
        }

        // --- Weekly Sales & Purchases for Chart ---
        $weeklySales = [];
        $weeklyPurchases = [];
        for ($i = 0; $i < 7; $i++) {
            $date = Carbon::now()->startOfWeek()->addDays($i);
            // Setiap perhitungan dimulai dengan meng-clone base query
            $weeklySales[] = (clone $baseSaleQuery)->whereDate('sale_date', $date)->sum('total');
            $weeklyPurchases[] = (clone $basePurchaseQuery)->whereDate('purchase_date', $date)->sum('total');
        }

        // --- Financial Statistics & Growth ---
        // Setiap perhitungan dimulai dengan meng-clone $baseFinanceQuery
        $incomeThisMonth = (clone $baseFinanceQuery)
            ->where('type', 'income')
            ->whereMonth('date', $today->month)
            ->sum('amount');
        $incomeLastMonth = (clone $baseFinanceQuery)
            ->where('type', 'income')
            ->whereMonth('date', $lastMonth->month)
            ->sum('amount');
        $incomeGrowth = $incomeLastMonth > 0 ? (($incomeThisMonth - $incomeLastMonth) / $incomeLastMonth * 100) : 0;

        $expensesThisMonth = (clone $baseFinanceQuery)
            ->where('type', 'expense')
            ->whereMonth('date', $today->month)
            ->sum('amount');
        $expensesLastMonth = (clone $baseFinanceQuery)
            ->where('type', 'expense')
            ->whereMonth('date', $lastMonth->month)
            ->sum('amount');
        $expenseGrowth = $expensesLastMonth > 0 ? (($expensesThisMonth - $expensesLastMonth) / $expensesLastMonth * 100) : 0;

        // --- Overall Report Percentages ---
        // Setiap perhitungan dimulai dengan meng-clone base query
        $totalPurchases = (clone $basePurchaseQuery)->whereMonth('purchase_date', $today->month)->sum('total');
        $totalSales = (clone $baseSaleQuery)->whereMonth('sale_date', $today->month)->sum('total');
        $totalExpenses = (clone $baseFinanceQuery)
            ->where('type', 'expense')
            ->whereMonth('date', $today->month)
            ->sum('amount');
        $grossProfit = $totalSales - $totalPurchases - $totalExpenses;

        $total = $totalPurchases + $totalSales + $totalExpenses + max(0, $grossProfit);
        $totalPurchases = $total > 0 ? ($totalPurchases / $total * 100) : 0;
        $totalSales = $total > 0 ? ($totalSales / $total * 100) : 0;
        $totalExpenses = $total > 0 ? ($totalExpenses / $total * 100) : 0;
        $grossProfit = $total > 0 ? (max(0, $grossProfit) / $total * 100) : 0;

        // --- Recent Sales ---
        // Dimulai dengan meng-clone $baseSaleQuery
        $recentSales = (clone $baseSaleQuery)
            ->with(['customer']) // Pastikan relasi 'customer' ada di model Sale Anda
            ->orderBy('sale_date', 'desc')
            ->take(5)
            ->get();

        return view('dashboard.index', compact(
            'todaySales',
            'monthSales',
            'salesGrowth',
            'totalPurchasesThisMonth',
            'purchaseGrowth',
            'incomeThisMonth',
            'incomeGrowth',
            'expensesThisMonth',
            'expenseGrowth',
            'monthlyIncomes',
            'monthlyExpenses',
            'weeklySales',
            'weeklyPurchases',
            'totalPurchases',
            'totalSales',
            'totalExpenses',
            'grossProfit',
            'recentSales'
        ));
    }

    public function pos()
    {
        // Ambil data ProductVariant beserta relasi produk, kategori, gambar, unit
        $variantsQuery = \App\Models\ProductVariant::with([
            'product.category',
            'product.images',
            'product',
            'productUnit.unit',
        ]);
        $categoriesQuery = Category::query();

        // Filter by store jika user tidak punya global access
        if (!auth()->user()->hasGlobalAccess()) {
            $storeId = auth()->user()->current_store_id;
            $variantsQuery->whereHas('product', function($q) use ($storeId) {
                $q->where('store_id', $storeId);
            });
            $categoriesQuery->where('store_id', $storeId);
        }

        $variants = $variantsQuery->get();
        $categories = $categoriesQuery->get();

        return view('dashboard.pos', compact('variants', 'categories'));
    }
}
