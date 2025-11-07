<?php

// app/Http/Controllers/Admin/DashboardController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'today_revenue' => Order::whereDate('created_at', today())
                ->where('payment_status', 'paid')
                ->sum('total'),
            'total_customers' => User::where('role', 'customer')->count(),
            'low_stock_products' => Product::lowStock()->count(),
            'out_of_stock' => Product::where('stock_quantity', 0)->count(),
        ];

        $recent_orders = Order::with(['user', 'items'])
            ->latest()
            ->take(10)
            ->get();

        $top_products = Product::withCount('orderItems')
            ->orderBy('order_items_count', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recent_orders', 'top_products'));
    }
}

// app/Http/Controllers/Admin/ProductController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'primaryImage']);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('sku', 'like', '%' . $request->search . '%')
                  ->orWhere('artist', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('stock')) {
            if ($request->stock === 'low') {
                $query->lowStock();
            } elseif ($request->stock === 'out') {
                $query->where('stock_quantity', 0);
            }
        }

        $products = $query->latest()->paginate(20);
        $categories = Category::all();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::active()->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:album,merch',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0|lt:price',
            'artist' => 'nullable|string|max:255',
            'release_year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'format' => 'nullable|string',
            'label' => 'nullable|string|max:255',
            'stock_quantity' => 'required|integer|min:0',
            'low_stock_threshold' => 'required|integer|min:1',
            'sku' => 'required|string|unique:products',
            'barcode' => 'nullable|string',
            'weight' => 'nullable|numeric|min:0',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $product = Product::create($validated);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('products', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'path' => $path,
                    'is_primary' => $index === 0,
                    'sort_order' => $index,
                ]);
            }
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Produkt został dodany pomyślnie!');
    }

    public function edit(Product $product)
    {
        $product->load(['category', 'images', 'variants']);
        $categories = Category::active()->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:album,merch',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0|lt:price',
            'artist' => 'nullable|string|max:255',
            'release_year' => 'nullable|integer',
            'format' => 'nullable|string',
            'label' => 'nullable|string|max:255',
            'stock_quantity' => 'required|integer|min:0',
            'low_stock_threshold' => 'required|integer|min:1',
            'sku' => 'required|string|unique:products,sku,' . $product->id,
            'barcode' => 'nullable|string',
            'weight' => 'nullable|numeric|min:0',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $product->update($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Produkt został zaktualizowany!');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('admin.products.index')
            ->with('success', 'Produkt został usunięty!');
    }

    public function stock(Product $product)
    {
        $stockMovements = $product->stockMovements()
            ->with('user')
            ->latest()
            ->paginate(20);

        return view('admin.products.stock', compact('product', 'stockMovements'));
    }

    public function adjustStock(Request $request, Product $product)
    {
        $validated = $request->validate([
            'type' => 'required|in:in,out,adjustment',
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|string',
            'reference' => 'nullable|string',
        ]);

        $stockBefore = $product->stock_quantity;

        if ($validated['type'] === 'in') {
            $product->increment('stock_quantity', $validated['quantity']);
        } elseif ($validated['type'] === 'out') {
            if ($product->stock_quantity < $validated['quantity']) {
                return back()->withErrors(['quantity' => 'Niewystarczająca ilość na stanie!']);
            }
            $product->decrement('stock_quantity', $validated['quantity']);
        } else {
            $product->update(['stock_quantity' => $validated['quantity']]);
        }

        $product->stockMovements()->create([
            'type' => $validated['type'],
            'quantity' => $validated['quantity'],
            'stock_before' => $stockBefore,
            'stock_after' => $product->fresh()->stock_quantity,
            'reason' => $validated['reason'],
            'reference' => $validated['reference'] ?? null,
            'user_id' => auth()->id(),
        ]);

        return back()->with('success', 'Stan magazynowy został zaktualizowany!');
    }
}

// app/Http/Controllers/Admin/OrderController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user', 'items', 'shipping']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('search')) {
            $query->where('order_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', function($q) use ($request) {
                      $q->where('name', 'like', '%' . $request->search . '%')
                        ->orWhere('email', 'like', '%' . $request->search . '%');
                  });
        }

        $orders = $query->latest()->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'items.product', 'shipping', 'stockMovements']);
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,processing,shipped,delivered,cancelled,refunded',
            'admin_notes' => 'nullable|string',
        ]);

        $order->update($validated);

        if ($validated['status'] === 'shipped' && $request->filled('tracking_number')) {
            $order->markAsShipped(
                $request->tracking_number,
                $request->carrier ?? 'InPost'
            );
        }

        return back()->with('success', 'Status zamówienia został zaktualizowany!');
    }

    public function updatePaymentStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'payment_status' => 'required|in:pending,paid,failed,refunded',
        ]);

        $order->update($validated);

        if ($validated['payment_status'] === 'paid') {
            $order->markAsPaid();
        }

        return back()->with('success', 'Status płatności został zaktualizowany!');
    }
}

// app/Http/Controllers/Admin/StockController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->filled('status')) {
            if ($request->status === 'low') {
                $query->lowStock();
            } elseif ($request->status === 'out') {
                $query->where('stock_quantity', 0);
            }
        }

        $products = $query->with('category')->get();

        return view('admin.stock.index', compact('products'));
    }

    public function history(Request $request)
    {
        $movements = StockMovement::with(['product', 'variant', 'order', 'user'])
            ->latest()
            ->paginate(50);

        return view('admin.stock.history', compact('movements'));
    }

    public function export()
    {
        // Implementacja eksportu do CSV/Excel
        $products = Product::with('category')->get();

        $filename = 'stock_report_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($products) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['SKU', 'Nazwa', 'Kategoria', 'Stan', 'Próg niskiego stanu', 'Status']);

            foreach ($products as $product) {
                fputcsv($file, [
                    $product->sku,
                    $product->name,
                    $product->category->name,
                    $product->stock_quantity,
                    $product->low_stock_threshold,
                    $product->isLowStock() ? 'Niski stan' : 'OK',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

// app/Http/Controllers/Admin/CategoryController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        Category::create($validated);

        return back()->with('success', 'Kategoria została dodana!');
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $category->update($validated);

        return back()->with('success', 'Kategoria została zaktualizowana!');
    }

    public function destroy(Category $category)
    {
        if ($category->products()->count() > 0) {
            return back()->withErrors(['error' => 'Nie można usunąć kategorii z przypisanymi produktami!']);
        }

        $category->delete();
        return back()->with('success', 'Kategoria została usunięta!');
    }
}

// app/Http/Controllers/Admin/ReportController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function sales(Request $request)
    {
        $startDate = $request->input('start_date', now()->subMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));

        // Sprzedaż według dni
        $dailySales = Order::where('payment_status', 'paid')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as orders, SUM(total) as revenue')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Top produkty
        $topProducts = OrderItem::select('product_id', 'product_name', 'sku')
            ->selectRaw('SUM(quantity) as total_sold, SUM(total) as revenue')
            ->whereHas('order', function($q) use ($startDate, $endDate) {
                $q->where('payment_status', 'paid')
                  ->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->groupBy('product_id', 'product_name', 'sku')
            ->orderByDesc('total_sold')
            ->take(10)
            ->get();

        // Statystyki
        $stats = [
            'total_revenue' => Order::where('payment_status', 'paid')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('total'),
            'total_orders' => Order::whereBetween('created_at', [$startDate, $endDate])->count(),
            'avg_order_value' => Order::where('payment_status', 'paid')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->avg('total'),
            'albums_revenue' => OrderItem::whereHas('product', function($q) {
                    $q->where('type', 'album');
                })
                ->whereHas('order', function($q) use ($startDate, $endDate) {
                    $q->where('payment_status', 'paid')
                      ->whereBetween('created_at', [$startDate, $endDate]);
                })
                ->sum('total'),
            'merch_revenue' => OrderItem::whereHas('product', function($q) {
                    $q->where('type', 'merch');
                })
                ->whereHas('order', function($q) use ($startDate, $endDate) {
                    $q->where('payment_status', 'paid')
                      ->whereBetween('created_at', [$startDate, $endDate]);
                })
                ->sum('total'),
        ];

        return view('admin.reports.sales', compact('dailySales', 'topProducts', 'stats', 'startDate', 'endDate'));
    }

    public function inventory()
    {
        $lowStockProducts = Product::lowStock()
            ->with('category')
            ->get();

        $outOfStock = Product::where('stock_quantity', 0)
            ->with(['category', 'stockMovements'])
            ->get();

        $totalValue = Product::selectRaw('SUM(stock_quantity * price) as value')
            ->first()
            ->value;

        return view('admin.reports.inventory', compact('lowStockProducts', 'outOfStock', 'totalValue'));
    }
}

// app/Http/Controllers/Admin/CouponController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index()
    {
        $coupons = Coupon::latest()->paginate(20);
        return view('admin.coupons.index', compact('coupons'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:coupons|max:50',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'min_order_value' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after:valid_from',
            'is_active' => 'boolean',
        ]);

        $validated['code'] = strtoupper($validated['code']);
        Coupon::create($validated);

        return back()->with('success', 'Kupon został utworzony!');
    }

    public function update(Request $request, Coupon $coupon)
    {
        $validated = $request->validate([
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'min_order_value' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after:valid_from',
            'is_active' => 'boolean',
        ]);

        $coupon->update($validated);

        return back()->with('success', 'Kupon został zaktualizowany!');
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();
        return back()->with('success', 'Kupon został usunięty!');
    }
}

// app/Http/Controllers/Admin/UserController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $users = $query->withCount('orders')->latest()->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        $user->load(['orders' => function($q) {
            $q->latest()->take(10);
        }, 'addresses', 'reviews']);

        return view('admin.users.show', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,customer',
            'phone' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $user->update($validated);

        return back()->with('success', 'Użytkownik został zaktualizowany!');
    }
}

// app/Http/Controllers/Admin/ReviewController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::with(['product', 'user']);

        if ($request->filled('status')) {
            if ($request->status === 'pending') {
                $query->where('is_approved', false);
            } elseif ($request->status === 'approved') {
                $query->where('is_approved', true);
            }
        }

        $reviews = $query->latest()->paginate(20);

        return view('admin.reviews.index', compact('reviews'));
    }

    public function approve(Review $review)
    {
        $review->update(['is_approved' => true]);
        return back()->with('success', 'Opinia została zaakceptowana!');
    }

    public function reject(Review $review)
    {
        $review->update(['is_approved' => false]);
        return back()->with('success', 'Opinia została odrzucona!');
    }

    public function destroy(Review $review)
    {
        $review->delete();
        return back()->with('success', 'Opinia została usunięta!');
    }
}