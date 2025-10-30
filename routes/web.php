<?php

// routes/web.php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\StockController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\ReportController;

// Public routes
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Authentication routes
Auth::routes();

// Admin routes - wymagają autoryzacji i roli admin
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    // Produkty
    Route::resource('products', ProductController::class);
    Route::get('products/{product}/stock', [ProductController::class, 'stock'])->name('products.stock');
    Route::post('products/{product}/adjust-stock', [ProductController::class, 'adjustStock'])->name('products.adjust-stock');
    
    // Kategorie
    Route::resource('categories', CategoryController::class)->except(['show', 'create', 'edit']);
    
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
    
    // Opinie
    Route::get('reviews', [ReviewController::class, 'index'])->name('reviews.index');
    Route::post('reviews/{review}/approve', [ReviewController::class, 'approve'])->name('reviews.approve');
    Route::post('reviews/{review}/reject', [ReviewController::class, 'reject'])->name('reviews.reject');
    Route::delete('reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
    
    // Raporty
    Route::get('reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
    Route::get('reports/inventory', [ReportController::class, 'inventory'])->name('reports.inventory');
});

// Customer routes - wymagają tylko autoryzacji
Route::middleware(['auth'])->group(function () {
    Route::get('/account', function () {
        return view('account.dashboard');
    })->name('account.dashboard');
    
    Route::get('/orders', function () {
        $orders = auth()->user()->orders()->latest()->paginate(10);
        return view('account.orders', compact('orders'));
    })->name('account.orders');
    
    Route::get('/wishlist', function () {
        $wishlist = auth()->user()->wishlist()->with('product')->get();
        return view('account.wishlist', compact('wishlist'));
    })->name('account.wishlist');
});

// API routes dla AJAX requests
Route::prefix('api')->middleware(['auth', 'admin'])->group(function () {
    Route::get('products/search', function (Illuminate\Http\Request $request) {
        $products = App\Models\Product::where('name', 'like', '%' . $request->q . '%')
            ->orWhere('sku', 'like', '%' . $request->q . '%')
            ->limit(10)
            ->get(['id', 'name', 'sku', 'stock_quantity']);
        return response()->json($products);
    });
    
    Route::get('dashboard/stats', function () {
        return response()->json([
            'today_orders' => App\Models\Order::whereDate('created_at', today())->count(),
            'today_revenue' => App\Models\Order::whereDate('created_at', today())
                ->where('payment_status', 'paid')
                ->sum('total'),
            'pending_orders' => App\Models\Order::where('status', 'pending')->count(),
            'low_stock_count' => App\Models\Product::lowStock()->count(),
        ]);
    });
});
Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
