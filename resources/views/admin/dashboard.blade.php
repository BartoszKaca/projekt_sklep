@extends('layouts.admin')

@section('title', 'Dashboard')

@push('styles')
<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        padding: 1.5rem;
        border-radius: 16px;
        border: 1px solid var(--border);
        transition: all 0.3s;
        position: relative;
        overflow: hidden;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.1);
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 100px;
        height: 100px;
        border-radius: 50%;
        opacity: 0.1;
        transform: translate(30%, -30%);
    }

    .stat-card.primary::before { background: var(--primary); }
    .stat-card.success::before { background: var(--success); }
    .stat-card.warning::before { background: var(--warning); }
    .stat-card.danger::before { background: var(--danger); }

    .stat-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1rem;
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
    }

    .stat-icon.primary { background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); }
    .stat-icon.success { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
    .stat-icon.warning { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
    .stat-icon.danger { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }

    .stat-label {
        font-size: 0.875rem;
        color: var(--gray);
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 0.5rem;
    }

    .stat-change {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        font-size: 0.875rem;
        font-weight: 600;
        padding: 0.25rem 0.75rem;
        border-radius: 8px;
    }

    .stat-change.up {
        background: #d1fae5;
        color: #065f46;
    }

    .stat-change.down {
        background: #fee2e2;
        color: #991b1b;
    }

    .dashboard-row {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .card {
        background: white;
        padding: 1.5rem;
        border-radius: 16px;
        border: 1px solid var(--border);
    }

    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid var(--border);
    }

    .card-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--dark);
    }

    .card-action {
        color: var(--primary);
        text-decoration: none;
        font-size: 0.875rem;
        font-weight: 600;
        transition: all 0.2s;
    }

    .card-action:hover {
        color: var(--primary-dark);
    }

    .order-list {
        list-style: none;
    }

    .order-item {
        display: flex;
        align-items: center;
        padding: 1rem 0;
        border-bottom: 1px solid var(--light-gray);
        transition: all 0.2s;
    }

    .order-item:last-child {
        border-bottom: none;
    }

    .order-item:hover {
        background: var(--light-gray);
        margin: 0 -0.5rem;
        padding: 1rem 0.5rem;
        border-radius: 8px;
    }

    .order-number {
        font-weight: 600;
        color: var(--dark);
        margin-bottom: 0.25rem;
    }

    .order-customer {
        font-size: 0.875rem;
        color: var(--gray);
    }

    .order-meta {
        margin-left: auto;
        text-align: right;
    }

    .order-price {
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 0.25rem;
    }

    .status-badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .status-badge.pending {
        background: #fef3c7;
        color: #92400e;
    }

    .status-badge.confirmed {
        background: #dbeafe;
        color: #1e40af;
    }

    .status-badge.shipped {
        background: #d1fae5;
        color: #065f46;
    }

    .product-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        background: var(--light-gray);
        border-radius: 12px;
        margin-bottom: 0.75rem;
    }

    .product-rank {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--primary);
        width: 40px;
        text-align: center;
    }

    .product-info {
        flex: 1;
    }

    .product-name {
        font-weight: 600;
        color: var(--dark);
        margin-bottom: 0.25rem;
    }

    .product-sales {
        font-size: 0.875rem;
        color: var(--gray);
    }

    .low-stock-alert {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        background: #fef3c7;
        border: 1px solid #fde68a;
        border-radius: 12px;
        margin-bottom: 0.75rem;
    }

    .low-stock-alert i {
        color: #f59e0b;
        font-size: 1.25rem;
    }

    .low-stock-info {
        flex: 1;
    }

    .low-stock-name {
        font-weight: 600;
        color: var(--dark);
    }

    .low-stock-count {
        font-size: 0.875rem;
        color: var(--gray);
    }

    @media (max-width: 1024px) {
        .dashboard-row {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <div class="breadcrumb">
        <span>Admin</span>
        <span>/</span>
        <span>Dashboard</span>
    </div>
    <h1 class="page-title">Dashboard</h1>
    <p class="page-subtitle">Witaj z powrotem, {{ auth()->user()->name }}! Oto podsumowanie Twojego sklepu.</p>
</div>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card primary">
        <div class="stat-header">
            <div>
                <div class="stat-label">Zamówienia dzisiaj</div>
                <div class="stat-value">{{ $stats['today_orders'] ?? 0 }}</div>
                <span class="stat-change up">
                    <i class="fas fa-arrow-up"></i>
                    +12%
                </span>
            </div>
            <div class="stat-icon primary">
                <i class="fas fa-shopping-cart"></i>
            </div>
        </div>
    </div>

    <div class="stat-card success">
        <div class="stat-header">
            <div>
                <div class="stat-label">Przychód dzisiaj</div>
                <div class="stat-value">{{ number_format($stats['today_revenue'] ?? 0, 2) }} zł</div>
                <span class="stat-change up">
                    <i class="fas fa-arrow-up"></i>
                    +8%
                </span>
            </div>
            <div class="stat-icon success">
                <i class="fas fa-money-bill-wave"></i>
            </div>
        </div>
    </div>

    <div class="stat-card warning">
        <div class="stat-header">
            <div>
                <div class="stat-label">Oczekujące</div>
                <div class="stat-value">{{ $stats['pending_orders'] ?? 0 }}</div>
                <span class="stat-change down">
                    <i class="fas fa-arrow-down"></i>
                    -3%
                </span>
            </div>
            <div class="stat-icon warning">
                <i class="fas fa-clock"></i>
            </div>
        </div>
    </div>

    <div class="stat-card danger">
        <div class="stat-header">
            <div>
                <div class="stat-label">Niski stan</div>
                <div class="stat-value">{{ $stats['low_stock_products'] ?? 0 }}</div>
                <a href="{{ route('admin.stock.index') }}" class="stat-change down" style="text-decoration: none;">
                    <i class="fas fa-exclamation-triangle"></i>
                    Zobacz
                </a>
            </div>
            <div class="stat-icon danger">
                <i class="fas fa-box-open"></i>
            </div>
        </div>
    </div>
</div>

<!-- Dashboard Row -->
<div class="dashboard-row">
    <!-- Recent Orders -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Ostatnie zamówienia</h2>
            <a href="{{ route('admin.orders.index') }}" class="card-action">
                Zobacz wszystkie <i class="fas fa-arrow-right"></i>
            </a>
        </div>

        <ul class="order-list">
            @forelse($recent_orders ?? [] as $order)
            <li class="order-item">
                <div>
                    <div class="order-number">#{{ $order->order_number }}</div>
                    <div class="order-customer">{{ $order->user->name ?? 'Gość' }}</div>
                </div>
                <div class="order-meta">
                    <div class="order-price">{{ number_format($order->total, 2) }} zł</div>
                    <span class="status-badge {{ $order->status }}">{{ $order->status }}</span>
                </div>
            </li>
            @empty
            <li style="text-align: center; padding: 2rem; color: var(--gray);">
                <i class="fas fa-inbox" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.3;"></i>
                <p>Brak zamówień do wyświetlenia</p>
            </li>
            @endforelse
        </ul>
    </div>

    <!-- Top Products & Alerts -->
    <div>
        <!-- Top Products -->
        <div class="card" style="margin-bottom: 1.5rem;">
            <div class="card-header">
                <h2 class="card-title">Top Produkty</h2>
                <a href="{{ route('admin.reports.sales') }}" class="card-action">
                    Raport <i class="fas fa-chart-bar"></i>
                </a>
            </div>

            @forelse($top_products ?? [] as $index => $product)
            <div class="product-item">
                <div class="product-rank">#{{ $index + 1 }}</div>
                <div class="product-info">
                    <div class="product-name">{{ $product->name }}</div>
                    <div class="product-sales">
                        <i class="fas fa-shopping-bag"></i> {{ $product->order_items_count ?? 0 }} sprzedanych
                    </div>
                </div>
            </div>
            @empty
            <p style="text-align: center; padding: 2rem; color: var(--gray);">Brak danych sprzedażowych</p>
            @endforelse
        </div>

        <!-- Low Stock Alert -->
        @if(($stats['low_stock_products'] ?? 0) > 0)
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Alerty magazynowe</h2>
                <a href="{{ route('admin.stock.index') }}?status=low" class="card-action">
                    Zobacz <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            <div class="low-stock-alert">
                <i class="fas fa-exclamation-triangle"></i>
                <div class="low-stock-info">
                    <div class="low-stock-name">Produkty z niskim stanem</div>
                    <div class="low-stock-count">{{ $stats['low_stock_products'] }} produktów wymaga uzupełnienia</div>
                </div>
            </div>

            @if(($stats['out_of_stock'] ?? 0) > 0)
            <div class="low-stock-alert" style="background: #fee2e2; border-color: #fecaca;">
                <i class="fas fa-times-circle" style="color: #ef4444;"></i>
                <div class="low-stock-info">
                    <div class="low-stock-name">Brak w magazynie</div>
                    <div class="low-stock-count">{{ $stats['out_of_stock'] }} produktów bez stanu</div>
                </div>
            </div>
            @endif
        </div>
        @endif
    </div>
</div>
@endsection

<!-- 
Zapisz jako: resources/views/admin/dashboard.blade.php
-->