@extends('layouts.shop')

@section('title', $product->name)

@push('styles')
<style>
    .product-page {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
        align-items: start;
    }
    .product-main-image { border-radius: 12px; overflow: hidden; background: var(--light); }
    .product-thumbs { display:flex; gap:0.5rem; margin-top:1rem; }
    .product-thumbs img { width:72px; height:72px; object-fit:cover; border-radius:8px; }
    .product-meta { background: white; padding: 1.5rem; border-radius: 12px; border:1px solid var(--border); }
    
    /* Size buttons */
    .size-selector { margin-top: 1rem; }
    .size-selector-label { font-weight: 600; margin-bottom: 0.5rem; display: block; }
    .size-buttons { display: flex; flex-wrap: wrap; gap: 0.5rem; }
    .size-btn {
        min-width: 50px;
        padding: 0.75rem 1rem;
        border: 2px solid var(--border);
        background: white;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        text-align: center;
    }
    .size-btn:hover:not(.disabled) {
        border-color: var(--primary);
        background: rgba(99, 102, 241, 0.05);
    }
    .size-btn.selected {
        border-color: var(--primary);
        background: var(--primary);
        color: white;
    }
    .size-btn.disabled {
        opacity: 0.4;
        cursor: not-allowed;
        text-decoration: line-through;
    }
    .size-btn .stock-info {
        font-size: 0.7rem;
        font-weight: 400;
        display: block;
        margin-top: 2px;
        opacity: 0.7;
    }
    .selected-variant-info {
        margin-top: 0.75rem;
        padding: 0.75rem;
        background: var(--light-gray);
        border-radius: 8px;
        font-size: 0.875rem;
    }
    .variant-price {
        font-weight: 700;
        color: var(--primary);
    }
    
    @media (max-width: 768px) {
        .product-page { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
<div class="container section">
    <div class="section-header">
        <h2 class="section-title">{{ $product->name }}</h2>
        @if(!empty($product->artist))
            <p class="section-subtitle">{{ $product->artist }}</p>
        @endif
    </div>

    <div class="product-page">
        <div>
            <div class="product-main-image">
                @if(optional($product->primaryImage)->path)
                    <img src="{{ asset('storage/' . $product->primaryImage->path) }}" alt="{{ $product->name }}" style="width:100%; display:block;">
                @else
                    <div style="padding:6rem; text-align:center;">
                        <i class="fas fa-compact-disc" style="font-size:4rem; color:var(--gray)"></i>
                    </div>
                @endif
            </div>

            @if($product->images && $product->images->count() > 0)
                <div class="product-thumbs" style="margin-top:1rem;">
                    @foreach($product->images as $img)
                        <img src="{{ asset('storage/' . $img->path) }}" alt="">
                    @endforeach
                </div>
            @endif
        </div>

        <div class="product-meta">
            <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:1rem;">
                <div>
                    <div style="font-size:1.75rem; font-weight:800; color:var(--primary);">
                        {{ number_format($product->getFinalPrice() ?? $product->price, 2) }} zł
                    </div>
                    @if($product->discount_price)
                        <div style="color:var(--gray); text-decoration: line-through; margin-top:0.25rem;">
                            {{ number_format($product->price,2) }} zł
                        </div>
                    @endif
                </div>

                <div style="text-align:right;">
                    <div style="font-weight:700;">Stan:</div>
                    @if($product->variants && $product->variants->count())
                        <small>Warianty: {{ $product->variants->sum('stock') }} szt.</small>
                    @else
                        <small>Brak informacji o wariantach</small>
                    @endif
                </div>
            </div>

            <p style="margin-top:1rem; color:var(--gray);">{!! nl2br(e($product->short_description ?? $product->description ?? '')) !!}</p>

            <div style="margin-top:1rem;">
                @if($product->variants && $product->variants->count())
                    <input type="hidden" id="variant" value="">
                    
                    {{-- Group variants by size --}}
                    @php
                        $sizes = $product->variants->whereNotNull('size')->groupBy('size');
                        $colors = $product->variants->whereNotNull('color')->pluck('color')->unique();
                    @endphp
                    
                    @if($sizes->count() > 0)
                    <div class="size-selector">
                        <label class="size-selector-label">Wybierz rozmiar:</label>
                        <div class="size-buttons">
                            @foreach($product->variants as $variant)
                                <button type="button" 
                                        class="size-btn {{ $variant->stock_quantity <= 0 ? 'disabled' : '' }}"
                                        data-variant-id="{{ $variant->id }}"
                                        data-price="{{ $variant->getFinalPrice() }}"
                                        data-stock="{{ $variant->stock_quantity }}"
                                        data-size="{{ $variant->size }}"
                                        data-color="{{ $variant->color }}"
                                        data-modifier="{{ $variant->price_modifier }}"
                                        {{ $variant->stock_quantity <= 0 ? 'disabled' : '' }}
                                        onclick="selectSize(this)">
                                    {{ $variant->size }}
                                    @if($variant->color)
                                        <span style="font-size:0.75rem; opacity:0.7;">{{ $variant->color }}</span>
                                    @endif
                                    <span class="stock-info">
                                        @if($variant->stock_quantity > 0)
                                            {{ $variant->stock_quantity }} szt.
                                        @else
                                            Brak
                                        @endif
                                    </span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    
                    <div id="selected-variant-info" class="selected-variant-info" style="display:none;">
                        <span id="variant-details"></span>
                    </div>
                @endif

                <div style="margin-top:0.75rem;">
                    <label for="quantity">Ilość</label>
                    <input id="quantity" type="number" value="1" min="1" style="width:100px; padding:0.5rem; margin-top:0.5rem; border-radius:8px;">
                </div>
            </div>

            <div style="margin-top:1.5rem; display:flex; gap:0.5rem; align-items:center;">
                <button class="btn btn-primary btn-sm" onclick="addToCart('{{ $product->id }}')">
                    <i class="fas fa-shopping-bag"></i> Dodaj do koszyka
                </button>

                <a href="{{ route('cart.index') }}" class="btn btn-secondary btn-sm" style="display:inline-flex; align-items:center;">
                    <i class="fas fa-shopping-cart"></i>&nbsp;Przejdź do koszyka
                </a>

                <button class="btn btn-sm" style="border:1px solid var(--border); background:white;" onclick="toggleWishlist('{{ $product->id }}')">
                    <i class="far fa-heart" aria-hidden="true"></i>&nbsp;Dodaj do ulubionych
                </button>
            </div>

            <hr style="margin:1.25rem 0; border:none; border-top:1px solid var(--border)">

            <h4>Opis</h4>
            <div style="color:var(--dark); margin-top:0.5rem;">
                {!! nl2br(e($product->description ?? 'Brak opisu')) !!}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const basePrice = {{ $product->getFinalPrice() }};
    
    function selectSize(btn) {
        if (btn.disabled) return;
        
        // Remove selection from all buttons
        document.querySelectorAll('.size-btn').forEach(b => b.classList.remove('selected'));
        
        // Select this button
        btn.classList.add('selected');
        
        // Update hidden input
        document.getElementById('variant').value = btn.dataset.variantId;
        
        // Show variant info
        const infoDiv = document.getElementById('selected-variant-info');
        const detailsSpan = document.getElementById('variant-details');
        
        const size = btn.dataset.size;
        const color = btn.dataset.color;
        const price = parseFloat(btn.dataset.price);
        const stock = parseInt(btn.dataset.stock);
        const modifier = parseFloat(btn.dataset.modifier);
        
        let details = `<strong>Rozmiar: ${size}</strong>`;
        if (color) details += ` | Kolor: ${color}`;
        details += ` | Dostępne: ${stock} szt.`;
        
        if (modifier > 0) {
            details += ` | <span class="variant-price">+${modifier.toFixed(2)} zł</span>`;
        }
        
        detailsSpan.innerHTML = details;
        infoDiv.style.display = 'block';
        
        // Update main price display
        const priceDisplay = document.querySelector('.product-meta [style*="font-size:1.75rem"]');
        if (priceDisplay) {
            priceDisplay.textContent = price.toFixed(2) + ' zł';
        }
    }

    async function addToCart(productId) {
        const variantInput = document.getElementById('variant');
        const variantId = variantInput ? variantInput.value : '';
        const quantity = parseInt(document.getElementById('quantity').value || 1, 10);
        
        // Check if variant is required but not selected
        @if($product->variants && $product->variants->count())
        if (!variantId) {
            alert('Wybierz rozmiar przed dodaniem do koszyka!');
            return;
        }
        @endif

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
                    variant_id: variantId || null,
                    quantity: quantity
                })
            });

            const data = await res.json();
            if (!res.ok || !data.success) {
                alert(data.message || 'Błąd dodawania do koszyka');
                return;
            }

            // update cart count in layout
            const el = document.getElementById('cart-count');
            if (el) el.textContent = data.cart_count ?? el.textContent;

            // optional: show short toast
            alert(data.message || 'Dodano do koszyka');
        } catch (err) {
            console.error(err);
            alert('Błąd komunikacji z serwerem.');
        }
    }
</script>
@endpush