<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ShippingController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ComponentpageController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\ShopController;
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
    Route::get('/dashboard/index2', [DashboardController::class, 'index2'])->name('index2');
    Route::get('/dashboard/index3', [DashboardController::class, 'index3'])->name('index3');
    Route::get('/dashboard/index4', [DashboardController::class, 'index4'])->name('index4');
    Route::get('/dashboard/index5', [DashboardController::class, 'index5'])->name('index5');
    Route::get('/dashboard/index6', [DashboardController::class, 'index6'])->name('index6');
    Route::get('/dashboard/index7', [DashboardController::class, 'index7'])->name('index7');
    Route::get('/dashboard/index8', [DashboardController::class, 'index8'])->name('index8');
    Route::get('/dashboard/index9', [DashboardController::class, 'index9'])->name('index9');
    Route::get('/dashboard/index10', [DashboardController::class, 'index10'])->name('index10');
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

    // Component page routes
    Route::get('/components/alert', [ComponentpageController::class, 'alert'])->name('components.alert');
    Route::get('/components/avatar', [ComponentpageController::class, 'avatar'])->name('components.avatar');
    Route::get('/components/badges', [ComponentpageController::class, 'badges'])->name('components.badges');
    Route::get('/components/button', [ComponentpageController::class, 'button'])->name('components.button');
    Route::get('/components/calendar', [ComponentpageController::class, 'calendar'])->name('components.calendar');
    Route::get('/components/card', [ComponentpageController::class, 'card'])->name('components.card');
    Route::get('/components/carousel', [ComponentpageController::class, 'carousel'])->name('components.carousel');
    Route::get('/components/colors', [ComponentpageController::class, 'colors'])->name('components.colors');
    Route::get('/components/dropdown', [ComponentpageController::class, 'dropdown'])->name('components.dropdown');
    Route::get('/components/image-upload', [ComponentpageController::class, 'imageUpload'])->name('components.image-upload');
    Route::get('/components/list', [ComponentpageController::class, 'list'])->name('components.list');
    Route::get('/components/pagination', [ComponentpageController::class, 'pagination'])->name('components.pagination');
    Route::get('/components/progress', [ComponentpageController::class, 'progress'])->name('components.progress');
    Route::get('/components/radio', [ComponentpageController::class, 'radio'])->name('components.radio');
    Route::get('/components/star-rating', [ComponentpageController::class, 'starRating'])->name('components.star-rating');
    Route::get('/components/switch', [ComponentpageController::class, 'switch'])->name('components.switch');
    Route::get('/components/tabs', [ComponentpageController::class, 'tabs'])->name('components.tabs');
    Route::get('/components/tags', [ComponentpageController::class, 'tags'])->name('components.tags');
    Route::get('/components/tooltip', [ComponentpageController::class, 'tooltip'])->name('components.tooltip');
    Route::get('/components/typography', [ComponentpageController::class, 'typography'])->name('components.typography');
    Route::get('/components/videos', [ComponentpageController::class, 'videos'])->name('components.videos');

    // Home routes
    Route::get('/calendar', [HomeController::class, 'calendar'])->name('calendar');
    Route::get('/chat-message', [HomeController::class, 'chatMessage'])->name('chat-message');
    Route::get('/chat-empty', [HomeController::class, 'chatempty'])->name('chat-empty');
    Route::get('/view-details', [HomeController::class, 'veiwDetails'])->name('view-details');
    Route::get('/email', [HomeController::class, 'email'])->name('email');
    Route::get('/error', [HomeController::class, 'error1'])->name('error');
    Route::get('/faq', [HomeController::class, 'faq'])->name('faq');
    Route::get('/gallery', [HomeController::class, 'gallery'])->name('gallery');
    Route::get('/kanban', [HomeController::class, 'kanban'])->name('kanban');
    Route::get('/pricing', [HomeController::class, 'pricing'])->name('pricing');
    Route::get('/terms-condition', [HomeController::class, 'termsCondition'])->name('terms-condition');
    Route::get('/widgets', [HomeController::class, 'widgets'])->name('widgets');
    Route::get('/chat-profile', [HomeController::class, 'chatProfile'])->name('chat-profile');
    Route::get('/blank-page', [HomeController::class, 'blankPage'])->name('blank-page');
    Route::get('/coming-soon', [HomeController::class, 'comingSoon'])->name('coming-soon');
    Route::get('/starred', [HomeController::class, 'starred'])->name('starred');
    Route::get('/testimonials', [HomeController::class, 'testimonials'])->name('testimonials');
    Route::get('/maintenance', [HomeController::class, 'maintenance'])->name('maintenance');

    // Invoice routes
    Route::get('/blog/add', [\App\Http\Controllers\BlogController::class, 'addBlog'])->name('addBlog');
    Route::get('/blog', [\App\Http\Controllers\BlogController::class, 'blog'])->name('blog');
    Route::get('/blog/details', [\App\Http\Controllers\BlogController::class, 'blogDetails'])->name('blogDetails');

    // Invoice routes
    Route::get('/invoice/add', [InvoiceController::class, 'invoiceAdd'])->name('invoice.add');
    Route::get('/invoice/edit', [InvoiceController::class, 'invoiceEdit'])->name('invoice.edit');
    Route::get('/invoice/list', [InvoiceController::class, 'invoiceList'])->name('invoice.list');
    Route::get('/invoice/preview', [InvoiceController::class, 'invoicePreview'])->name('invoice.preview');

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

    // Payment AR History routes
    Route::post('/payment-ar-histories', [App\Http\Controllers\PaymentARHistoryController::class, 'store'])->name('payment-ar-histories.store');
    Route::get('/payment-ar-histories/{accountReceivable}', [App\Http\Controllers\PaymentARHistoryController::class, 'getHistory'])->name('payment-ar-histories.history');
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

