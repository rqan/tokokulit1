<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\PesananController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\NewsletterController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CmsController;
use App\Http\Controllers\Admin\SalesController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PaymentMethodController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\BoutiqueController;

// ==========================================
// 1. GUEST & AKSES PUBLIK (Tanpa Filter)
// ==========================================
Route::get('/', [CatalogController::class, 'index']);
Route::get('/katalog', [CatalogController::class, 'katalog']);
Route::get('/product/{id}', [CatalogController::class, 'show']);
Route::post('/appointments/book/{product}', [AppointmentController::class, 'store'])->name('appointments.book');

Route::get('/stores', [PageController::class, 'stores'])->name('stores');
Route::get('/boutiques', [BoutiqueController::class, 'index'])->name('boutiques');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');

// Fitur Tambahan
Route::view('/privacy-policy', 'privacy')->name('privacy.policy');
Route::post('/subscribe', [\App\Http\Controllers\SubscriberController::class, 'store'])->name('subscribe.store');

Route::get('/profile', function () {
    if (!Auth::check() && !session('isLoggedIn')) {
        return redirect('/login');
    }
    
    $orders = \App\Models\Order::where('user_id', \Illuminate\Support\Facades\Auth::id())
                ->latest()
                ->take(5)
                ->get();
                
    return view('profile', compact('orders'));
})->name('profile');

// Auth
Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'attemptLogin']);
Route::get('/register', [AuthController::class, 'register']);
Route::post('/register', [AuthController::class, 'attemptRegister']);
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// Invoice Publik (accessible via link)
Route::get('/invoice/{invoiceNumber}', [InvoiceController::class, 'show'])->name('invoice.show');
Route::get('/invoice/{invoiceNumber}/pdf', [InvoiceController::class, 'pdf'])->name('invoice.pdf');

// Rating via Token (accessible tanpa login)
Route::get('/rating/{token}', [RatingController::class, 'showForm'])->name('rating.form');
Route::post('/rating/{token}', [RatingController::class, 'store'])->name('rating.store');

// Midtrans Payment Gateway Webhook Callback
Route::post('/api/midtrans/notification', [\App\Http\Controllers\Api\MidtransCallbackController::class, 'handle'])->name('midtrans.notification');
Route::post('/midtrans/notification', [\App\Http\Controllers\Api\MidtransCallbackController::class, 'handle']);


// ==========================================
// 2. PELANGGAN
// ==========================================
Route::middleware(['role:pelanggan'])->prefix('pelanggan')->group(function () {
    // Cart
    Route::get('cart', [CartController::class, 'index']);
    Route::post('cart/add', [CartController::class, 'add']);
    Route::post('cart/update', [CartController::class, 'update']);
    Route::post('cart/remove', [CartController::class, 'remove']);

    // Checkout
    Route::get('checkout', [CheckoutController::class, 'index']);
    Route::post('checkout/process', [CheckoutController::class, 'process']);

    // Pesanan (Riwayat & Detail)
    Route::get('pesanan', [PesananController::class, 'riwayat']);
    Route::get('pesanan/{id}', [PesananController::class, 'detail']);
    Route::post('pesanan/{id}/upload-bukti', [PesananController::class, 'uploadBukti']);
    Route::post('pesanan/{id}/confirm', [PesananController::class, 'confirmReceived']);

    // Wishlist
    Route::get('wishlist', [\App\Http\Controllers\WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('wishlist', [\App\Http\Controllers\WishlistController::class, 'store'])->name('wishlist.store');
    Route::delete('wishlist/{id}', [\App\Http\Controllers\WishlistController::class, 'destroy'])->name('wishlist.destroy');

    // Address Book
    Route::resource('addresses', \App\Http\Controllers\AddressController::class)->except(['show']);
    Route::patch('addresses/{address}/primary', [\App\Http\Controllers\AddressController::class, 'setPrimary'])->name('addresses.primary');
});

// ==========================================
// 3. ADMIN PANEL (Admin & Superadmin)
// ==========================================
Route::middleware(['role:admin,superadmin'])->prefix('admin')->group(function () {
    Route::get('/', [DashboardController::class, 'index']);
    Route::get('dashboard', [DashboardController::class, 'index']);
    
    // Orders Management
    Route::get('orders', [OrderController::class, 'index']);
    Route::get('orders/{id}', [OrderController::class, 'show']);
    Route::post('orders/{id}/shipping', [OrderController::class, 'updateShipping']);
    Route::post('orders/{id}/approve', [OrderController::class, 'approve']);
    Route::post('orders/{id}/verify-payment', [OrderController::class, 'verifyPayment']);
    Route::post('orders/{id}/reject-payment', [OrderController::class, 'rejectPayment']);
    Route::post('orders/{id}/tracking', [OrderController::class, 'updateTracking']);
    Route::post('orders/{id}/complete', [OrderController::class, 'markCompleted']);
    Route::post('orders/{id}/cancel', [OrderController::class, 'cancel']);
    Route::post('orders/{id}/send-rating', [OrderController::class, 'sendRatingLink']);

    // Products
    Route::get('products', [ProductController::class, 'index']);
    Route::get('products/create', [ProductController::class, 'create']);
    Route::post('products/store', [ProductController::class, 'store']);
    Route::get('products/edit/{id}', [ProductController::class, 'edit']);
    Route::post('products/update/{id}', [ProductController::class, 'update']);
    Route::get('products/delete/{id}', [ProductController::class, 'delete']);

    // Reviews
    Route::get('reviews', [ReviewController::class, 'index']);
    Route::post('reviews/reply/{id}', [ReviewController::class, 'reply']);
    Route::get('reviews/delete/{id}', [ReviewController::class, 'delete']);

    // Newsletter
    Route::get('newsletter', [NewsletterController::class, 'index']);
    Route::get('newsletter/create', [NewsletterController::class, 'create']);
    Route::post('newsletter/store', [NewsletterController::class, 'store']);
    Route::post('newsletter/send/{id}', [NewsletterController::class, 'send']);
});

// ==========================================
// 4. SUPERADMIN ONLY (Hanya Superadmin)
// ==========================================
Route::middleware(['role:superadmin'])->prefix('admin')->group(function () {
    // Users
    Route::get('users', [UserController::class, 'index']);
    Route::post('users/change-role/{id}', [UserController::class, 'changeRole']);
    Route::get('users/delete/{id}', [UserController::class, 'delete']);

    // Payment Methods
    Route::get('payment-methods', [PaymentMethodController::class, 'index']);
    Route::post('payment-methods', [PaymentMethodController::class, 'store']);
    Route::post('payment-methods/{id}', [PaymentMethodController::class, 'update']);
    Route::delete('payment-methods/{id}', [PaymentMethodController::class, 'destroy']);

    // CMS
    Route::get('cms', [CmsController::class, 'index']);
    Route::post('cms/update', [CmsController::class, 'update']);
});

// Password Reset Routes
Route::get('/forgot-password', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('password.update');

// Admin Reports & Vouchers Routes
Route::middleware(['role:admin,superadmin'])->prefix('admin')->group(function () {
    Route::get('reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('admin.reports.index');
    Route::get('reports/export', [\App\Http\Controllers\Admin\ReportController::class, 'exportCsv'])->name('admin.reports.export');
    Route::get('vouchers', [\App\Http\Controllers\Admin\VoucherController::class, 'index'])->name('admin.vouchers.index');
    Route::post('vouchers', [\App\Http\Controllers\Admin\VoucherController::class, 'store'])->name('admin.vouchers.store');
    Route::delete('vouchers/{id}', [\App\Http\Controllers\Admin\VoucherController::class, 'destroy'])->name('admin.vouchers.destroy');
});

Route::middleware(['role:superadmin'])->prefix('admin')->group(function () {
    // Sales Analytics
    Route::get('sales', [SalesController::class, 'index']);
});
