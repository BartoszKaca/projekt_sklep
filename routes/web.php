<?php

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

// === STRONY GŁÓWNE ===

// Strona główna
Route::get('/', [HomeController::class, 'index'])->name('home');

// Lista produktów
Route::get('/produkty', [HomeController::class, 'products'])->name('products.index');

// Kategoria produktów
Route::get('/kategoria/{slug}', [CategoryController::class, 'show'])->name('category.show');

// Szczegóły produktu
Route::get('/produkt/{slug}', [ProductController::class, 'show'])->name('products.show');

// === KOSZYK ===

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
Route::get('/cart/count', [CartController::class, 'count'])->name('cart.count');

// === NEWSLETTER ===

Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');
Route::post('/newsletter/unsubscribe', [NewsletterController::class, 'unsubscribe'])->name('newsletter.unsubscribe');
Route::get('/newsletter/unsubscribe', [NewsletterController::class, 'unsubscribe'])->name('newsletter.unsubscribe.link');

// === LOGOWANIE I REJESTRACJA ===

Route::get('/login', [\App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [\App\Http\Controllers\Auth\LoginController::class, 'login']);
Route::post('/logout', [\App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

Route::get('/register', [\App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [\App\Http\Controllers\Auth\RegisterController::class, 'register']);

Route::get('/password/reset', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/password/email', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/password/reset/{token}', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('password.update');

// === WERYFIKACJA EMAILA ===

Route::middleware(['auth'])->group(function () {
    Route::get('/verify-email', [App\Http\Controllers\Auth\VerificationController::class, 'show'])->name('verify.email.form');
    Route::post('/verification/resend', [App\Http\Controllers\Auth\VerificationController::class, 'resend'])->name('verification.resend');
});

Route::get('/email/verify', [App\Http\Controllers\Auth\VerificationController::class, 'show'])
    ->middleware('auth')
    ->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', [App\Http\Controllers\Auth\VerificationController::class, 'verify'])
    ->middleware(['auth', 'signed'])
    ->name('verification.verify');

// === CHECKOUT (kasa) ===

Route::middleware(['guest.checkout'])->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/coupon', [CheckoutController::class, 'applyCoupon'])->name('checkout.coupon');
    Route::delete('/checkout/coupon', [CheckoutController::class, 'removeCoupon'])->name('checkout.coupon.remove');
    Route::post('/checkout', [CheckoutController::class, 'processOrder'])->name('checkout.process');
});

Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])->name('checkout.success');

// === PŁATNOŚCI ===

Route::get('/payment/{order}', [PaymentController::class, 'process'])->name('payment.process');
Route::get('/payment/{order}/return', [PaymentController::class, 'return'])->name('payment.return');
Route::get('/payment/{order}/status', [PaymentController::class, 'checkStatus'])->name('payment.status');
Route::post('/payment/notify', [PaymentController::class, 'notify'])->name('payment.notify');

// === PANEL ADMINA ===

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Produkty
    Route::resource('products', AdminProductController::class);
    Route::get('products/{product}/stock', [AdminProductController::class, 'stock'])->name('products.stock');
    Route::post('products/{product}/adjust-stock', [AdminProductController::class, 'adjustStock'])->name('products.adjust-stock');
    Route::post('variants/{variant}/adjust-stock', [AdminProductController::class, 'adjustVariantStock'])->name('variants.adjust-stock');

    // Kategorie
    Route::resource('categories', AdminCategoryController::class)->except(['show', 'create', 'edit']);

    // Zamówienia
    Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::patch('orders/{order}/payment', [OrderController::class, 'updatePaymentStatus'])->name('orders.update-payment');

    // Magazyn
    Route::get('stock', [StockController::class, 'index'])->name('stock.index');
    Route::get('stock/history', [StockController::class, 'history'])->name('stock.history');
    Route::get('stock/export', [StockController::class, 'export'])->name('stock.export');

    // Kupony
    Route::resource('coupons', CouponController::class)->except(['show', 'create', 'edit']);

    // Użytkownicy
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::patch('users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');

    // Opinie
    Route::get('reviews', [ReviewController::class, 'index'])->name('reviews.index');
    Route::post('reviews/{review}/approve', [ReviewController::class, 'approve'])->name('reviews.approve');
    Route::post('reviews/{review}/reject', [ReviewController::class, 'reject'])->name('reviews.reject');
    Route::delete('reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

    // Raporty
    Route::get('reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
    Route::get('reports/inventory', [ReportController::class, 'inventory'])->name('reports.inventory');
});

// === STRONY INFORMACYJNE ===

Route::get('/o-nas', function () {
    return view('pages.about');
})->name('about');

Route::get('/regulamin', function () {
    return view('pages.terms');
})->name('terms');

Route::get('/polityka-prywatnosci', function () {
    return view('pages.privacy');
})->name('privacy');

Route::get('/dostawa-i-platnosc', function () {
    return view('pages.shipping');
})->name('shipping');

Route::get('/zwroty-i-reklamacje', function () {
    return view('pages.returns');
})->name('returns');

// === PANEL UŻYTKOWNIKA ===

Route::middleware(['auth', 'verified'])->prefix('account')->name('account.')->group(function () {

    // Dashboard użytkownika
    Route::get('/', [AccountController::class, 'dashboard'])->name('dashboard');
    
    // Profil
    Route::get('/profile', [AccountController::class, 'editProfile'])->name('profile');
    Route::put('/profile', [AccountController::class, 'updateProfile'])->name('profile.update');
    
    // Zmiana hasła
    Route::get('/password', [AccountController::class, 'showPasswordForm'])->name('password');
    Route::put('/password', [AccountController::class, 'updatePassword'])->name('password.update');
    
    // Adresy
    Route::get('/addresses', [AccountController::class, 'addresses'])->name('addresses');
    Route::get('/addresses/create', [AccountController::class, 'createAddress'])->name('addresses.create');
    Route::post('/addresses', [AccountController::class, 'storeAddress'])->name('addresses.store');
    Route::get('/addresses/{address}/edit', [AccountController::class, 'editAddress'])->name('addresses.edit');
    Route::put('/addresses/{address}', [AccountController::class, 'updateAddress'])->name('addresses.update');
    Route::delete('/addresses/{address}', [AccountController::class, 'destroyAddress'])->name('addresses.destroy');
    Route::post('/addresses/{address}/default', [AccountController::class, 'setDefaultAddress'])->name('addresses.default');
    
    // Zamówienia
    Route::get('/orders', [AccountController::class, 'orders'])->name('orders');
    Route::get('/orders/{order}', [AccountController::class, 'showOrder'])->name('orders.show');
    
    // Lista życzeń (wishlist)
    Route::get('/wishlist', [AccountController::class, 'wishlist'])->name('wishlist');
    Route::post('/wishlist/add', [AccountController::class, 'addToWishlist'])->name('wishlist.add');
    Route::post('/wishlist/remove', [AccountController::class, 'removeFromWishlist'])->name('wishlist.remove');
});

// === API (tylko dla admina) ===

Route::prefix('api')->middleware(['auth', 'admin'])->group(function () {
    // Tutaj można dodać endpointy API
});
