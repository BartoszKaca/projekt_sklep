{{-- resources/views/admin/reviews/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Opinie klientów')

@push('styles')
<style>
    .reviews-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .review-card {
        background: white;
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1rem;
        transition: all 0.2s;
    }

    .review-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }

    .review-card.pending {
        border-left: 4px solid var(--warning);
    }

    .review-card.approved {
        border-left: 4px solid var(--success);
    }

    .review-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 1rem;
    }

    .review-user {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .review-user-avatar {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
    }

    .review-user-info h4 {
        font-weight: 600;
        margin-bottom: 0.25rem;
    }

    .review-user-info p {
        font-size: 0.875rem;
        color: var(--gray);
    }

    .review-rating {
        display: flex;
        gap: 0.25rem;
    }

    .review-rating i {
        color: #f59e0b;
        font-size: 1.125rem;
    }

    .review-product {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        background: var(--light-gray);
        border-radius: 10px;
        margin-bottom: 1rem;
    }

    .review-product-img {
        width: 60px;
        height: 60px;
        border-radius: 8px;
        background: white;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--gray);
    }

    .review-product-info h5 {
        font-weight: 600;
        margin-bottom: 0.25rem;
    }

    .review-product-info p {
        font-size: 0.875rem;
        color: var(--gray);
    }

    .review-content {
        margin-bottom: 1rem;
    }

    .review-title {
        font-weight: 600;
        font-size: 1.125rem;
        margin-bottom: 0.5rem;
        color: var(--dark);
    }

    .review-text {
        color: var(--gray);
        line-height: 1.6;
    }

    .review-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 1rem;
        border-top: 1px solid var(--border);
    }

    .review-meta {
        font-size: 0.875rem;
        color: var(--gray);
    }

    .review-meta i {
        margin-right: 0.25rem;
    }

    .review-actions {
        display: flex;
        gap: 0.5rem;
    }

    .filter-tabs {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
    }

    .filter-tab {
        padding: 0.625rem 1rem;
        border-radius: 8px;
        border: 1px solid var(--border);
        background: white;
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        color: var(--dark);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .filter-tab:hover {
        border-color: var(--primary);
        background: rgba(99,102,241,0.05);
    }

    .filter-tab.active {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
    }

    .verified-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.25rem 0.75rem;
        background: #d1fae5;
        color: #065f46;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <div class="breadcrumb">
        <a href="{{ route('admin.dashboard') }}">Admin</a>
        <span>/</span>
        <span>Opinie</span>
    </div>
    <h1 class="page-title">Opinie klientów</h1>
    <p class="page-subtitle">Moderuj i zarządzaj recenzjami produktów</p>
</div>

<!-- Stats -->
<div class="reviews-stats">
    <div class="stat-card-small">
        <div class="stat-icon-small" style="background: #dbeafe; color: #1e40af;">
            <i class="fas fa-star"></i>
        </div>
        <div>
            <div style="font-size: 0.75rem; color: var(--gray); margin-bottom: 0.25rem;">Wszystkie</div>
            <div style="font-size: 1.5rem; font-weight: 700;">{{ $reviews->total() }}</div>
        </div>
    </div>

    <div class="stat-card-small">
        <div class="stat-icon-small" style="background: #fef3c7; color: #92400e;">
            <i class="fas fa-clock"></i>
        </div>
        <div>
            <div style="font-size: 0.75rem; color: var(--gray); margin-bottom: 0.25rem;">Oczekujące</div>
            <div style="font-size: 1.5rem; font-weight: 700;">
                {{ \App\Models\Review::where('is_approved', false)->count() }}
            </div>
        </div>
    </div>

    <div class="stat-card-small">
        <div class="stat-icon-small" style="background: #d1fae5; color: #065f46;">
            <i class="fas fa-check-circle"></i>
        </div>
        <div>
            <div style="font-size: 0.75rem; color: var(--gray); margin-bottom: 0.25rem;">Zatwierdzone</div>
            <div style="font-size: 1.5rem; font-weight: 700;">
                {{ \App\Models\Review::where('is_approved', true)->count() }}
            </div>
        </div>
    </div>

    <div class="stat-card-small">
        <div class="stat-icon-small" style="background: #fef3c7; color: #f59e0b;">
            <i class="fas fa-star-half-alt"></i>
        </div>
        <div>
            <div style="font-size: 0.75rem; color: var(--gray); margin-bottom: 0.25rem;">Średnia ocena</div>
            <div style="font-size: 1.5rem; font-weight: 700;">
                {{ number_format(\App\Models\Review::avg('rating'), 1) }}/5
            </div>
        </div>
    </div>
</div>

<!-- Filter Tabs -->
<div class="filter-tabs">
    <a href="{{ route('admin.reviews.index') }}" 
       class="filter-tab {{ !request('status') ? 'active' : '' }}">
        <i class="fas fa-list"></i> Wszystkie
    </a>
    <a href="{{ route('admin.reviews.index') }}?status=pending" 
       class="filter-tab {{ request('status') == 'pending' ? 'active' : '' }}">
        <i class="fas fa-clock"></i> Oczekujące
        @if(\App\Models\Review::where('is_approved', false)->count() > 0)
        <span class="badge" style="background: var(--warning); color: white; padding: 0.125rem 0.5rem; border-radius: 12px;">
            {{ \App\Models\Review::where('is_approved', false)->count() }}
        </span>
        @endif
    </a>
    <a href="{{ route('admin.reviews.index') }}?status=approved" 
       class="filter-tab {{ request('status') == 'approved' ? 'active' : '' }}">
        <i class="fas fa-check"></i> Zatwierdzone
    </a>
</div>

<!-- Reviews List -->
@forelse($reviews as $review)
<div class="review-card {{ $review->is_approved ? 'approved' : 'pending' }}">
    <div class="review-header">
        <div class="review-user">
            <div class="review-user-avatar">
                {{ strtoupper(substr($review->user->name, 0, 1)) }}
            </div>
            <div class="review-user-info">
                <h4>{{ $review->user->name }}</h4>
                <p>{{ $review->created_at->format('d.m.Y H:i') }}</p>
            </div>
        </div>

        <div style="text-align: right;">
            <div class="review-rating">
                @for($i = 1; $i <= 5; $i++)
                    <i class="fas fa-star" style="color: {{ $i <= $review->rating ? '#f59e0b' : '#e5e7eb' }};"></i>
                @endfor
            </div>
            @if($review->is_verified_purchase)
            <div class="verified-badge" style="margin-top: 0.5rem;">
                <i class="fas fa-badge-check"></i> Zweryfikowany zakup
            </div>
            @endif
        </div>
    </div>

    <!-- Product Info -->
    <div class="review-product">
        <div class="review-product-img">
            <i class="fas fa-compact-disc"></i>
        </div>
        <div class="review-product-info">
            <h5>{{ $review->product->name }}</h5>
            <p>SKU: {{ $review->product->sku }}</p>
        </div>
        <a href="{{ route('admin.products.edit', $review->product) }}" class="btn btn-secondary" style="margin-left: auto;">
            <i class="fas fa-external-link-alt"></i>
        </a>
    </div>

    <!-- Review Content -->
    <div class="review-content">
        @if($review->title)
        <div class="review-title">{{ $review->title }}</div>
        @endif
        <div class="review-text">{{ $review->comment }}</div>
    </div>

    <!-- Review Footer -->
    <div class="review-footer">
        <div class="review-meta">
            <span>
                <i class="fas fa-shopping-bag"></i>
                @if($review->order)
                    Zamówienie <a href="{{ route('admin.orders.show', $review->order) }}" style="color: var(--primary);">#{{ $review->order->order_number }}</a>
                @else
                    Bez zamówienia
                @endif
            </span>
        </div>

        <div class="review-actions">
            @if(!$review->is_approved)
            <form method="POST" action="{{ route('admin.reviews.approve', $review) }}" style="margin: 0;">
                @csrf
                <button type="submit" class="btn btn-primary" style="padding: 0.5rem 1rem;">
                    <i class="fas fa-check"></i> Zatwierdź
                </button>
            </form>
            @else
            <form method="POST" action="{{ route('admin.reviews.reject', $review) }}" style="margin: 0;">
                @csrf
                <button type="submit" class="btn btn-secondary" style="padding: 0.5rem 1rem;">
                    <i class="fas fa-times"></i> Odrzuć
                </button>
            </form>
            @endif

            <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}" style="margin: 0;" 
                  onsubmit="return confirm('Czy na pewno chcesz usunąć tę opinię?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="action-btn delete" style="width: auto; padding: 0.5rem 1rem;">
                    <i class="fas fa-trash"></i>
                </button>
            </form>
        </div>
    </div>
</div>
@empty
<div class="empty-state" style="background: white; padding: 4rem; border-radius: 16px;">
    <i class="fas fa-star"></i>
    <h3>Brak opinii</h3>
    <p>Nie ma jeszcze żadnych opinii do moderacji</p>
</div>
@endforelse

<!-- Pagination -->
@if($reviews->hasPages())
<div style="display: flex; justify-content: center; margin-top: 2rem;">
    {{ $reviews->links() }}
</div>
@endif
@endsection