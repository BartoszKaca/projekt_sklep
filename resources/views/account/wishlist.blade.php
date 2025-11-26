@extends('layouts.shop')

@section('title', 'Ulubione')

@push('styles')
<style>
    .account-grid {
        display: grid;
        grid-template-columns: 280px 1fr;
        gap: 2rem;
        padding: 2rem 0;
    }
    
    .account-sidebar {
        background: white;
        border-radius: 16px;
        border: 1px solid var(--border);
        padding: 1.5rem;
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
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 2rem;
        font-weight: 700;
        margin: 0 auto 1rem;
    }
    
    .user-name {
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
    }
    
    .user-email {
        color: var(--gray);
        font-size: 0.875rem;
    }
    
    .account-nav {
        list-style: none;
    }
    
    .account-nav li {
        margin-bottom: 0.5rem;
    }
    
    .account-nav a {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.875rem 1rem;
        border-radius: 8px;
        color: var(--dark);
        text-decoration: none;
        transition: all 0.2s;
    }
    
    .account-nav a:hover {
        background: var(--light);
    }
    
    .account-nav a.active {
        background: var(--primary);
        color: white;
    }
    
    .account-nav i {
        width: 20px;
        text-align: center;
    }
    
    .account-content {
        background: white;
        border-radius: 16px;
        border: 1px solid var(--border);
        padding: 2rem;
    }
    
    .section-title {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid var(--border);
    }
    
    .wishlist-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
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
    }
    
    .wishlist-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .wishlist-image i {
        font-size: 3rem;
        color: var(--gray);
        opacity: 0.3;
    }
    
    .wishlist-info {
        padding: 1rem;
    }
    
    .wishlist-name {
        font-weight: 600;
        margin-bottom: 0.25rem;
    }
    
    .wishlist-name a {
        color: var(--dark);
        text-decoration: none;
    }
    
    .wishlist-name a:hover {
        color: var(--primary);
    }
    
    .wishlist-artist {
        font-size: 0.875rem;
        color: var(--gray);
        margin-bottom: 0.5rem;
    }
    
    .wishlist-price {
        font-size: 1.125rem;
        font-weight: 700;
        color: var(--primary);
    }
    
    .wishlist-actions {
        display: flex;
        gap: 0.5rem;
        margin-top: 1rem;
    }
    
    .btn-add-cart {
        flex: 1;
        padding: 0.5rem;
        background: var(--primary);
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .btn-add-cart:hover {
        background: var(--primary-dark);
    }
    
    .btn-remove {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        border: 1px solid var(--border);
        background: white;
        color: var(--danger);
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .btn-remove:hover {
        background: var(--danger);
        color: white;
        border-color: var(--danger);
    }
    
    .empty-state {
        text-align: center;
        padding: 3rem;
    }
    
    .empty-state i {
        font-size: 4rem;
        color: var(--gray);
        opacity: 0.3;
        margin-bottom: 1rem;
    }
    
    .alert {
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1rem;
    }
    
    .alert-success {
        background: #d1fae5;
        color: #065f46;
    }
    
    @media (max-width: 768px) {
        .account-grid {
            grid-template-columns: 1fr;
        }
        
        .wishlist-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
</style>
@endpush

@section('content')
<div class="container">
    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <div class="account-grid">
        <aside class="account-sidebar">
            <div class="user-info">
                <div class="user-avatar">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="user-name">{{ auth()->user()->name }}</div>
                <div class="user-email">{{ auth()->user()->email }}</div>
            </div>
            
            <ul class="account-nav">
                <li>
                    <a href="{{ route('account.dashboard') }}">
                        <i class="fas fa-home"></i> Dashboard
                    </a>
                </li>
                <li>
                    <a href="{{ route('account.orders') }}">
                        <i class="fas fa-shopping-bag"></i> Moje zamówienia
                    </a>
                </li>
                <li>
                    <a href="{{ route('account.wishlist') }}" class="active">
                        <i class="fas fa-heart"></i> Ulubione
                    </a>
                </li>
                <li>
                    <a href="{{ route('account.edit') }}">
                        <i class="fas fa-user-edit"></i> Edytuj profil
                    </a>
                </li>
                <li>
                    <a href="{{ route('account.password') }}">
                        <i class="fas fa-lock"></i> Zmień hasło
                    </a>
                </li>
            </ul>
        </aside>
        
        <div class="account-content">
            <h2 class="section-title">Ulubione ({{ $wishlist->count() }})</h2>
            
            @if($wishlist->isEmpty())
            <div class="empty-state">
                <i class="fas fa-heart"></i>
                <h3>Brak ulubionych</h3>
                <p style="color: var(--gray);">Dodaj produkty do ulubionych, aby je tutaj zobaczyć.</p>
                <a href="{{ route('products.index') }}" class="btn btn-primary" style="margin-top: 1rem;">
                    Przeglądaj produkty
                </a>
            </div>
            @else
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
                        <div class="wishlist-name">
                            <a href="{{ route('products.show', $item->product->slug) }}">{{ $item->product->name }}</a>
                        </div>
                        @if($item->product->artist)
                        <div class="wishlist-artist">{{ $item->product->artist }}</div>
                        @endif
                        <div class="wishlist-price">{{ number_format($item->product->getFinalPrice(), 2) }} zł</div>
                        
                        <div class="wishlist-actions">
                            <button class="btn-add-cart" onclick="addToCart('{{ $item->product->id }}')">
                                <i class="fas fa-shopping-bag"></i> Dodaj
                            </button>
                            <form action="{{ route('wishlist.remove', $item->id) }}" method="POST" style="margin: 0;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-remove" title="Usuń z ulubionych">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endif
                @endforeach
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    async function addToCart(productId) {
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        try {
            const res = await fetch("{{ route('cart.add') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: 1
                })
            });
            
            const data = await res.json();
            if (data.success) {
                const el = document.getElementById('cart-count');
                if (el) el.textContent = data.cart_count ?? el.textContent;
                alert(data.message || 'Dodano do koszyka!');
            } else {
                alert(data.message || 'Błąd');
            }
        } catch (err) {
            console.error(err);
            alert('Błąd komunikacji z serwerem.');
        }
    }
</script>
@endpush
@endsection