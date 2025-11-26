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
use App\Http\Controllers\WishlistController;

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');

// Search and products
Route::get('/szukaj', [ProductController::class, 'search'])->name('products.search');
Route::get('/produkty', [ProductController::class, 'index'])->name('products.index');

// public route for categories
Route::get('/kategoria/{slug}', [CategoryController::class, 'show'])->name('category.show');

// public route for product page
Route::get('/produkt/{slug}', [ProductController::class, 'show'])->name('products.show');
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
Route::get('/cart/count', [CartController::class, 'count'])->name('cart.count');

// Checkout routes (allow guest checkout)
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/checkout/success/{orderNumber}', [CheckoutController::class, 'success'])->name('checkout.success');

// Authentication routes
Auth::routes();

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
Route::middleware(['auth'])->group(function () {
    // Account
    Route::get('/account', [AccountController::class, 'dashboard'])->name('account.dashboard');
    Route::get('/account/edit', [AccountController::class, 'edit'])->name('account.edit');
    Route::put('/account', [AccountController::class, 'update'])->name('account.update');
    Route::get('/account/password', [AccountController::class, 'passwordForm'])->name('account.password');
    Route::put('/account/password', [AccountController::class, 'updatePassword'])->name('account.password.update');
    
    // Orders
    Route::get('/orders', [AccountController::class, 'orders'])->name('account.orders');
    Route::get('/orders/{order}', [AccountController::class, 'orderShow'])->name('account.orders.show');

    // Wishlist
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('account.wishlist');
    Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
    Route::delete('/wishlist/{id}', [WishlistController::class, 'remove'])->name('wishlist.remove');
});

// API routes dla AJAX requests (niezmienione)
Route::prefix('api')->middleware(['auth', 'admin'])->group(function () {
    // zachowaj istniejące api routes
});