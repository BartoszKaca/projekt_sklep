<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressRequest;
use App\Http\Requests\PasswordUpdateRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\Address;
use App\Models\Wishlist;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

/**
 * Controller for user account management.
 * Handles dashboard, profile editing, password changes, addresses, orders, and wishlist.
 */
class AccountController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the user dashboard.
     */

    
    public function dashboard(): View
    {
        $user = auth()->user();
        $recentOrders = $user->orders()->latest()->take(5)->get();
        $recentOrdersCount = $recentOrders->count();
        $wishlistCount = $user->wishlist()->count();
        $addressCount = $user->addresses()->count();
        
        return view('account.dashboard', compact('user', 'recentOrders', 'recentOrdersCount', 'wishlistCount', 'addressCount'));
    }

    /**
     * Show the profile edit form.
     */
    public function editProfile(): View
    {
        $user = auth()->user();
        return view('account.profile', compact('user'));
    }

    /**
     * Update the user profile.
     */
    public function updateProfile(UpdateProfileRequest $request): RedirectResponse
    {
        $user = auth()->user();
        $user->update($request->validated());

        return redirect()->route('account.profile')
            ->with('success', 'Profil został zaktualizowany.');
    }

    /**
     * Show the password change form.
     */
    public function showPasswordForm(): View
    {
        return view('account.password');
    }

    /**
     * Update the user password.
     */
    public function updatePassword(PasswordUpdateRequest $request): RedirectResponse
    {
        $user = auth()->user();
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('account.password')
            ->with('success', 'Hasło zostało zmienione.');
    }

    /**
     * Display user addresses.
     */
    public function addresses(): View
    {
        $addresses = auth()->user()->addresses()->orderBy('is_default', 'desc')->get();
        return view('account.addresses', compact('addresses'));
    }

    /**
     * Show the address creation form.
     */
    public function createAddress(): View
    {
        return view('account.address-form', ['address' => null]);
    }

    /**
     * Store a new address.
     */
    public function storeAddress(AddressRequest $request): RedirectResponse
    {
        $user = auth()->user();
        
        // If this is set as default, reset other addresses
        if ($request->is_default) {
            $user->addresses()->update(['is_default' => false]);
        }
        
        $user->addresses()->create($request->validated());

        return redirect()->route('account.addresses')
            ->with('success', 'Adres został dodany.');
    }

    /**
     * Show the address edit form.
     */
    public function editAddress(Address $address): View|RedirectResponse
    {
        // Ensure address belongs to authenticated user
        if ($address->user_id !== auth()->id()) {
            return redirect()->route('account.addresses')
                ->with('error', 'Nie masz dostępu do tego adresu.');
        }

        return view('account.address-form', compact('address'));
    }

    /**
     * Update an existing address.
     */
    public function updateAddress(AddressRequest $request, Address $address): RedirectResponse
    {
        // Ensure address belongs to authenticated user
        if ($address->user_id !== auth()->id()) {
            return redirect()->route('account.addresses')
                ->with('error', 'Nie masz dostępu do tego adresu.');
        }

        $user = auth()->user();
        
        // If this is set as default, reset other addresses
        if ($request->is_default) {
            $user->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
        }
        
        $address->update($request->validated());

        return redirect()->route('account.addresses')
            ->with('success', 'Adres został zaktualizowany.');
    }

    /**
     * Delete an address.
     */
    public function destroyAddress(Address $address): RedirectResponse
    {
        // Ensure address belongs to authenticated user
        if ($address->user_id !== auth()->id()) {
            return redirect()->route('account.addresses')
                ->with('error', 'Nie masz dostępu do tego adresu.');
        }

        $address->delete();

        return redirect()->route('account.addresses')
            ->with('success', 'Adres został usunięty.');
    }

    /**
     * Set an address as default.
     */
    public function setDefaultAddress(Address $address): RedirectResponse
    {
        // Ensure address belongs to authenticated user
        if ($address->user_id !== auth()->id()) {
            return redirect()->route('account.addresses')
                ->with('error', 'Nie masz dostępu do tego adresu.');
        }

        $user = auth()->user();
        $user->addresses()->update(['is_default' => false]);
        $address->update(['is_default' => true]);

        return redirect()->route('account.addresses')
            ->with('success', 'Adres domyślny został zmieniony.');
    }

    /**
     * Display user orders.
     */
    public function orders(): View
    {
        $orders = auth()->user()->orders()
            ->with(['items.product', 'shipping'])
            ->latest()
            ->paginate(10);
            
        return view('account.orders', compact('orders'));
    }

    /**
     * Show order details.
     */
    public function showOrder(int $orderId): View|RedirectResponse
    {
        $order = auth()->user()->orders()
            ->with(['items.product', 'shipping'])
            ->find($orderId);

        if (!$order) {
            return redirect()->route('account.orders')
                ->with('error', 'Zamówienie nie zostało znalezione.');
        }

        return view('account.order-detail', compact('order'));
    }

    /**
     * Display user wishlist.
     */
    public function wishlist(): View
    {
        $wishlist = auth()->user()->wishlist()
            ->with(['product.primaryImage', 'product.category'])
            ->get();
            
        return view('account.wishlist', compact('wishlist'));
    }

    /**
     * Add product to wishlist.
     */
    public function addToWishlist(Request $request): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $user = auth()->user();
        $productId = $request->product_id;

        // Check if already in wishlist
        $exists = $user->wishlist()->where('product_id', $productId)->exists();
        
        if (!$exists) {
            $user->wishlist()->create(['product_id' => $productId]);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Produkt dodany do ulubionych.',
                'count' => $user->wishlist()->count(),
            ]);
        }

        return redirect()->back()
            ->with('success', 'Produkt został dodany do ulubionych.');
    }

    /**
     * Remove product from wishlist.
     */
    public function removeFromWishlist(Request $request): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $user = auth()->user();
        $user->wishlist()->where('product_id', $request->product_id)->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Produkt usunięty z ulubionych.',
                'count' => $user->wishlist()->count(),
            ]);
        }

        return redirect()->back()
            ->with('success', 'Produkt został usunięty z ulubionych.');
    }
}