@extends('layouts.shop')

@section('title','Ulubione')
@section('title', 'Ulubione')

@push('styles')
<style>
    .account-container {
        display: grid;
        grid-template-columns: 250px 1fr;
        gap: 2rem;
        margin-top: 2rem;
    }
    
    .account-sidebar {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        border: 1px solid var(--border);
        height: fit-content;
    }
    
    .user-info {
        text-align: center;
        padding-bottom: 1.5rem;
        border-bottom: 1px solid var(--border);
        margin-bottom: 1.5rem;
    }
    
    .user-avatar {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        font-size: 2rem;
        color: white;
        font-weight: 700;
    }
    
    .user-name { font-size: 1.125rem; font-weight: 700; }
    .user-email { color: var(--gray); font-size: 0.875rem; }
    
    .sidebar-nav { list-style: none; }
    .sidebar-nav li { margin-bottom: 0.5rem; }
    .sidebar-nav a {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 1rem;
        border-radius: 8px;
        color: var(--dark);
        text-decoration: none;
        transition: all 0.2s;
    }
    .sidebar-nav a:hover, .sidebar-nav a.active { background: rgba(99, 102, 241, 0.1); color: var(--primary); }
    .sidebar-nav a.active { font-weight: 600; }
    .sidebar-nav i { width: 20px; text-align: center; }
    .logout-link { margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--border); }
    .logout-link a { color: var(--danger) !important; }
    
    .account-content {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        border: 1px solid var(--border);
    }
    
    .page-title { font-size: 1.5rem; font-weight: 700; margin-bottom: 2rem; }
    
    .wishlist-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 1.5rem;
    }
    
    .wishlist-card {
        background: var(--light);
        border-radius: 12px;
        overflow: hidden;
        position: relative;
    }
    
    .wishlist-image {
        aspect-ratio: 1;
        background: white;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }
    
    .wishlist-image img { width: 100%; height: 100%; object-fit: cover; }
    .wishlist-image i { font-size: 3rem; color: var(--gray); opacity: 0.3; }
    
    .wishlist-info { padding: 1rem; }
    .wishlist-category { font-size: 0.75rem; color: var(--gray); text-transform: uppercase; margin-bottom: 0.25rem; }
    .wishlist-name { font-weight: 700; margin-bottom: 0.5rem; }
    .wishlist-name a { color: var(--dark); text-decoration: none; }
    .wishlist-name a:hover { color: var(--primary); }
    .wishlist-price { font-size: 1.25rem; font-weight: 700; color: var(--primary); }
    
    .wishlist-actions {
        display: flex;
        gap: 0.5rem;
        margin-top: 1rem;
    }
    
    .btn {
        padding: 0.75rem 1rem;
        border-radius: 8px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        flex: 1;
    }
    
    .btn-primary { background: linear-gradient(135deg, var(--primary), var(--secondary)); color: white; }
    .btn-danger { background: rgba(239, 68, 68, 0.1); color: var(--danger); flex: 0; padding: 0.75rem; }
    .btn-danger:hover { background: var(--danger); color: white; }
    
    .empty-state {
        text-align: center;
        padding: 3rem;
        color: var(--gray);
    }
    
    .empty-state i { font-size: 3rem; opacity: 0.3; margin-bottom: 1rem; display: block; }
    
    .alert {
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
    }
    
    .alert-success { background: rgba(16, 185, 129, 0.1); color: var(--success); border: 1px solid var(--success); }
    
    @media (max-width: 1024px) {
        .account-container { grid-template-columns: 1fr; }
        .account-sidebar { display: none; }
    }
</style>
@endpush
@section('content')
<div class="container">
    <div class="account-container">
        <aside class="account-sidebar">
            <div class="user-info">
                <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                <div class="user-name">{{ auth()->user()->name }}</div>
                <div class="user-email">{{ auth()->user()->email }}</div>
            </div>
            <nav>
                <ul class="sidebar-nav">
                    <li><a href="{{ route('account.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Panel główny</a></li>
                    <li><a href="{{ route('account.orders') }}"><i class="fas fa-shopping-bag"></i> Zamówienia</a></li>
                    <li><a href="{{ route('account.wishlist') }}" class="active"><i class="fas fa-heart"></i> Ulubione</a></li>
                    <li><a href="{{ route('account.addresses') }}"><i class="fas fa-map-marker-alt"></i> Adresy</a></li>
                    <li><a href="{{ route('account.profile') }}"><i class="fas fa-user"></i> Profil</a></li>
                    <li><a href="{{ route('account.password') }}"><i class="fas fa-lock"></i> Zmień hasło</a></li>
                    <li class="logout-link">
                        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fas fa-sign-out-alt"></i> Wyloguj
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>
                    </li>
                </ul>
            </nav>
        </aside>

        <main class="account-content">
            <h1 class="page-title">Ulubione produkty</h1>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if($wishlist->count() > 0)
            <div class="wishlist-grid">
                @foreach($wishlist as $item)
                @if($item->product)
                <div class="wishlist-card">
                    <div class="wishlist-image">
                        @if($item->product->primaryImage)
                            <img src="{{ asset('storage/' . $item->product->primaryImage->path) }}" alt="{{ $item->product->name }}">
                        @else
                            <i class="fas fa-compact-disc"></i>
                        @endif
                    </div>
                    <div class="wishlist-info">
                        <div class="wishlist-category">{{ $item->product->category->name ?? 'Brak kategorii' }}</div>
                        <div class="wishlist-name">
                            <a href="{{ route('products.show', $item->product->slug) }}">{{ $item->product->name }}</a>
                        </div>
                        <div class="wishlist-price">{{ number_format($item->product->getFinalPrice(), 2) }} zł</div>

                        <div class="wishlist-actions">
                            <button class="btn btn-primary" onclick='addToCart({{ $item->product->id }})'>
                                <i class="fas fa-shopping-bag"></i> Do koszyka
                            </button>
                            <form action="{{ route('account.wishlist.remove') }}" method="POST" style="flex: 0;">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $item->product->id }}">
                                <button type="submit" class="btn btn-danger" title="Usuń z ulubionych">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endif
                @endforeach
            </div>
            @else
            <div class="empty-state">
                <i class="fas fa-heart"></i>
                <p>Nie masz jeszcze ulubionych produktów</p>
                <a href="{{ route('home') }}" class="btn btn-primary" style="margin-top: 1rem;">Przejdź do sklepu</a>
            </div>
            @endif
        </main>
    </div>
</div>

@push('scripts')
<script>
function addToCart(productId) {
    fetch('{{ route("cart.add") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ product_id: productId, quantity: 1 })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Produkt dodany do koszyka!');
            if (document.getElementById('cart-count')) {
                document.getElementById('cart-count').textContent = data.cart_count;
            }
        } else {
            alert(data.message || 'Wystąpił błąd');
        }
    });
}
</script>
@endpush
@endsection