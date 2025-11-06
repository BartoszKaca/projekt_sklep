<?php

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
        $startDate = $request->input('start_date', now()->subMonth());
        $endDate = $request->input('end_date', now());

        // Sprzedaż według dni
        $dailySales = Order::where('payment_status', 'paid')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as orders, SUM(total) as revenue')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Top produkty
        $topProducts = OrderItem::select('product_id', 'product_name')
            ->selectRaw('SUM(quantity) as total_sold, SUM(total) as revenue')
            ->whereHas('order', function($q) use ($startDate, $endDate) {
                $q->where('payment_status', 'paid')
                  ->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->groupBy('product_id', 'product_name')
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
