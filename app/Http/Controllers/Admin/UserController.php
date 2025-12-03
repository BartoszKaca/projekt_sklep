<?php

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

        
        if ($request->filled('status')) {
            $isActive = $request->status === 'active';
            $query->where('is_active', $isActive);
        }

        
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%');
            });
        }

        
        switch ($request->get('sort', 'newest')) {
            case 'oldest':
                $query->oldest();
                break;
            case 'name':
                $query->orderBy('name');
                break;
            case 'orders':
                $query->withCount('orders')->orderBy('orders_count', 'desc');
                break;
            default: // newest
                $query->latest();
                break;
        }

        $users = $query->with(['orders', 'reviews'])->paginate(20);

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
        ]);

        
        $validated['is_active'] = $request->has('is_active');

        $user->update($validated);

        return back()->with('success', 'Użytkownik został zaktualizowany!');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Nie możesz usunąć swojego własnego konta!');
        }

        $userName = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', "Użytkownik {$userName} został usunięty!");
    }

    public function toggleStatus(User $user)
    {
        // Prevent deactivating yourself
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Nie możesz dezaktywować swojego własnego konta!');
        }

        $user->update([
            'is_active' => !$user->is_active
        ]);

        $status = $user->is_active ? 'aktywowany' : 'dezaktywowany';
        return back()->with('success', "Użytkownik {$user->name} został {$status}!");
    }
}