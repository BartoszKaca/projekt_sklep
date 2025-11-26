@extends('layouts.shop')

@section('title', 'Zamówienie złożone')

@push('styles')
<style>
    .success-container {
        max-width: 800px;
        margin: 0 auto;
        text-align: center;
    }
    
    .success-icon {
        width: 100px;
        height: 100px;
        background: linear-gradient(135deg, #10b981, #059669);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 2rem;
        animation: scaleIn 0.5s ease;
    }
    
    .success-icon i {
        font-size: 3rem;
        color: white;
    }
    
    @keyframes scaleIn {
        0% { transform: scale(0); }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); }
    }
    
    .success-title {
        font-size: 2rem;
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 1rem;
    }
    
    .success-message {
        font-size: 1.125rem;
        color: var(--gray);
        margin-bottom: 2rem;
    }
    
    .order-box {
        background: white;
        border-radius: 12px;
        border: 1px solid var(--border);
        padding: 2rem;
        margin-bottom: 2rem;
        text-align: left;
    }
    
    .order-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid var(--border);
    }
    
    .order-number {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--primary);
    }
    
    .order-date {
        color: var(--gray);
        font-size: 0.875rem;
    }
    
    .order-items {
        margin-bottom: 1.5rem;
    }
    
    .order-item {
        display: flex;
        justify-content: space-between;
        padding: 0.75rem 0;
        border-bottom: 1px solid var(--light);
    }
    
    .order-item-name {
        font-weight: 500;
    }
    
    .order-item-qty {
        color: var(--gray);
        font-size: 0.875rem;
    }
    
    .order-totals {
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 2px solid var(--border);
    }
    
    .order-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.5rem;
    }
    
    .order-row.total {
        font-size: 1.25rem;
        font-weight: 700;
        margin-top: 1rem;
        padding-top: 0.5rem;
    }
    
    .shipping-info {
        background: var(--light);
        padding: 1.5rem;
        border-radius: 8px;
    }
    
    .shipping-info h4 {
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: 0.75rem;
    }
    
    .shipping-info p {
        margin: 0;
        color: var(--gray);
        line-height: 1.6;
    }
    
    .action-buttons {
        display: flex;
        gap: 1rem;
        justify-content: center;
    }
    
    .action-buttons .btn {
        padding: 1rem 2rem;
    }
</style>
@endpush

@section('content')
<div class="container section">
    <div class="success-container">
        <div class="success-icon">
            <i class="fas fa-check"></i>
        </div>
        
        <h1 class="success-title">Dziękujemy za zamówienie!</h1>
        <p class="success-message">
            Twoje zamówienie zostało przyjęte do realizacji. 
            @if($order->shipping && $order->shipping->email)
                Potwierdzenie wysłaliśmy na adres: <strong>{{ $order->shipping->email }}</strong>
            @endif
        </p>
        
        <div class="order-box">
            <div class="order-header">
                <div>
                    <div class="order-number">#{{ $order->order_number }}</div>
                    <div class="order-date">{{ $order->created_at->format('d.m.Y H:i') }}</div>
                </div>
                <span class="status-badge" style="padding: 0.5rem 1rem; background: #fef3c7; color: #92400e; border-radius: 20px; font-weight: 600; font-size: 0.875rem;">
                    Oczekujące
                </span>
            </div>
            
            <div class="order-items">
                @foreach($order->items as $item)
                <div class="order-item">
                    <div>
                        <div class="order-item-name">{{ $item->product_name }}</div>
                        <div class="order-item-qty">Ilość: {{ $item->quantity }}</div>
                    </div>
                    <div style="font-weight: 600;">{{ number_format($item->total, 2) }} zł</div>
                </div>
                @endforeach
            </div>
            
            <div class="order-totals">
                <div class="order-row">
                    <span>Produkty</span>
                    <span>{{ number_format($order->subtotal, 2) }} zł</span>
                </div>
                <div class="order-row">
                    <span>Dostawa</span>
                    <span>{{ $order->shipping_cost > 0 ? number_format($order->shipping_cost, 2) . ' zł' : 'Bezpłatnie' }}</span>
                </div>
                <div class="order-row total">
                    <span>Razem</span>
                    <span>{{ number_format($order->total, 2) }} zł</span>
                </div>
            </div>
            
            @if($order->shipping)
            <div class="shipping-info" style="margin-top: 1.5rem;">
                <h4><i class="fas fa-map-marker-alt"></i> Adres dostawy</h4>
                <p>
                    {{ $order->shipping->first_name }} {{ $order->shipping->last_name }}<br>
                    {{ $order->shipping->street_address }}
                    @if($order->shipping->apartment), {{ $order->shipping->apartment }}@endif<br>
                    {{ $order->shipping->postal_code }} {{ $order->shipping->city }}<br>
                    {{ $order->shipping->country }}
                </p>
            </div>
            @endif
        </div>
        
        <div class="action-buttons">
            <a href="{{ route('home') }}" class="btn btn-primary">
                <i class="fas fa-shopping-bag"></i> Kontynuuj zakupy
            </a>
            @auth
            <a href="{{ route('account.orders') }}" class="btn btn-secondary">
                <i class="fas fa-list"></i> Moje zamówienia
            </a>
            @endauth
        </div>
    </div>
</div>
@endsection
