<?php

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
            'tracking_number' => 'nullable|string',
            'carrier' => 'nullable|string',
        ]);

        $order->update([
            'status' => $validated['status'],
            'admin_notes' => $validated['admin_notes'] ?? $order->admin_notes,
            'payment_status' => $validated['status'] === 'refunded' ? 'refunded' : $order->payment_status,
        ]);

        

        if ($validated['status'] === 'shipped' && $request->filled('tracking_number')) {
            $order->markAsShipped(
                $request->tracking_number,
                $request->carrier ?? 'InPost'
            );
        }

        if ($validated['status'] === 'delivered') {
            $order->markAsDelivered();
        }

        if ($validated['status'] === 'cancelled') {
            $order->markAsCancelled($request->input('admin_notes'));
        }

        if ($validated['status'] === 'refunded') {
            $order->markAsRefunded($request->input('admin_notes'));
        }

        return back()->with('success', 'Status zamówienia został zaktualizowany!');
    }

    public function updatePaymentStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'payment_status' => 'required|in:pending,paid,failed,refunded',
        ]);

        // Jeśli status = paid, użyj markAsPaid() który ustawia też paid_at i wysyła email
        if ($validated['payment_status'] === 'paid') {
            $order->markAsPaid();
        } else {
            // Dla innych statusów - zwykły update
            $order->payment_status = $validated['payment_status'];
            $order->save();
        }

        return back()->with('success', 'Status płatności został zaktualizowany!');
    }
}
