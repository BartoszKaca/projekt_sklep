@extends('layouts.shop')

@section('title', 'Moje zamówienia')

@push('styles')
<style>
    .account-grid {
        display: grid;
        grid-template-columns: 280px 1fr;
        gap: 2rem;
        padding: 2rem 0;
    }
    
    .account-sidebar {
        background: white;
        border-radius: 16px;
        border: 1px solid var(--border);
        padding: 1.5rem;
        height: fit-content;
    }
    
    .user-info {
        text-align: center;
        padding-bottom: 1.5rem;
        border-bottom: 1px solid var(--border);
        margin-bottom: 1.5rem;
    }
    
    .user-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 2rem;
        font-weight: 700;
        margin: 0 auto 1rem;
    }
    
    .user-name {
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
    }
    
    .user-email {
        color: var(--gray);
        font-size: 0.875rem;
    }
    
    .account-nav {
        list-style: none;
    }
    
    .account-nav li {
        margin-bottom: 0.5rem;
    }
    
    .account-nav a {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.875rem 1rem;
        border-radius: 8px;
        color: var(--dark);
        text-decoration: none;
        transition: all 0.2s;
    }
    
    .account-nav a:hover {
        background: var(--light);
    }
    
    .account-nav a.active {
        background: var(--primary);
        color: white;
    }
    
    .account-nav i {
        width: 20px;
        text-align: center;
    }
    
    .account-content {
        background: white;
        border-radius: 16px;
        border: 1px solid var(--border);
        padding: 2rem;
    }
    
    .section-title {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid var(--border);
    }
    
    .order-card {
        background: var(--light);
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1rem;
    }
    
    .order-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid var(--border);
    }
    
    .order-number {
        font-size: 1.125rem;
        font-weight: 700;
        color: var(--primary);
    }
    
    .order-date {
        color: var(--gray);
        font-size: 0.875rem;
    }
    
    .order-status {
        padding: 0.375rem 1rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
    }
    
    .order-status.pending { background: #fef3c7; color: #92400e; }
    .order-status.confirmed { background: #dbeafe; color: #1e40af; }
    .order-status.processing { background: #dbeafe; color: #1e40af; }
    .order-status.shipped { background: #c7d2fe; color: #3730a3; }
    .order-status.delivered { background: #d1fae5; color: #065f46; }
    .order-status.cancelled { background: #fee2e2; color: #991b1b; }
    
    .order-items {
        margin-bottom: 1rem;
    }
    
    .order-item {
        display: flex;
        justify-content: space-between;
        padding: 0.5rem 0;
        font-size: 0.9375rem;
    }
    
    .order-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 1rem;
        border-top: 1px solid var(--border);
    }
    
    .order-total {
        font-size: 1.25rem;
        font-weight: 700;
    }
    
    .btn-view {
        padding: 0.5rem 1rem;
        background: var(--primary);
        color: white;
        border: none;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.875rem;
        transition: all 0.2s;
    }
    
    .btn-view:hover {
        background: var(--primary-dark);
    }
    
    .empty-state {
        text-align: center;
        padding: 3rem;
    }
    
    .empty-state i {
        font-size: 4rem;
        color: var(--gray);
        opacity: 0.3;
        margin-bottom: 1rem;
    }
    
    @media (max-width: 768px) {
        .account-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="account-grid">
        <aside class="account-sidebar">
            <div class="user-info">
                <div class="user-avatar">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="user-name">{{ auth()->user()->name }}</div>
                <div class="user-email">{{ auth()->user()->email }}</div>
            </div>
            
            <ul class="account-nav">
                <li>
                    <a href="{{ route('account.dashboard') }}">
                        <i class="fas fa-home"></i> Dashboard
                    </a>
                </li>
                <li>
                    <a href="{{ route('account.orders') }}" class="active">
                        <i class="fas fa-shopping-bag"></i> Moje zamówienia
                    </a>
                </li>
                <li>
                    <a href="{{ route('account.wishlist') }}">
                        <i class="fas fa-heart"></i> Ulubione
                    </a>
                </li>
                <li>
                    <a href="{{ route('account.edit') }}">
                        <i class="fas fa-user-edit"></i> Edytuj profil
                    </a>
                </li>
                <li>
                    <a href="{{ route('account.password') }}">
                        <i class="fas fa-lock"></i> Zmień hasło
                    </a>
                </li>
            </ul>
        </aside>
        
        <div class="account-content">
            <h2 class="section-title">Moje zamówienia</h2>
            
            @forelse($orders as $order)
            <div class="order-card">
                <div class="order-header">
                    <div>
                        <div class="order-number">#{{ $order->order_number }}</div>
                        <div class="order-date">{{ $order->created_at->format('d.m.Y H:i') }}</div>
                    </div>
                    <span class="order-status {{ $order->status }}">{{ $order->status }}</span>
                </div>
                
                <div class="order-items">
                    @foreach($order->items->take(3) as $item)
                    <div class="order-item">
                        <span>{{ $item->product_name }} x{{ $item->quantity }}</span>
                        <span>{{ number_format($item->total, 2) }} zł</span>
                    </div>
                    @endforeach
                    @if($order->items->count() > 3)
                    <div class="order-item" style="color: var(--gray);">
                        ... i {{ $order->items->count() - 3 }} więcej
                    </div>
                    @endif
                </div>
                
                <div class="order-footer">
                    <div class="order-total">Razem: {{ number_format($order->total, 2) }} zł</div>
                    <a href="{{ route('account.orders.show', $order) }}" class="btn-view">
                        <i class="fas fa-eye"></i> Szczegóły
                    </a>
                </div>
            </div>
            @empty
            <div class="empty-state">
                <i class="fas fa-shopping-bag"></i>
                <h3>Brak zamówień</h3>
                <p style="color: var(--gray);">Nie złożyłeś jeszcze żadnego zamówienia.</p>
                <a href="{{ route('products.index') }}" class="btn btn-primary" style="margin-top: 1rem;">
                    Rozpocznij zakupy
                </a>
            </div>
            @endforelse
            
            @if($orders->hasPages())
            <div style="margin-top: 2rem;">
                {{ $orders->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection