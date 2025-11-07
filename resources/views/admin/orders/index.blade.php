{{-- resources/views/admin/orders/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Zamówienia')

@push('styles')
<style>
    .orders-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .stat-card-small {
        background: white;
        padding: 1.25rem;
        border-radius: 12px;
        border: 1px solid var(--border);
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: all 0.2s;
    }

    .stat-card-small:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .stat-icon-small {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }

    .orders-filters {
        background: white;
        padding: 1.5rem;
        border-radius: 12px;
        border: 1px solid var(--border);
        margin-bottom: 1.5rem;
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .status-tabs {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .status-tab {
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
    }

    .status-tab:hover {
        border-color: var(--primary);
        background: rgba(99,102,241,0.05);
    }

    .status-tab.active {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
    }

    .order-row {
        background: white;
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 1.25rem;
        margin-bottom: 1rem;
        transition: all 0.2s;
    }

    .order-row:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }

    .order-header {
        display: grid;
        grid-template-columns: 1.5fr 1fr 1fr 1fr auto;
        gap: 1rem;
        align-items: center;
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid var(--light-gray);
    }

    .order-number {
        font-weight: 700;
        font-size: 1.125rem;
        color: var(--primary);
    }

    .order-date {
        font-size: 0.875rem;
        color: var(--gray);
    }

    .order-body {
        display: grid;
        grid-template-columns: 1.5fr 1fr 1fr 1fr auto;
        gap: 1rem;
        align-items: center;
    }

    .order-customer {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .customer-avatar {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
    }

    .customer-info h4 {
        font-weight: 600;
        margin-bottom: 0.125rem;
    }

    .customer-info p {
        font-size: 0.875rem;
        color: var(--gray);
    }

    .order-amount {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--dark);
    }

    .payment-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.375rem 0.75rem;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .payment-badge.paid {
        background: #d1fae5;
        color: #065f46;
    }

    .payment-badge.pending {
        background: #fef3c7;
        color: #92400e;
    }

    .quick-actions {
        display: flex;
        gap: 0.5rem;
    }

    @media (max-width: 1024px) {
        .order-header, .order-body {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <div class="breadcrumb">
        <a href="{{ route('admin.dashboard') }}">Admin</a>
        <span>/</span>
        <span>Zamówienia</span>
    </div>
    <h1 class="page-title">Zamówienia</h1>
    <p class="page-subtitle">Zarządzaj zamówieniami klientów</p>
</div>

<!-- Stats -->
<div class="orders-stats">
    <div class="stat-card-small">
        <div class="stat-icon-small" style="background: #dbeafe; color: #1e40af;">
            <i class="fas fa-shopping-cart"></i>
        </div>
        <div>
            <div style="font-size: 0.75rem; color: var(--gray); margin-bottom: 0.25rem;">Wszystkie</div>
            <div style="font-size: 1.5rem; font-weight: 700;">{{ $orders->total() }}</div>
        </div>
    </div>

    <div class="stat-card-small">
        <div class="stat-icon-small" style="background: #fef3c7; color: #92400e;">
            <i class="fas fa-clock"></i>
        </div>
        <div>
            <div style="font-size: 0.75rem; color: var(--gray); margin-bottom: 0.25rem;">Oczekujące</div>
            <div style="font-size: 1.5rem; font-weight: 700;">
                {{ \App\Models\Order::where('status', 'pending')->count() }}
            </div>
        </div>
    </div>

    <div class="stat-card-small">
        <div class="stat-icon-small" style="background: #d1fae5; color: #065f46;">
            <i class="fas fa-truck"></i>
        </div>
        <div>
            <div style="font-size: 0.75rem; color: var(--gray); margin-bottom: 0.25rem;">W dostawie</div>
            <div style="font-size: 1.5rem; font-weight: 700;">
                {{ \App\Models\Order::where('status', 'shipped')->count() }}
            </div>
        </div>
    </div>

    <div class="stat-card-small">
        <div class="stat-icon-small" style="background: #fee2e2; color: #991b1b;">
            <i class="fas fa-times-circle"></i>
        </div>
        <div>
            <div style="font-size: 0.75rem; color: var(--gray); margin-bottom: 0.25rem;">Anulowane</div>
            <div style="font-size: 1.5rem; font-weight: 700;">
                {{ \App\Models\Order::where('status', 'cancelled')->count() }}
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<form method="GET" class="orders-filters">
    <div class="filter-group" style="flex: 1; min-width: 200px;">
        <input type="text" name="search" class="filter-input" 
               placeholder="Szukaj po numerze, kliencie..." 
               value="{{ request('search') }}">
    </div>

    <div class="status-tabs">
        <a href="{{ route('admin.orders.index') }}" 
           class="status-tab {{ !request('status') ? 'active' : '' }}">
            Wszystkie
        </a>
        <a href="{{ route('admin.orders.index') }}?status=pending" 
           class="status-tab {{ request('status') == 'pending' ? 'active' : '' }}">
            Oczekujące
        </a>
        <a href="{{ route('admin.orders.index') }}?status=confirmed" 
           class="status-tab {{ request('status') == 'confirmed' ? 'active' : '' }}">
            Potwierdzone
        </a>
        <a href="{{ route('admin.orders.index') }}?status=shipped" 
           class="status-tab {{ request('status') == 'shipped' ? 'active' : '' }}">
            W dostawie
        </a>
        <a href="{{ route('admin.orders.index') }}?status=delivered" 
           class="status-tab {{ request('status') == 'delivered' ? 'active' : '' }}">
            Dostarczone
        </a>
    </div>
</form>

<!-- Orders List -->
@forelse($orders as $order)
<div class="order-row">
    <div class="order-header">
        <div>
            <div class="order-number">#{{ $order->order_number }}</div>
            <div class="order-date">{{ $order->created_at->format('d.m.Y H:i') }}</div>
        </div>
        <div>
            <span class="status-badge {{ $order->status }}">{{ $order->status }}</span>
        </div>
        <div>
            <span class="payment-badge {{ $order->payment_status }}">
                <i class="fas fa-{{ $order->payment_status == 'paid' ? 'check' : 'clock' }}"></i>
                {{ $order->payment_status }}
            </span>
        </div>
        <div class="order-amount">{{ number_format($order->total, 2) }} zł</div>
        <div class="quick-actions">
            <a href="{{ route('admin.orders.show', $order) }}" class="action-btn edit" title="Szczegóły">
                <i class="fas fa-eye"></i>
            </a>
        </div>
    </div>

    <div class="order-body">
        <div class="order-customer">
            <div class="customer-avatar">
                {{ strtoupper(substr($order->user->name ?? 'G', 0, 1)) }}
            </div>
            <div class="customer-info">
                <h4>{{ $order->user->name ?? 'Gość' }}</h4>
                <p>{{ $order->user->email ?? '-' }}</p>
            </div>
        </div>

        <div>
            <div style="font-size: 0.75rem; color: var(--gray); margin-bottom: 0.25rem;">Produktów</div>
            <div style="font-weight: 600;">{{ $order->items->count() }}</div>
        </div>

        <div>
            <div style="font-size: 0.75rem; color: var(--gray); margin-bottom: 0.25rem;">Płatność</div>
            <div style="font-weight: 600; text-transform: uppercase;">{{ $order->payment_method }}</div>
        </div>

        <div>
            @if($order->tracking_number)
            <div style="font-size: 0.75rem; color: var(--gray); margin-bottom: 0.25rem;">Tracking</div>
            <div style="font-weight: 600; font-family: monospace;">{{ $order->tracking_number }}</div>
            @endif
        </div>
    </div>
</div>
@empty
<div class="empty-state" style="background: white; padding: 4rem; border-radius: 16px; border: 1px solid var(--border);">
    <i class="fas fa-inbox"></i>
    <h3>Brak zamówień</h3>
    <p>Nie znaleziono zamówień pasujących do kryteriów</p>
</div>
@endforelse

<!-- Pagination -->
@if($orders->hasPages())
<div style="display: flex; justify-content: center; margin-top: 2rem;">
    {{ $orders->links() }}
</div>
@endif
@endsection