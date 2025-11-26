<?php

// routes/web.php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\StockController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\NewsletterController;

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/produkty', [HomeController::class, 'products'])->name('products.index');
Route::get('/szukaj', [HomeController::class, 'search'])->name('products.search');

// public route for categories
Route::get('/kategoria/{slug}', [CategoryController::class, 'show'])->name('category.show');

// public route for product page
Route::get('/produkt/{slug}', [ProductController::class, 'show'])->name('products.show');

// Cart routes
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
Route::get('/cart/count', [CartController::class, 'count'])->name('cart.count');

// Newsletter routes
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');
Route::post('/newsletter/unsubscribe', [NewsletterController::class, 'unsubscribe'])->name('newsletter.unsubscribe');

// Authentication routes
Auth::routes(['verify' => true]);

// Checkout routes (guest or authenticated)
Route::middleware(['guest.checkout'])->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/coupon', [CheckoutController::class, 'applyCoupon'])->name('checkout.coupon');
    Route::delete('/checkout/coupon', [CheckoutController::class, 'removeCoupon'])->name('checkout.coupon.remove');
    Route::post('/checkout', [CheckoutController::class, 'processOrder'])->name('checkout.process');
});
Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])->name('checkout.success');

// Payment routes
Route::get('/payment/{order}', [PaymentController::class, 'process'])->name('payment.process');
Route::get('/payment/{order}/return', [PaymentController::class, 'return'])->name('payment.return');
Route::post('/payment/notify', [PaymentController::class, 'notify'])->name('payment.notify');

// Admin routes - wymagają autoryzacji i roli admin
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Produkty (admin)
    Route::resource('products', AdminProductController::class);
    Route::get('products/{product}/stock', [AdminProductController::class, 'stock'])->name('products.stock');
    Route::post('products/{product}/adjust-stock', [AdminProductController::class, 'adjustStock'])->name('products.adjust-stock');

    // Kategorie (admin)
    Route::resource('categories', AdminCategoryController::class)->except(['show', 'create', 'edit']);

    // Zamówienia (admin)
    Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::patch('orders/{order}/payment', [OrderController::class, 'updatePaymentStatus'])->name('orders.update-payment');

    // Magazyn (admin)
    Route::get('stock', [StockController::class, 'index'])->name('stock.index');
    Route::get('stock/history', [StockController::class, 'history'])->name('stock.history');
    Route::get('stock/export', [StockController::class, 'export'])->name('stock.export');

    // Kupony (admin)
    Route::resource('coupons', CouponController::class)->except(['show', 'create', 'edit']);

    // Użytkownicy (admin)
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::patch('users/{user}', [UserController::class, 'update'])->name('users.update');

    // Opinie (admin)
    Route::get('reviews', [ReviewController::class, 'index'])->name('reviews.index');
    Route::post('reviews/{review}/approve', [ReviewController::class, 'approve'])->name('reviews.approve');
    Route::post('reviews/{review}/reject', [ReviewController::class, 'reject'])->name('reviews.reject');
    Route::delete('reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

    // Raporty (admin)
    Route::get('reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
    Route::get('reports/inventory', [ReportController::class, 'inventory'])->name('reports.inventory');
});

// Customer routes - wymagają tylko autoryzacji
Route::middleware(['auth'])->prefix('account')->name('account.')->group(function () {
    // Dashboard
    Route::get('/', [AccountController::class, 'dashboard'])->name('dashboard');
    
    // Profile
    Route::get('/profile', [AccountController::class, 'editProfile'])->name('profile');
    Route::put('/profile', [AccountController::class, 'updateProfile'])->name('profile.update');
    
    // Password
    Route::get('/password', [AccountController::class, 'showPasswordForm'])->name('password');
    Route::put('/password', [AccountController::class, 'updatePassword'])->name('password.update');
    
    // Addresses
    Route::get('/addresses', [AccountController::class, 'addresses'])->name('addresses');
    Route::get('/addresses/create', [AccountController::class, 'createAddress'])->name('addresses.create');
    Route::post('/addresses', [AccountController::class, 'storeAddress'])->name('addresses.store');
    Route::get('/addresses/{address}/edit', [AccountController::class, 'editAddress'])->name('addresses.edit');
    Route::put('/addresses/{address}', [AccountController::class, 'updateAddress'])->name('addresses.update');
    Route::delete('/addresses/{address}', [AccountController::class, 'destroyAddress'])->name('addresses.destroy');
    Route::post('/addresses/{address}/default', [AccountController::class, 'setDefaultAddress'])->name('addresses.default');
    
    // Orders
    Route::get('/orders', [AccountController::class, 'orders'])->name('orders');
    Route::get('/orders/{order}', [AccountController::class, 'showOrder'])->name('orders.show');
    
    // Wishlist
    Route::get('/wishlist', [AccountController::class, 'wishlist'])->name('wishlist');
    Route::post('/wishlist/add', [AccountController::class, 'addToWishlist'])->name('wishlist.add');
    Route::post('/wishlist/remove', [AccountController::class, 'removeFromWishlist'])->name('wishlist.remove');
});

// API routes dla AJAX requests (niezmienione)
Route::prefix('api')->middleware(['auth', 'admin'])->group(function () {
    // zachowaj istniejące api routes
});