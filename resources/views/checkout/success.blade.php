@extends('layouts.shop')

@section('title', 'Zamówienie złożone')

@push('styles')
<style>
    .success-container {
        max-width: 800px;
        margin: 3rem auto;
        text-align: center;
    }
    
    .success-icon {
        width: 100px;
        height: 100px;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 2rem;
        font-size: 3rem;
        color: white;
    }
    
    .success-title {
        font-size: 2rem;
        font-weight: 800;
        margin-bottom: 1rem;
        color: var(--dark);
    }
    
    .success-subtitle {
        font-size: 1.125rem;
        color: var(--gray);
        margin-bottom: 2rem;
    }
    
    .order-details {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        margin: 2rem 0;
        border: 1px solid var(--border);
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
    }
    
    .info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
        margin-bottom: 2rem;
    }
    
    .info-section h3 {
        font-size: 0.875rem;
        text-transform: uppercase;
        color: var(--gray);
        margin-bottom: 0.5rem;
    }
    
    .info-section p {
        margin: 0;
        line-height: 1.8;
    }
    
    .order-items {
        margin-top: 1.5rem;
        border-top: 1px solid var(--border);
        padding-top: 1.5rem;
    }
    
    .order-items h3 {
        font-size: 1rem;
        margin-bottom: 1rem;
    }
    
    .order-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem 0;
        border-bottom: 1px solid var(--border);
    }
    
    .order-item:last-child {
        border-bottom: none;
    }
    
    .item-image {
        width: 60px;
        height: 60px;
        border-radius: 8px;
        background: var(--light);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .item-image i {
        color: var(--gray);
    }
    
    .item-details {
        flex: 1;
    }
    
    .item-name {
        font-weight: 600;
    }
    
    .item-qty {
        color: var(--gray);
        font-size: 0.875rem;
    }
    
    .item-price {
        font-weight: 600;
    }
    
    .order-totals {
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 1px solid var(--border);
    }
    
    .total-row {
        display: flex;
        justify-content: space-between;
        padding: 0.5rem 0;
    }
    
    .total-row.grand-total {
        font-size: 1.25rem;
        font-weight: 700;
        border-top: 2px solid var(--border);
        padding-top: 1rem;
        margin-top: 0.5rem;
    }
    
    .payment-info {
        background: rgba(99, 102, 241, 0.1);
        border-radius: 12px;
        padding: 1.5rem;
        margin: 2rem 0;
    }
    
    .payment-info h3 {
        color: var(--primary);
        margin-bottom: 1rem;
    }
    
    .bank-details {
        background: white;
        padding: 1rem;
        border-radius: 8px;
        font-family: monospace;
    }
    
    .actions {
        display: flex;
        gap: 1rem;
        justify-content: center;
        margin-top: 2rem;
    }
    
    .btn {
        padding: 1rem 2rem;
        border-radius: 50px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: white;
    }
    
    .btn-secondary {
        background: var(--light);
        color: var(--dark);
        border: 2px solid var(--border);
    }
    
    .btn:hover {
        transform: translateY(-2px);
    }
    
    .alert {
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
    }
    
    .alert-info {
        background: rgba(99, 102, 241, 0.1);
        color: var(--primary);
        border: 1px solid var(--primary);
    }
    
    .alert-error {
        background: rgba(239, 68, 68, 0.1);
        color: var(--danger);
        border: 1px solid var(--danger);
    }
    
    @media (max-width: 768px) {
        .info-grid {
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }
        
        .actions {
            flex-direction: column;
        }
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="success-container">
        @if(session('info'))
            <div class="alert alert-info">{{ session('info') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        <div class="success-icon">
            <i class="fas fa-check"></i>
        </div>

        <h1 class="success-title">Dziękujemy za zamówienie! 🎉</h1>
        <p class="success-subtitle">
            Twoje zamówienie zostało przyjęte. Potwierdzenie wysłaliśmy na adres email.
        </p>

        <div class="order-details">
            <div class="order-header">
                <div>
                    <div class="order-number">Zamówienie #{{ $order->order_number }}</div>
                    <div class="order-date">{{ $order->created_at->format('d.m.Y H:i') }}</div>
                </div>
                <div>
                    <span style="background: rgba(99, 102, 241, 0.1); color: var(--primary); padding: 0.5rem 1rem; border-radius: 50px; font-weight: 600;">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
            </div>

            <div class="info-grid">
                @if($order->shipping)
                <div class="info-section">
                    <h3>Adres dostawy</h3>
                    <p>
                        {{ $order->shipping->first_name }} {{ $order->shipping->last_name }}<br>
                        {{ $order->shipping->street_address }}
                        @if($order->shipping->apartment)
                            {{ $order->shipping->apartment }}
                        @endif
                        <br>
                        {{ $order->shipping->postal_code }} {{ $order->shipping->city }}<br>
                        {{ $order->shipping->country }}
                    </p>
                </div>
                @endif

                <div class="info-section">
                    <h3>Metoda płatności</h3>
                    <p>
                        @switch($order->payment_method)
                            @case('cash_on_delivery')
                                Płatność przy odbiorze
                                @break
                            @case('bank_transfer')
                                Przelew bankowy
                                @break
                            @case('payu')
                                PayU (karta/BLIK)
                                @break
                            @default
                                {{ $order->payment_method }}
                        @endswitch
                        <br>
                        Status: 
                        <strong>
                            @switch($order->payment_status)
                                @case('paid')
                                    <span style="color: var(--success);">Opłacone</span>
                                    @break
                                @case('pending')
                                    <span style="color: var(--warning);">Oczekuje na płatność</span>
                                    @break
                                @case('failed')
                                    <span style="color: var(--danger);">Płatność nieudana</span>
                                    @break
                                @default
                                    {{ $order->payment_status }}
                            @endswitch
                        </strong>
                    </p>
                </div>
            </div>

            @if($order->payment_method === 'bank_transfer' && $order->payment_status !== 'paid')
            <div class="payment-info">
                <h3><i class="fas fa-university"></i> Dane do przelewu</h3>
                <div class="bank-details">
                    <p><strong>Numer konta:</strong> XX XXXX XXXX XXXX XXXX XXXX XXXX</p>
                    <p><strong>Tytuł przelewu:</strong> {{ $order->order_number }}</p>
                    <p><strong>Kwota:</strong> {{ number_format($order->total, 2) }} zł</p>
                </div>
                <p style="margin-top: 1rem; font-size: 0.875rem; color: var(--gray);">
                    Po zaksięgowaniu płatności otrzymasz potwierdzenie na email.
                </p>
            </div>
            @endif

            <div class="order-items">
                <h3>Zamówione produkty</h3>
                @foreach($order->items as $item)
                <div class="order-item">
                    <div class="item-image">
                        <i class="fas fa-compact-disc"></i>
                    </div>
                    <div class="item-details">
                        <div class="item-name">{{ $item->product_name }}</div>
                        @if($item->variant_name)
                            <div class="item-qty">{{ $item->variant_name }}</div>
                        @endif
                        <div class="item-qty">Ilość: {{ $item->quantity }}</div>
                    </div>
                    <div class="item-price">{{ number_format($item->total, 2) }} zł</div>
                </div>
                @endforeach
            </div>

            <div class="order-totals">
                <div class="total-row">
                    <span>Wartość produktów</span>
                    <span>{{ number_format($order->subtotal, 2) }} zł</span>
                </div>
                <div class="total-row">
                    <span>Dostawa</span>
                    <span>{{ number_format($order->shipping_cost, 2) }} zł</span>
                </div>
                @if($order->discount > 0)
                <div class="total-row" style="color: var(--success);">
                    <span>Rabat</span>
                    <span>-{{ number_format($order->discount, 2) }} zł</span>
                </div>
                @endif
                <div class="total-row grand-total">
                    <span>Razem</span>
                    <span>{{ number_format($order->total, 2) }} zł</span>
                </div>
            </div>
        </div>

        <div class="actions">
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