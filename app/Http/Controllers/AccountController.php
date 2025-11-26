<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Order;

class AccountController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        $recentOrders = $user->orders()->latest()->take(5)->get();
        $wishlistCount = $user->wishlist()->count();
        
        return view('account.dashboard', compact('user', 'recentOrders', 'wishlistCount'));
    }

    public function edit()
    {
        $user = auth()->user();
        return view('account.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
        ]);

        $user->update($validated);

        return redirect()->route('account.dashboard')
            ->with('success', 'Dane zostały zaktualizowane!');
    }

    public function passwordForm()
    {
        return view('account.password');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = auth()->user();

        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Nieprawidłowe aktualne hasło.']);
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('account.dashboard')
            ->with('success', 'Hasło zostało zmienione!');
    }

    public function orders()
    {
        $orders = auth()->user()->orders()->with(['items'])->latest()->paginate(10);
        return view('account.orders', compact('orders'));
    }

    public function orderShow(Order $order)
    {
        // Ensure user can only see their own orders
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $order->load(['items', 'shipping']);
        return view('account.order-show', compact('order'));
    }
}
