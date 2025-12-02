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
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function __construct()
    {
        // Wszystkie metody wymagają logowania
        $this->middleware('auth');
    }

    // Panel główny konta
    public function dashboard(): View
    {
        $user = auth()->user();
        $recentOrders = $user->orders()->latest()->take(5)->get();
        $recentOrdersCount = $recentOrders->count();
        $wishlistCount = $user->wishlist()->count();
        $addressCount = $user->addresses()->count();
        
        return view('account.dashboard', compact('user', 'recentOrders', 'recentOrdersCount', 'wishlistCount', 'addressCount'));
    }

    // Formularz edycji profilu
    public function editProfile(): View
    {
        $user = auth()->user();
        return view('account.profile', compact('user'));
    }

    // Zapisz zmiany w profilu
    public function updateProfile(UpdateProfileRequest $request): RedirectResponse
    {
        $user = auth()->user();
        $user->update($request->validated());

        return redirect()->route('account.profile')
            ->with('success', 'Profil został zaktualizowany.');
    }

    // Formularz zmiany hasła
    public function showPasswordForm(): View
    {
        return view('account.password');
    }

    // Zapisz nowe hasło
    public function updatePassword(PasswordUpdateRequest $request): RedirectResponse
    {
        $user = auth()->user();
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('account.password')
            ->with('success', 'Hasło zostało zmienione.');
    }

    // Lista adresów użytkownika
    public function addresses(): View
    {
        $addresses = auth()->user()->addresses()->orderBy('is_default', 'desc')->get();
        return view('account.addresses', compact('addresses'));
    }

    // Formularz dodawania adresu
    public function createAddress(): View
    {
        return view('account.address-form', ['address' => null]);
    }

    // Zapisz nowy adres
    public function storeAddress(AddressRequest $request): RedirectResponse
    {
        $user = auth()->user();
        
        // Jeśli to domyślny adres, usuń flagę z innych
        if ($request->is_default) {
            $user->addresses()->update(['is_default' => false]);
        }
        
        $user->addresses()->create($request->validated());

        return redirect()->route('account.addresses')
            ->with('success', 'Adres został dodany.');
    }

    // Formularz edycji adresu
    public function editAddress(Address $address): View|RedirectResponse
    {
        // Sprawdź czy adres należy do zalogowanego użytkownika
        if ($address->user_id !== auth()->id()) {
            return redirect()->route('account.addresses')
                ->with('error', 'Nie masz dostępu do tego adresu.');
        }

        return view('account.address-form', compact('address'));
    }

    // Zaktualizuj adres
    public function updateAddress(AddressRequest $request, Address $address): RedirectResponse
    {
        // Sprawdź czy adres należy do zalogowanego użytkownika
        if ($address->user_id !== auth()->id()) {
            return redirect()->route('account.addresses')
                ->with('error', 'Nie masz dostępu do tego adresu.');
        }

        $user = auth()->user();
        
        // Jeśli to domyślny adres, usuń flagę z innych
        if ($request->is_default) {
            $user->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
        }
        
        $address->update($request->validated());

        return redirect()->route('account.addresses')
            ->with('success', 'Adres został zaktualizowany.');
    }

    // Usuń adres
    public function destroyAddress(Address $address): RedirectResponse
    {
        // Sprawdź czy adres należy do zalogowanego użytkownika
        if ($address->user_id !== auth()->id()) {
            return redirect()->route('account.addresses')
                ->with('error', 'Nie masz dostępu do tego adresu.');
        }

        $address->delete();

        return redirect()->route('account.addresses')
            ->with('success', 'Adres został usunięty.');
    }

    // Ustaw adres jako domyślny
    public function setDefaultAddress(Address $address): RedirectResponse
    {
        // Sprawdź czy adres należy do zalogowanego użytkownika
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

    // Lista zamówień użytkownika
    public function orders(): View
    {
        $orders = auth()->user()->orders()
            ->with(['items.product', 'shipping'])
            ->latest()
            ->paginate(10);
            
        return view('account.orders', compact('orders'));
    }

    // Szczegóły zamówienia
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

    // Lista życzeń (wishlist)
    public function wishlist(): View
    {
        $wishlist = auth()->user()->wishlist()
            ->with(['product.primaryImage', 'product.category'])
            ->get();
            
        return view('account.wishlist', compact('wishlist'));
    }

    // Dodaj produkt do listy życzeń
    public function addToWishlist(Request $request): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $user = auth()->user();
        $productId = $request->product_id;

        // Sprawdź czy produkt już jest na liście
        $exists = $user->wishlist()->where('product_id', $productId)->exists();
        
        if (!$exists) {
            $user->wishlist()->create(['product_id' => $productId]);
        }

        // Odpowiedź AJAX
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Produkt dodany do ulubionych.',
                'count' => $user->wishlist()->count(),
            ]);
        }

        // Zwykłe przekierowanie
        return redirect()->back()
            ->with('success', 'Produkt został dodany do ulubionych.');
    }

    // Usuń produkt z listy życzeń
    public function removeFromWishlist(Request $request): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $user = auth()->user();
        $user->wishlist()->where('product_id', $request->product_id)->delete();

        // Odpowiedź AJAX
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Produkt usunięty z ulubionych.',
                'count' => $user->wishlist()->count(),
            ]);
        }

        // Zwykłe przekierowanie
        return redirect()->back()
            ->with('success', 'Produkt został usunięty z ulubionych.');
    }
}
