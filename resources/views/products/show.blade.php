@extends('layouts.shop')

@section('title', $product->name)

@section('content')
<div class="container section">
    <div class="section-header">
        <h2 class="section-title">{{ $product->name }}</h2>
        <p class="section-subtitle">{{ $product->artist ?? '' }}</p>
    </div>

    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:2rem; align-items:start;">
        <div>
            @if(optional($product->primaryImage)->path)
                <img src="{{ asset('storage/' . $product->primaryImage->path) }}" alt="{{ $product->name }}" style="width:100%; border-radius:12px;">
            @else
                <div style="background:var(--light); padding:4rem; text-align:center; border-radius:12px;">
                    <i class="fas fa-compact-disc" style="font-size:4rem; color:var(--gray)"></i>
                </div>
            @endif

            @if($product->images && $product->images->count() > 1)
                <div style="display:flex; gap:0.5rem; margin-top:1rem;">
                    @foreach($product->images as $img)
                        <img src="{{ asset('storage/' . $img->path) }}" alt="" style="width:72px; height:72px; object-fit:cover; border-radius:8px;">
                    @endforeach
                </div>
            @endif
        </div>

        <div>
            <div style="font-size:1.5rem; font-weight:800; color:var(--primary);">
                {{ number_format($product->getFinalPrice() ?? $product->price, 2) }} zł
            </div>

            <p style="margin-top:1rem;">{!! nl2br(e($product->description)) !!}</p>

            @if($product->variants && $product->variants->count())
                <div style="margin-top:1rem;">
                    <label for="variant">Wersja:</label>
                    <select id="variant" style="display:block; margin-top:0.5rem; padding:0.75rem; border-radius:8px;">
                        @foreach($product->variants as $variant)
                            <option value="{{ $variant->id }}">{{ $variant->name }} @if($variant->price) - {{ number_format($variant->price,2) }} zł @endif (stan: {{ $variant->stock }})</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div style="margin-top:1.5rem;">
                <button class="btn btn-primary" onclick="addToCart('{{ $product->id }}')">
                    <i class="fas fa-shopping-bag"></i> Dodaj do koszyka
                </button>
            </div>
        </div>
    </div>
</div>
@endsection