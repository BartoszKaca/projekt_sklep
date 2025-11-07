@extends('layouts.shop')

@section('title', 'Strona główna')

@push('styles')
<style>
    /* Hero Section */
    .hero {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        color: white;
        padding: 4rem 0;
        position: relative;
        overflow: hidden;
    }

    .hero::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 50%;
        height: 100%;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 600"><circle cx="900" cy="300" r="400" fill="rgba(99,102,241,0.1)"/><circle cx="1100" cy="100" r="300" fill="rgba(236,72,153,0.1)"/></svg>') no-repeat center;
        background-size: cover;
    }

    .hero-content {
        position: relative;
        z-index: 1;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 4rem;
        align-items: center;
    }

    .hero-text h1 {
        font-size: 3.5rem;
        font-weight: 900;
        line-height: 1.1;
        margin-bottom: 1.5rem;
    }

    .hero-text .highlight {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .hero-text p {
        font-size: 1.25rem;
        color: rgba(255,255,255,0.8);
        margin-bottom: 2rem;
        line-height: 1.6;
    }

    .hero-buttons {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .btn {
        padding: 1rem 2rem;
        border-radius: 50px;
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        border: none;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: white;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 24px rgba(99,102,241,0.3);
    }

    .btn-secondary {
        background: rgba(255,255,255,0.1);
        color: white;
        border: 2px solid rgba(255,255,255,0.3);
    }

    .btn-secondary:hover {
        background: rgba(255,255,255,0.2);
    }

    .hero-image {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }

    .hero-card {
        background: rgba(255,255,255,0.1);
        backdrop-filter: blur(10px);
        border-radius: 16px;
        padding: 1.5rem;
        text-align: center;
        transition: all 0.3s;
    }

    .hero-card:hover {
        transform: translateY(-8px);
        background: rgba(255,255,255,0.15);
    }

    .hero-card i {
        font-size: 3rem;
        margin-bottom: 1rem;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .hero-card h3 {
        font-size: 1.25rem;
        margin-bottom: 0.5rem;
    }

    .hero-card p {
        color: rgba(255,255,255,0.7);
        font-size: 0.875rem;
    }

    /* Section */
    .section {
        padding: 4rem 0;
    }

    .section-header {
        text-align: center;
        margin-bottom: 3rem;
    }

    .section-title {
        font-size: 2.5rem;
        font-weight: 800;
        margin-bottom: 1rem;
    }

    .section-subtitle {
        font-size: 1.125rem;
        color: var(--gray);
    }

    /* Product Grid */
    .products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 2rem;
    }

    .product-card {
        background: white;
        border-radius: 16px;
        overflow: hidden;
        transition: all 0.3s;
        border: 1px solid var(--border);
        position: relative;
    }

    .product-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.1);
    }

    .product-badge {
        position: absolute;
        top: 1rem;
        right: 1rem;
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        z-index: 1;
    }

    .product-badge.new {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: white;
    }

    .product-badge.sale {
        background: var(--danger);
        color: white;
    }

    .product-image {
        aspect-ratio: 1;
        background: var(--light);
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }

    .product-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s;
    }

    .product-card:hover .product-image img {
        transform: scale(1.1);
    }

    .product-image i {
        font-size: 4rem;
        color: var(--gray);
        opacity: 0.3;
    }

    .product-actions {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        display: flex;
        gap: 0.5rem;
        padding: 1rem;
        background: linear-gradient(to top, rgba(0,0,0,0.6), transparent);
        opacity: 0;
        transform: translateY(20px);
        transition: all 0.3s;
    }

    .product-card:hover .product-actions {
        opacity: 1;
        transform: translateY(0);
    }

    .product-action-btn {
        flex: 1;
        padding: 0.75rem;
        border-radius: 8px;
        border: none;
        background: white;
        color: var(--dark);
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }

    .product-action-btn:hover {
        background: var(--primary);
        color: white;
    }

    .product-action-btn.icon-only {
        flex: 0;
        width: 44px;
    }

    .product-info {
        padding: 1.5rem;
    }

    .product-category {
        font-size: 0.75rem;
        color: var(--gray);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.5rem;
    }

    .product-name {
        font-size: 1.125rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        color: var(--dark);
    }

    .product-artist {
        font-size: 0.875rem;
        color: var(--gray);
        margin-bottom: 1rem;
    }

    .product-price {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .price-current {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--primary);
    }

    .price-old {
        font-size: 1rem;
        color: var(--gray);
        text-decoration: line-through;
    }

    .product-rating {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-top: 0.75rem;
    }

    .stars {
        display: flex;
        gap: 0.125rem;
    }

    .stars i {
        color: #f59e0b;
        font-size: 0.875rem;
    }

    .rating-count {
        font-size: 0.875rem;
        color: var(--gray);
    }

    /* Categories Section */
    .categories-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
    }

    .category-card {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        border-radius: 16px;
        padding: 2rem;
        color: white;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s;
        position: relative;
        overflow: hidden;
    }

    .category-card::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 300px;
        height: 300px;
        border-radius: 50%;
        background: rgba(255,255,255,0.1);
        transform: translate(-50%, -50%);
    }

    .category-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 24px rgba(99,102,241,0.3);
    }

    .category-card i {
        font-size: 3rem;
        margin-bottom: 1rem;
        position: relative;
        z-index: 1;
    }

    .category-card h3 {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        position: relative;
        z-index: 1;
    }

    .category-card p {
        opacity: 0.9;
        position: relative;
        z-index: 1;
    }

    /* Banner */
    .promo-banner {
        background: linear-gradient(135deg, #6366f1 0%, #ec4899 100%);
        border-radius: 24px;
        padding: 3rem;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin: 4rem 0;
        position: relative;
        overflow: hidden;
    }

    .promo-banner::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 500px;
        height: 500px;
        border-radius: 50%;
        background: rgba(255,255,255,0.1);
    }

    .promo-content {
        position: relative;
        z-index: 1;
    }

    .promo-content h2 {
        font-size: 2.5rem;
        font-weight: 900;
        margin-bottom: 1rem;
    }

    .promo-content p {
        font-size: 1.25rem;
        margin-bottom: 2rem;
        opacity: 0.9;
    }

    @media (max-width: 768px) {
        .hero-content {
            grid-template-columns: 1fr;
        }

        .hero-text h1 {
            font-size: 2.5rem;
        }

        .hero-image {
            display: none;
        }

        .products-grid {
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 1rem;
        }

        .promo-banner {
            flex-direction: column;
            text-align: center;
            padding: 2rem;
        }

        .promo-content h2 {
            font-size: 1.75rem;
        }
    }
</style>
@endpush

@section('content')
<!-- Hero Section -->
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
                    <a href="#" class="btn btn-secondary">
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

<!-- Featured Products -->
<section class="section" id="nowosci">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">🔥 Wyróżnione produkty</h2>
            <p class="section-subtitle">Najgorętsze nowości w naszym sklepie</p>
        </div>

        <div class="products-grid">
            @forelse($featuredProducts ?? [] as $product)
            <div class="product-card">
                @if($product->discount_price)
                <span class="product-badge sale">-{{ $product->getDiscountPercentage() }}%</span>
                @elseif($product->created_at->gt(now()->subDays(7)))
                <span class="product-badge new">Nowość</span>
                @endif

                <div class="product-image">
                    @if($product->primaryImage)
                    <img src="{{ asset('storage/' . $product->primaryImage->path) }}" alt="{{ $product->name }}">
                    @else
                    <i class="fas fa-compact-disc"></i>
                    @endif

                    <div class="product-actions">
                        <button class="product-action-btn" onclick="addToCart('{{ $product->id }}')">
                            <i class="fas fa-shopping-bag"></i> Dodaj
                        </button>
                        <button class="product-action-btn icon-only" onclick="toggleWishlist('{{ $product->id }}')">
                            <i class="far fa-heart"></i>
                        </button>
                    </div>
                </div>

                <div class="product-info">
                    <div class="product-category">{{ $product->category->name }}</div>
                    <h3 class="product-name">{{ $product->name }}</h3>
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
            <a href="#" class="btn btn-primary">
                Zobacz wszystkie produkty <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</section>

<!-- Categories -->
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

<!-- Promo Banner -->
<section class="container">
    <div class="promo-banner">
        <div class="promo-content">
            <h2>🎁 Zapisz się na newsletter!</h2>
            <p>Otrzymaj 10% zniżki na pierwsze zakupy</p>
            <form class="newsletter-form" style="max-width: 400px;">
                <input type="email" placeholder="Twój email" required>
                <button type="submit">Zapisz się</button>
            </form>
        </div>
        <div style="position: relative; z-index: 1;">
            <i class="fas fa-gift" style="font-size: 8rem; opacity: 0.3;"></i>
        </div>
    </div>
</section>

<!-- Latest Products -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Ostatnio dodane</h2>
            <p class="section-subtitle">Najnowsze albumy i merch</p>
        </div>

        <div class="products-grid">
            @foreach($latestProducts ?? [] as $product)
            <div class="product-card">
                <span class="product-badge new">Nowość</span>

                <div class="product-image">
                    <i class="fas fa-compact-disc"></i>
                    <div class="product-actions">
                        <button class="product-action-btn">
                            <i class="fas fa-shopping-bag"></i> Dodaj
                        </button>
                        <button class="product-action-btn icon-only">
                            <i class="far fa-heart"></i>
                        </button>
                    </div>
                </div>

                <div class="product-info">
                    <div class="product-category">{{ $product->category->name }}</div>
                    <h3 class="product-name">{{ $product->name }}</h3>
                    @if($product->artist)
                    <div class="product-artist">{{ $product->artist }}</div>
                    @endif

                    <div class="product-price">
                        <span class="price-current">{{ number_format($product->price, 2) }} zł</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

@push('scripts')
<script>
    function addToCart(productId) {
        console.log('Add to cart:', productId);
        // Implementacja dodawania do koszyka
        alert('Produkt dodany do koszyka!');
    }

    function toggleWishlist(productId) {
        console.log('Toggle wishlist:', productId);
        // Implementacja listy życzeń
    }
</script>
@endpush
@endsection

<!-- 
Zapisz jako: resources/views/home.blade.php
-->