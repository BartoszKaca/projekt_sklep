@extends('layouts.shop')

@section('title', $category->name)

@section('content')
<div class="container section">
    <div class="section-header">
        <h2 class="section-title">{{ $category->name }}</h2>
        @if(!empty($category->description))
            <p class="section-subtitle">{{ $category->description }}</p>
        @endif
    </div>

    <div class="products-grid">
        @forelse($products as $product)
            <div class="product-card">
                <div class="product-image">
                    @if($product->primaryImage)
                        <img src="{{ asset('storage/' . $product->primaryImage->path) }}" alt="{{ $product->name }}">
                    @else
                        <i class="fas fa-compact-disc"></i>
                    @endif
                </div>
                <div class="product-info">
                    <div class="product-category">{{ $category->name }}</div>
                    <h3 class="product-name">{{ $product->name }}</h3>
                    <div class="product-price">
                        <span class="price-current">{{ number_format($product->getFinalPrice(), 2) }} zł</span>
                    </div>
                </div>
            </div>
        @empty
            <div style="grid-column: 1/-1; text-align: center; padding: 4rem; color: var(--gray);">
                <p>Brak produktów w tej kategorii.</p>
            </div>
        @endforelse
    </div>

    <div style="margin-top: 2rem;">{{ $products->links() }}</div>
</div>
@endsection