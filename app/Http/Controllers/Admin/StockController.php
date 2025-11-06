<?php

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