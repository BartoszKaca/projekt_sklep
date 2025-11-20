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
                <a href="{{ route('product.show', $product->slug) }}">
                    <div class="product-image">
                        @if(optional($product->primaryImage)->path)
                            <img src="{{ asset('storage/' . $product->primaryImage->path) }}" alt="{{ $product->name }}">
                        @else
                            <i class="fas fa-compact-disc"></i>
                        @endif
                    </div>
                </a>

                <div class="product-info">
                    <div class="product-category">{{ $category->name }}</div>
                    <a href="{{ route('product.show', $product->slug) }}">
                        <h3 class="product-name">{{ $product->name }}</h3>
                    </a>
                    <div class="product-price">
                        <span class="price-current">{{ number_format($product->getFinalPrice() ?? $product->price, 2) }} zł</span>
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