<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function sales(Request $request)
    {
        // Parsuj daty - obsłuż różne formaty
        $startDate = $request->input('start_date') 
            ? Carbon::parse($request->input('start_date'))->startOfDay()
            : now()->subMonth()->startOfDay();
        $endDate = $request->input('end_date')
            ? Carbon::parse($request->input('end_date'))->endOfDay()
            : now()->endOfDay();

        // Pobierz dzienną sprzedaż
        $dailySales = Order::where('payment_status', 'paid')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as orders, SUM(total) as revenue')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Pobierz top produkty
        $topProducts = OrderItem::select('order_items.product_id', 'order_items.product_name', 'products.sku')
            ->selectRaw('SUM(order_items.quantity) as total_sold, SUM(order_items.total) as revenue')
            ->leftJoin('products', 'order_items.product_id', '=', 'products.id')
            ->whereHas('order', function($q) use ($startDate, $endDate) {
                $q->where('payment_status', 'paid')
                  ->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->groupBy('order_items.product_id', 'order_items.product_name', 'products.sku')
            ->orderByDesc('total_sold')
            ->take(10)
            ->get();

        // Oblicz przychody według typu produktu
        $albumsRevenue = OrderItem::whereHas('order', function($q) use ($startDate, $endDate) {
                $q->where('payment_status', 'paid')
                  ->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->whereHas('product', function($q) {
                $q->where('type', 'album');
            })
            ->sum('total');

        $merchRevenue = OrderItem::whereHas('order', function($q) use ($startDate, $endDate) {
                $q->where('payment_status', 'paid')
                  ->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->whereHas('product', function($q) {
                $q->where('type', 'merch');
            })
            ->sum('total');

        // Oblicz statystyki
        $totalRevenue = Order::where('payment_status', 'paid')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total');
            
        $totalOrders = Order::where('payment_status', 'paid')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $stats = [
            'total_revenue' => $totalRevenue,
            'total_orders' => $totalOrders,
            'avg_order_value' => $totalOrders > 0 ? $totalRevenue / $totalOrders : 0,
            'albums_revenue' => $albumsRevenue,
            'merch_revenue' => $merchRevenue,
        ];

        return view('admin.reports.sales', compact('dailySales', 'topProducts', 'stats', 'startDate', 'endDate'));
    }

    public function inventory()
    {
        $lowStockProducts = Product::lowStock()
            ->with('category')
            ->get();

        $outOfStock = Product::where('stock_quantity', 0)
            ->with('category')
            ->get();

        $totalValue = Product::selectRaw('SUM(stock_quantity * price) as value')
            ->first()
            ->value;

        return view('admin.reports.inventory', compact('lowStockProducts', 'outOfStock', 'totalValue'));
    }
}
