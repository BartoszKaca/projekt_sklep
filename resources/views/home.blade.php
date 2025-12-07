@extends('layouts.shop')

@section('title', 'Strona główna')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/home.css') }}">
@endpush

@section('content')

{{-- Alert weryfikacji emaila --}}
@auth
@if(!auth()->user()->hasVerifiedEmail())
<div class="container">
    <div class="alert alert-warning" style="display: flex; align-items: center; gap: 1rem; background: linear-gradient(135deg, var(--warning) 0%, #f59e0b 100%); color: white; padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem;">
        <i class="fas fa-exclamation-triangle" style="font-size: 1.5rem;"></i>
        <div style="flex: 1;">
            <strong>Zweryfikuj swój email!</strong>
            <p style="margin-top: 0.5rem; margin-bottom: 0; font-size: 0.95rem;">Aby wykonywać zakupy, musisz potwierdzić swój adres email.</p>
        </div>
        <a href="{{ route('verify.email.form') }}" class="btn-secondary" style="white-space: nowrap;">
            Weryfikuj teraz
        </a>
    </div>
</div>
@endif
@endauth

{{-- Sekcja Hero --}}
<section class="hero">
    <div class="container">
        <div class="hero-content">
            <div class="hero-text">
                <h1>
                    Witaj w świecie <br>
                    <span class="highlight">polskiego rapu</span>
                </h1>
                <p>
                    Największy wybór płyt, vinylu i limitowanych edycji.
                    Wspieraj swoich ulubionych artystów i zbuduj swoją kolekcję!
                </p>
                <div class="hero-buttons">
                    <a href="#nowosci" class="btn btn-primary">
                        <i class="fas fa-fire"></i> Zobacz nowości
                    </a>
                    <a href="{{ route('products.index') }}" class="btn btn-secondary">
                        <i class="fas fa-tags"></i> Promocje
                    </a>
                </div>
            </div>

            <div class="hero-image">
                <div class="hero-card">
                    <i class="fas fa-compact-disc"></i>
                    <h3>500+</h3>
                    <p>Albumów w ofercie</p>
                </div>
                <div class="hero-card">
                    <i class="fas fa-tshirt"></i>
                    <h3>Merch</h3>
                    <p>Oficjalny merch</p>
                </div>
                <div class="hero-card">
                    <i class="fas fa-shipping-fast"></i>
                    <h3>24h</h3>
                    <p>Szybka wysyłka</p>
                </div>
                <div class="hero-card">
                    <i class="fas fa-star"></i>
                    <h3>100%</h3>
                    <p>Oryginalne wydania</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Wyróżnione produkty --}}
<section class="section" id="nowosci">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">🔥 Wyróżnione produkty</h2>
            <p class="section-subtitle">Najgorętsze nowości w naszym sklepie</p>
        </div>

        <div class="products-grid">
            @forelse($featuredProducts ?? [] as $product)
            <div class="product-card" data-product-id="{{ $product->id }}" data-slug="{{ $product->slug }}">
                @if($product->discount_price)
                <span class="product-badge sale">-{{ $product->getDiscountPercentage() }}%</span>
                @elseif($product->created_at->gt(now()->subDays(7)))
                <span class="product-badge new">Nowość</span>
                @endif

                <a href="{{ route('products.show', $product->slug) }}" class="product-image-link">
                    <div class="product-image">
                        @if($product->primaryImage)
                        <img src="{{ asset('storage/' . $product->primaryImage->path) }}" alt="{{ $product->name }}">
                        @else
                        <i class="fas fa-compact-disc"></i>
                        @endif

                        <div class="product-actions">
                            <button class="product-action-btn" onclick="event.preventDefault(); event.stopPropagation(); addToCart('{{ $product->id }}', {{ ($product->variants && $product->variants->count() > 0) ? 'true' : 'false' }})">
                                <i class="fas fa-shopping-bag"></i> Dodaj
                            </button>
                            <button class="product-action-btn icon-only" onclick="event.preventDefault(); event.stopPropagation(); toggleWishlist('{{ $product->id }}')">
                                <i class="far fa-heart"></i>
                            </button>
                        </div>
                    </div>
                </a>

                <div class="product-info">
                    <div class="product-category">{{ $product->category->name ?? 'Brak kategorii' }}</div>
                    <h3 class="product-name">
                        <a href="{{ route('products.show', $product->slug) }}">{{ $product->name }}</a>
                    </h3>
                    @if($product->artist)
                    <div class="product-artist">{{ $product->artist }}</div>
                    @endif

                    <div class="product-price">
                        <span class="price-current">{{ number_format($product->getFinalPrice(), 2) }} zł</span>
                        @if($product->discount_price)
                        <span class="price-old">{{ number_format($product->price, 2) }} zł</span>
                        @endif
                    </div>

                    <div class="product-rating">
                        <div class="stars">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star"></i>
                            @endfor
                        </div>
                        <span class="rating-count">({{ $product->reviews->count() }})</span>
                    </div>
                </div>
            </div>
            @empty
            <div style="grid-column: 1/-1; text-align: center; padding: 4rem; color: var(--gray);">
                <i class="fas fa-box-open" style="font-size: 4rem; margin-bottom: 1rem; opacity: 0.3;"></i>
                <p>Wkrótce dodamy nowe produkty!</p>
            </div>
            @endforelse
        </div>

        <div style="text-align: center; margin-top: 3rem;">
            <a href="{{ route('products.index') }}" class="btn btn-primary">
                Zobacz wszystkie produkty <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</section>

{{-- Kategorie --}}
<section class="section" style="background: white;">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Przeglądaj kategorie</h2>
            <p class="section-subtitle">Znajdź to czego szukasz</p>
        </div>

        <div class="categories-grid">
            <div class="category-card" style="background: linear-gradient(135deg, #6366f1, #4f46e5);">
                <i class="fas fa-compact-disc"></i>
                <h3>Płyty CD</h3>
                <p>{{ \App\Models\Product::where('format', 'CD')->count() }} produktów</p>
            </div>

            <div class="category-card" style="background: linear-gradient(135deg, #ec4899, #db2777);">
                <i class="fas fa-record-vinyl"></i>
                <h3>Winyle</h3>
                <p>{{ \App\Models\Product::where('format', 'Vinyl')->count() }} produktów</p>
            </div>

            <div class="category-card" style="background: linear-gradient(135deg, #10b981, #059669);">
                <i class="fas fa-tshirt"></i>
                <h3>Odzież</h3>
                <p>{{ \App\Models\Product::where('type', 'merch')->where('format', 'Clothing')->count() }} produktów</p>
            </div>

            <div class="category-card" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                <i class="fas fa-hat-cowboy"></i>
                <h3>Akcesoria</h3>
                <p>{{ \App\Models\Product::where('format', 'Accessories')->count() }} produktów</p>
            </div>
        </div>
    </div>
</section>

{{-- Banner promocyjny --}}
<section class="container">
    <div class="promo-banner">
        <div class="promo-content">
            <h2>🎁 Zapisz się na newsletter!</h2>
            <p>Otrzymaj 10% zniżki na pierwsze zakupy</p>
            <form class="newsletter-form" style="max-width: 400px;" onsubmit="subscribeNewsletter(event)">
                <input type="email" name="email" placeholder="Twój email" required>
                <button type="submit">Zapisz się</button>
            </form>
        </div>
        <div style="position: relative; z-index: 1;">
            <i class="fas fa-gift" style="font-size: 8rem; opacity: 0.3;"></i>
        </div>
    </div>
</section>

{{-- Ostatnio dodane --}}
<section class="section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Ostatnio dodane</h2>
            <p class="section-subtitle">Najnowsze albumy i merch</p>
        </div>

        <div class="products-grid">
            @foreach($latestProducts ?? [] as $product)
            <div class="product-card" data-product-id="{{ $product->id }}" data-slug="{{ $product->slug }}">
                <span class="product-badge new">Nowość</span>

                <a href="{{ route('products.show', $product->slug) }}" class="product-image-link">
                    <div class="product-image">
                        @if($product->primaryImage)
                        <img src="{{ asset('storage/' . $product->primaryImage->path) }}" alt="{{ $product->name }}">
                        @else
                        <i class="fas fa-compact-disc"></i>
                        @endif
                        <div class="product-actions">
                            <button class="product-action-btn" onclick="event.preventDefault(); event.stopPropagation(); addToCart('{{ $product->id }}', {{ ($product->variants && $product->variants->count() > 0) ? 'true' : 'false' }})">
                                <i class="fas fa-shopping-bag"></i> Dodaj
                            </button>
                            <button class="product-action-btn icon-only" onclick="event.preventDefault(); event.stopPropagation(); toggleWishlist('{{ $product->id }}')">
                                <i class="far fa-heart"></i>
                            </button>
                        </div>
                    </div>
                </a>

                <div class="product-info">
                    <div class="product-category">{{ $product->category->name ?? 'Brak kategorii' }}</div>
                    <h3 class="product-name">
                        <a href="{{ route('products.show', $product->slug) }}">{{ $product->name }}</a>
                    </h3>
                    @if($product->artist)
                    <div class="product-artist">{{ $product->artist }}</div>
                    @endif

                    <div class="product-price">
                        <span class="price-current">{{ number_format($product->getFinalPrice(), 2) }} zł</span>
                        @if($product->discount_price)
                        <span class="price-old">{{ number_format($product->price, 2) }} zł</span>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

@push('scripts')
<script>
    // Dodaj produkt do koszyka
    async function addToCart(productId, hasVariants = false) {
        // Jeśli produkt ma warianty, przekieruj na stronę produktu
        if (hasVariants) {
            // Znajdź slug produktu z atrybutu data
            const productCard = document.querySelector(`[data-product-id="${productId}"]`);
            const productSlug = productCard?.dataset?.slug;
            const productLink = productCard?.querySelector('a.product-image-link');
            
            if (productSlug) {
                window.location.href = `/produkt/${productSlug}`;
            } else if (productLink) {
                window.location.href = productLink.href;
            } else {
                window.location.href = `/produkt/${productId}`;
            }
            return;
        }

        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (!token) {
            alert('Błąd: Brak tokenu CSRF');
            return;
        }
        
        try {
            const cartAddUrl = @json(route('cart.add')) || '/cart/add';
            
            if (!cartAddUrl) {
                console.error('Cart add route not found');
                alert('Błąd konfiguracji: brak ścieżki do koszyka');
                return;
            }
            
            const res = await fetch(cartAddUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    product_id: productId,
                    variant_id: null,
                    quantity: 1
                })
            });

            // Sprawdź czy to błąd 404
            if (res.status === 404) {
                console.error('Route not found:', cartAddUrl);
                alert('Błąd: Nie znaleziono ścieżki do koszyka. Proszę odświeżyć stronę.');
                return;
            }

            let data;
            try {
                data = await res.json();
            } catch (jsonError) {
                console.error('JSON parse error:', jsonError);
                alert('Błąd: Nieprawidłowa odpowiedź z serwera.');
                return;
            }

            if (data.success) {
                showNotification('Produkt dodany do koszyka!', 'success');
                
                // Aktualizuj licznik
                const cartCount = document.getElementById('cart-count');
                if (cartCount && data.cart_count) {
                    cartCount.textContent = data.cart_count;
                }
            } else {
                // Sprawdź czy produkt wymaga wariantu
                if (data.requires_variant || data.redirect_url) {
                    if (data.redirect_url) {
                        window.location.href = data.redirect_url;
                    } else if (data.product_slug) {
                        window.location.href = `/produkt/${data.product_slug}`;
                    }
                    return;
                }
                showNotification(data.message || 'Wystąpił błąd', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showNotification('Wystąpił błąd podczas dodawania do koszyka', 'error');
        }
    }

    // Funkcje toggleWishlist i showNotification są zdefiniowane globalnie w layoutcie
</script>
@endpush
@endsection
