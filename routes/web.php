<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ShippingController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'store.access'])->group(function () {
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/profile/change-password', [ProfileController::class, 'showChangePasswordForm'])->name('profile.change-password');
    Route::post('/profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.update-password');
    Route::delete('/profile/avatar', [ProfileController::class, 'deleteAvatar'])->name('profile.delete-avatar');

    // Dashboard routes
    Route::get('/', [DashboardController::class, 'index'])->name('index');
    Route::get('/pos', [DashboardController::class, 'pos'])->name('pos');

    // Resource routes
    Route::resource('categories', CategoryController::class);
    Route::resource('products', ProductController::class);
    Route::resource('customers', CustomerController::class);
    Route::get('/sales/export', [SaleController::class, 'export'])->name('sales.export');
    Route::resource('sales', SaleController::class);
    Route::resource('purchases', PurchaseController::class);
    Route::patch('purchases/{purchase}/status', [PurchaseController::class, 'updateStatus'])->name('purchases.update-status');
    Route::resource('shippings', ShippingController::class)->parameters([
        'shippings' => 'shipping'
    ])->where([
        'shipping' => '[0-9]+'
    ]);
    Route::get('/shippings/search', [ShippingController::class, 'search'])->name('shippings.search');
    Route::post('/shippings/{shipping}/status', [ShippingController::class, 'updateStatus'])->name('shippings.update-status');
    Route::post('/shippings/{shipping}/accepter', [ShippingController::class, 'accepter'])->name('shippings.accepter')->where('shipping', '[0-9]+');
    Route::get('/shippings/{shipping}/surat-jalan', [ShippingController::class, 'suratJalan'])
        ->name('shippings.surat-jalan')->where('shipping', '[0-9]+');
    Route::get('/shippings/{shipping}/items/{item}/barcode', [ShippingController::class, 'generateBarcodePdf'])->name('shippings.barcode')->where('shipping', '[0-9]+');
    Route::resource('users', UsersController::class);
    Route::resource('finances', FinanceController::class);
    Route::get('/finances/{finance}/export', [FinanceController::class, 'export'])->name('finances.export');
    Route::get('/export-finances/export-selected', [FinanceController::class, 'exportSelected'])->name('finances.export-selected');
    Route::resource('stores', StoreController::class);

    Route::get('/gallery/images', [GalleryController::class, 'images'])->name('gallery.images');
    Route::get('/gallery/search', [GalleryController::class, 'search'])->name('gallery.search');
    Route::resource('gallery', GalleryController::class);

    // Store user management routes
    Route::get('/stores/{store}/users', [StoreController::class, 'users'])->name('stores.users');
    Route::post('/stores/{store}/users', [StoreController::class, 'assignUser'])->name('stores.assign-user');
    Route::delete('/stores/{store}/users/{user}', [StoreController::class, 'removeUser'])->name('stores.remove-user');



    // Settings routes
    Route::get('/settings/company', [SettingsController::class, 'company'])->name('settings.company');
    Route::get('/settings/currencies', [SettingsController::class, 'currencies'])->name('settings.currencies');
    Route::get('/settings/language', [SettingsController::class, 'language'])->name('settings.language');
    Route::get('/settings/notification', [SettingsController::class, 'notification'])->name('settings.notification');
    Route::get('/settings/notification-alert', [SettingsController::class, 'notificationAlert'])->name('settings.notification-alert');
    Route::get('/settings/payment-gateway', [SettingsController::class, 'paymentGateway'])->name('settings.payment-gateway');
    Route::get('/settings/theme', [SettingsController::class, 'theme'])->name('settings.theme');

    // Search routes
    Route::get('/search/customer-search', [CustomerController::class, 'search'])->name('customers.search');

    // Debt (Hutang) routes
    Route::get('/debts', [\App\Http\Controllers\DebtController::class, 'index'])->name('debts.index');

    // API route untuk categories by store
    Route::get('/api/categories', function (Request $request) {
        $categories = App\Models\Category::where('store_id', $request->store_id)->get();
        return response()->json($categories);
    })->name('api.categories');

    // Gallery routes
    Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery.index');
    Route::post('/gallery', [GalleryController::class, 'store'])->name('gallery.store');
    Route::delete('/gallery/{image}', [GalleryController::class, 'destroy'])->name('gallery.destroy');

    // Voucher routes
    Route::get('/api/vouchers/check', [App\Http\Controllers\VoucherController::class, 'check'])->name('vouchers.check');
    Route::resource('vouchers', VoucherController::class);

    // Payment AR History routes
    Route::post('/payment-ar-histories', [App\Http\Controllers\PaymentARHistoryController::class, 'store'])->name('payment-ar-histories.store');
    Route::get('/payment-ar-histories/{accountReceivable}', [App\Http\Controllers\PaymentARHistoryController::class, 'getHistory'])->name('payment-ar-histories.history');

    // Unit routes
    Route::resource('units', UnitController::class);
    Route::get('product-units', [\App\Http\Controllers\UnitController::class, 'productUnits']);
    Route::resource('product-variants', App\Http\Controllers\ProductVariantController::class);
    Route::resource('suppliers', SupplierController::class);

    // API route for suppliers by store
    Route::get('/api/suppliers', [SupplierController::class, 'apiByStore'])->name('api.suppliers');

    // API get product by id (for variant create warning)
    Route::get('/api/products/get', [ProductController::class, 'getProduct'])->name('api.products.get');

    // API get product variants by product id (for purchase modal)
    Route::get('/api/products/variants', [ProductController::class, 'getVariants'])->name('api.products.variants');

    // API untuk varian + unit produk (untuk modal pembelian)
    Route::get('/api/products/variants-with-units', [ProductController::class, 'getVariantsWithUnits'])->name('products.variants-with-units');
});

// Store Selection Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/store/select', [StoreController::class, 'select'])->name('store.select');
    Route::post('/store/switch', [StoreController::class, 'switch'])->name('store.switch');

    // Product routes
    Route::get('download-template-products', [ProductController::class, 'downloadTemplate'])->name('products.template');
    Route::get('/products/{product}/barcode', [ProductController::class, 'generateBarcodePdf'])->name('products.barcode');
    Route::get('/download/barcode/multiple', [ProductController::class, 'generateMultipleBarcodePdf'])->name('products.barcode.multiple');
    Route::post('/products/import', [ProductController::class, 'import'])->name('products.import');

    Route::get('/cod', [App\Http\Controllers\CodController::class, 'index'])->name('cod.index');

});

// Shop routes
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/shop/{product}', [ShopController::class, 'show'])->name('shop.show');


require __DIR__.'/auth.php';
