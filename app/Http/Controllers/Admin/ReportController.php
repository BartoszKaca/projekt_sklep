<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function sales(Request $request)
    {
        try {
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

            // Pobierz top produkty - naprawiona wersja z whereIn
            $topProducts = OrderItem::select('order_items.product_id', 'order_items.product_name', 'products.sku')
                ->selectRaw('SUM(order_items.quantity) as total_sold, SUM(order_items.total) as revenue')
                ->leftJoin('products', 'order_items.product_id', '=', 'products.id')
                ->whereIn('order_items.order_id', function($query) use ($startDate, $endDate) {
                    $query->select('id')
                        ->from('orders')
                        ->where('payment_status', 'paid')
                        ->whereBetween('created_at', [$startDate, $endDate]);
                })
                ->groupBy('order_items.product_id', 'order_items.product_name', 'products.sku')
                ->orderByDesc('total_sold')
                ->take(10)
                ->get();

            // Oblicz przychody według typu produktu - naprawiona wersja
            $albumsRevenue = OrderItem::whereIn('order_items.order_id', function($query) use ($startDate, $endDate) {
                    $query->select('id')
                        ->from('orders')
                        ->where('payment_status', 'paid')
                        ->whereBetween('created_at', [$startDate, $endDate]);
                })
                ->whereIn('order_items.product_id', function($query) {
                    $query->select('id')
                        ->from('products')
                        ->where('type', 'album');
                })
                ->sum('total');

            $merchRevenue = OrderItem::whereIn('order_items.order_id', function($query) use ($startDate, $endDate) {
                    $query->select('id')
                        ->from('orders')
                        ->where('payment_status', 'paid')
                        ->whereBetween('created_at', [$startDate, $endDate]);
                })
                ->whereIn('order_items.product_id', function($query) {
                    $query->select('id')
                        ->from('products')
                        ->where('type', 'merch');
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
        } catch (\Exception $e) {
            Log::error('Sales report error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return redirect()->route('admin.dashboard')
                ->with('error', 'Wystąpił błąd podczas generowania raportu sprzedaży: ' . $e->getMessage());
        }
    }

    public function inventory()
    {
        try {
            // UPROSZCZONY RAPORT - TYLKO PRODUKTY BEZ WARIANTÓW
            
            // Produkty z niskim stanem
            $lowStockProducts = Product::where(function($query) {
                    $query->whereRaw('stock_quantity > 0')
                        ->whereRaw('stock_quantity <= low_stock_threshold');
                })
                ->with('category')
                ->get();

            // Produkty bez stanu
            $outOfStock = Product::where('stock_quantity', 0)
                ->with('category')
                ->get();

            // Wartość zapasów - tylko produkty
            try {
                $totalValue = Product::selectRaw('COALESCE(SUM(stock_quantity * price), 0) as value')
                    ->value('value') ?? 0;
                $totalValue = (float)$totalValue;
            } catch (\Exception $e) {
                Log::warning('Error calculating inventory value: ' . $e->getMessage());
                $totalValue = 0;
            }

            // Łączna liczba jednostek - tylko produkty
            try {
                $totalUnits = Product::sum('stock_quantity') ?? 0;
                $totalUnits = (int)$totalUnits;
            } catch (\Exception $e) {
                Log::warning('Error calculating total units: ' . $e->getMessage());
                $totalUnits = 0;
            }

            return view('admin.reports.inventory', compact(
                'lowStockProducts', 
                'outOfStock', 
                'totalValue',
                'totalUnits'
            ));
        } catch (\Exception $e) {
            Log::error('Inventory report error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return redirect()->route('admin.dashboard')
                ->with('error', 'Wystąpił błąd podczas generowania raportu inwentarza.');
        }
    }
}
