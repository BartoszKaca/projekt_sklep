@extends('layouts.shop')

@section('title', 'Produkty')

@push('styles')
<style>
    .products-page {
        display: grid;
        grid-template-columns: 280px 1fr;
        gap: 2rem;
        padding: 2rem 0;
    }

    /* Filters Sidebar */
    .filters-sidebar {
        position: sticky;
        top: 100px;
        height: fit-content;
    }

    .filter-section {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        margin-bottom: 1rem;
        border: 1px solid var(--border);
    }

    .filter-title {
        font-weight: 700;
        font-size: 1.125rem;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .filter-group {
        margin-bottom: 1rem;
    }

    .filter-group:last-child {
        margin-bottom: 0;
    }

    .filter-label {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s;
    }

    .filter-label:hover {
        background: var(--light);
    }

    .filter-label input[type="checkbox"],
    .filter-label input[type="radio"] {
        width: 20px;
        height: 20px;
        cursor: pointer;
    }

    .filter-label span {
        flex: 1;
    }

    .filter-count {
        color: var(--gray);
        font-size: 0.875rem;
    }

    .price-range {
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }

    .price-input {
        flex: 1;
        padding: 0.75rem;
        border: 1px solid var(--border);
        border-radius: 8px;
        font-size: 0.875rem;
    }

    .price-input:focus {
        outline: none;
        border-color: var(--primary);
    }

    .filter-btn {
        width: 100%;
        padding: 0.875rem;
        border-radius: 8px;
        border: none;
        background: var(--primary);
        color: white;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        margin-top: 1rem;
    }

    .filter-btn:hover {
        background: var(--primary-dark);
    }

    .clear-filters {
        width: 100%;
        padding: 0.875rem;
        border-radius: 8px;
        border: 1px solid var(--border);
        background: white;
        color: var(--dark);
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }

    .clear-filters:hover {
        background: var(--light);
    }

    /* Products Area */
    .products-area {
        min-height: 100vh;
    }

    .products-header {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        border: 1px solid var(--border);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .results-count {
        font-size: 1.125rem;
        font-weight: 600;
    }

    .view-sort {
        display: flex;
        gap: 1rem;
        align-items: center;
    }

    .view-toggle {
        display: flex;
        gap: 0.5rem;
    }

    .view-btn {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        border: 1px solid var(--border);
        background: white;
        cursor: pointer;
        transition: all 0.2s;
    }

    .view-btn.active {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
    }

    .sort-select {
        padding: 0.75rem 1rem;
        border: 1px solid var(--border);
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
    }

    .sort-select:focus {
        outline: none;
        border-color: var(--primary);
    }

    /* Active Filters */
    .active-filters {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }

    .filter-tag {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        background: var(--primary);
        color: white;
        border-radius: 50px;
        font-size: 0.875rem;
        font-weight: 500;
    }

    .filter-tag button {
        background: none;
        border: none;
        color: white;
        cursor: pointer;
        font-size: 1rem;
        padding: 0;
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        transition: background 0.2s;
    }

    .filter-tag button:hover {
        background: rgba(255,255,255,0.2);
    }

    /* Product Grid Views */
    .products-grid.grid-view {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 1.5rem;
    }

    .products-grid.list-view {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .product-card.list-view {
        display: grid;
        grid-template-columns: 200px 1fr auto;
        gap: 1.5rem;
    }

    .product-card.list-view .product-image {
        aspect-ratio: 1;
    }

    .product-card.list-view .product-info {
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .product-card.list-view .product-actions {
        position: static;
        opacity: 1;
        transform: none;
        background: none;
        flex-direction: column;
        justify-content: center;
    }

    /* Pagination */
    .pagination {
        display: flex;
        justify-content: center;
        gap: 0.5rem;
        margin-top: 3rem;
    }

    .page-btn {
        width: 44px;
        height: 44px;
        border-radius: 8px;
        border: 1px solid var(--border);
        background: white;
        cursor: pointer;
        transition: all 0.2s;
        font-weight: 600;
    }

    .page-btn:hover {
        border-color: var(--primary);
        color: var(--primary);
    }

    .page-btn.active {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
    }

    .page-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    /* Mobile */
    @media (max-width: 1024px) {
        .products-page {
            grid-template-columns: 1fr;
        }

        .filters-sidebar {
            position: static;
        }

        .products-grid.grid-view {
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
        }

        .product-card.list-view {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="container">
    <!-- Breadcrumb -->
    <div style="padding: 1rem 0; color: var(--gray); font-size: 0.875rem;">
        <a href="{{ route('home') }}" style="color: var(--primary); text-decoration: none;">Strona główna</a>
        <i class="fas fa-chevron-right" style="margin: 0 0.5rem; font-size: 0.75rem;"></i>
        <span>Produkty</span>
    </div>

    <div class="products-page">
        <!-- Filters Sidebar -->
        <aside class="filters-sidebar">
            <form method="GET" id="filters-form">
                <!-- Categories -->
                <div class="filter-section">
                    <h3 class="filter-title">
                        <i class="fas fa-tags"></i>
                        Kategorie
                    </h3>
                    @foreach($categories ?? [] as $category)
                    <div class="filter-group">
                        <label class="filter-label">
                            <input type="checkbox" name="categories[]" value="{{ $category->id }}" 
                                   {{ in_array($category->id, request('categories', [])) ? 'checked' : '' }}>
                            <span>{{ $category->name }}</span>
                            <span class="filter-count">({{ $category->products_count ?? 0 }})</span>
                        </label>
                    </div>
                    @endforeach
                </div>

                <!-- Type -->
                <div class="filter-section">
                    <h3 class="filter-title">
                        <i class="fas fa-layer-group"></i>
                        Typ produktu
                    </h3>
                    <div class="filter-group">
                        <label class="filter-label">
                            <input type="radio" name="type" value="" {{ !request('type') ? 'checked' : '' }}>
                            <span>Wszystkie</span>
                        </label>
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">
                            <input type="radio" name="type" value="album" {{ request('type') == 'album' ? 'checked' : '' }}>
                            <span>Płyty</span>
                            <span class="filter-count">({{ \App\Models\Product::where('type', 'album')->count() }})</span>
                        </label>
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">
                            <input type="radio" name="type" value="merch" {{ request('type') == 'merch' ? 'checked' : '' }}>
                            <span>Merch</span>
                            <span class="filter-count">({{ \App\Models\Product::where('type', 'merch')->count() }})</span>
                        </label>
                    </div>
                </div>

                <!-- Price Range -->
                <div class="filter-section">
                    <h3 class="filter-title">
                        <i class="fas fa-dollar-sign"></i>
                        Cena
                    </h3>
                    <div class="price-range">
                        <input type="number" name="price_min" class="price-input" 
                               placeholder="Od" value="{{ request('price_min') }}">
                        <span>-</span>
                        <input type="number" name="price_max" class="price-input" 
                               placeholder="Do" value="{{ request('price_max') }}">
                    </div>
                </div>

                <!-- Format (for albums) -->
                <div class="filter-section">
                    <h3 class="filter-title">
                        <i class="fas fa-compact-disc"></i>
                        Format
                    </h3>
                    <div class="filter-group">
                        <label class="filter-label">
                            <input type="checkbox" name="formats[]" value="CD" 
                                   {{ in_array('CD', request('formats', [])) ? 'checked' : '' }}>
                            <span>CD</span>
                        </label>
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">
                            <input type="checkbox" name="formats[]" value="Vinyl" 
                                   {{ in_array('Vinyl', request('formats', [])) ? 'checked' : '' }}>
                            <span>Vinyl</span>
                        </label>
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">
                            <input type="checkbox" name="formats[]" value="Cassette" 
                                   {{ in_array('Cassette', request('formats', [])) ? 'checked' : '' }}>
                            <span>Kaseta</span>
                        </label>
                    </div>
                </div>

                <!-- Availability -->
                <div class="filter-section">
                    <h3 class="filter-title">
                        <i class="fas fa-box"></i>
                        Dostępność
                    </h3>
                    <div class="filter-group">
                        <label class="filter-label">
                            <input type="checkbox" name="in_stock" value="1" 
                                   {{ request('in_stock') ? 'checked' : '' }}>
                            <span>Dostępne</span>
                        </label>
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">
                            <input type="checkbox" name="on_sale" value="1" 
                                   {{ request('on_sale') ? 'checked' : '' }}>
                            <span>W promocji</span>
                        </label>
                    </div>
                </div>

                <button type="submit" class="filter-btn">
                    <i class="fas fa-filter"></i> Zastosuj filtry
                </button>
                <button type="button" onclick="clearFilters()" class="clear-filters" style="margin-top: 0.5rem;">
                    <i class="fas fa-times"></i> Wyczyść
                </button>
            </form>
        </aside>

        <!-- Products Area -->
        <div class="products-area">
            <!-- Products Header -->
            <div class="products-header">
                <div class="results-count">
                    Znaleziono <strong>{{ $products->total() }}</strong> produktów
                </div>

                <div class="view-sort">
                    <div class="view-toggle">
                        <button class="view-btn active" onclick="setView('grid')" title="Siatka">
                            <i class="fas fa-th"></i>
                        </button>
                        <button class="view-btn" onclick="setView('list')" title="Lista">
                            <i class="fas fa-list"></i>
                        </button>
                    </div>

                    <select class="sort-select" onchange="updateSort(this.value)">
                        <option value="">Sortuj: Domyślnie</option>
                        <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Cena: rosnąco</option>
                        <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Cena: malejąco</option>
                        <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Nazwa: A-Z</option>
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Najnowsze</option>
                    </select>
                </div>
            </div>

            <!-- Active Filters -->
            @if(request()->hasAny(['categories', 'type', 'formats', 'price_min', 'price_max', 'in_stock', 'on_sale']))
            <div class="active-filters">
                @foreach(request('categories', []) as $catId)
                    @php $cat = $categories->find($catId); @endphp
                    @if($cat)
                    <span class="filter-tag">
                        {{ $cat->name }}
                        <button type="button" onclick="removeFilter('categories[]', '{{ $catId }}')">×</button>
                    </span>
                    @endif
                @endforeach

                @if(request('type'))
                <span class="filter-tag">
                    {{ request('type') == 'album' ? 'Płyty' : 'Merch' }}
                    <button type="button" onclick="removeFilter('type')">×</button>
                </span>
                @endif

                @if(request('price_min') || request('price_max'))
                <span class="filter-tag">
                    Cena: {{ request('price_min', 0) }} - {{ request('price_max', '∞') }} zł
                    <button type="button" onclick="removeFilter('price_min'); removeFilter('price_max')">×</button>
                </span>
                @endif

                @if(request('on_sale'))
                <span class="filter-tag">
                    Promocje
                    <button type="button" onclick="removeFilter('on_sale')">×</button>
                </span>
                @endif
            </div>
            @endif

            <!-- Products Grid -->
            <div class="products-grid grid-view" id="products-grid">
                @forelse($products as $product)
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

                        @if(!$product->isInStock())
                        <div style="color: var(--danger); font-size: 0.875rem; margin-top: 0.5rem;">
                            <i class="fas fa-times-circle"></i> Brak w magazynie
                        </div>
                        @endif
                    </div>
                </div>
                @empty
                <div style="grid-column: 1/-1; text-align: center; padding: 4rem; background: white; border-radius: 16px;">
                    <i class="fas fa-search" style="font-size: 4rem; color: var(--gray); opacity: 0.3; margin-bottom: 1rem;"></i>
                    <h3>Nie znaleziono produktów</h3>
                    <p style="color: var(--gray);">Spróbuj zmienić filtry lub kategorie</p>
                </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($products->hasPages())
            <div class="pagination">
                {{ $products->appends(request()->query())->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    function setView(view) {
        const grid = document.getElementById('products-grid');
        const cards = grid.querySelectorAll('.product-card');
        const buttons = document.querySelectorAll('.view-btn');

        buttons.forEach(btn => btn.classList.remove('active'));
        event.currentTarget.classList.add('active');

        if (view === 'list') {
            grid.classList.remove('grid-view');
            grid.classList.add('list-view');
            cards.forEach(card => card.classList.add('list-view'));
        } else {
            grid.classList.remove('list-view');
            grid.classList.add('grid-view');
            cards.forEach(card => card.classList.remove('list-view'));
        }
    }

    function updateSort(sort) {
        const url = new URL(window.location);
        if (sort) {
            url.searchParams.set('sort', sort);
        } else {
            url.searchParams.delete('sort');
        }
        window.location = url;
    }

    function removeFilter(name, value) {
        const form = document.getElementById('filters-form');
        const inputs = form.querySelectorAll(`[name="${name}"]`);
        
        inputs.forEach(input => {
            if (value === undefined || input.value == value) {
                if (input.type === 'checkbox' || input.type === 'radio') {
                    input.checked = false;
                } else {
                    input.value = '';
                }
            }
        });
        
        form.submit();
    }

    function clearFilters() {
        window.location = '{{ route("products.index") }}';
    }

    function addToCart(productId) {
        // TODO: Implementacja koszyka
        alert('Produkt dodany do koszyka!');
    }

    function toggleWishlist(productId) {
        // TODO: Implementacja listy życzeń
        console.log('Toggle wishlist:', productId);
    }
</script>
@endpush
@endsection

<!-- 
Zapisz jako: resources/views/products/index.blade.php
-->