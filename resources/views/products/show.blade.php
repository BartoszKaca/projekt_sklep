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
                    <label for="variant">Wybierz wariant</label>
                    <select id="variant" style="display:block; margin-top:0.5rem; padding:0.75rem; border-radius:8px; width:100%;">
                        <option value="">-- wybierz --</option>
                        @foreach($product->variants as $variant)
                            <option value="{{ $variant->id }}" data-price="{{ $variant->price ?? $product->getFinalPrice() }}">{{ $variant->name ?? ($variant->sku ?? 'Wariant') }} @if($variant->price) - {{ number_format($variant->price,2) }} zł @endif (stan: {{ $variant->stock }})</option>
                        @endforeach
                    </select>
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
    async function addToCart(productId) {
        const variantSelect = document.getElementById('variant');
        const variantId = variantSelect ? variantSelect.value : '';
        const quantity = parseInt(document.getElementById('quantity').value || 1, 10);

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