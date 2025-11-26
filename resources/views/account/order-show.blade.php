@extends('layouts.shop')

@section('title', 'Zamówienie #' . $order->order_number)

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
    
    .order-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid var(--border);
    }
    
    .order-title {
        font-size: 1.5rem;
        font-weight: 700;
    }
    
    .order-date {
        color: var(--gray);
        font-size: 0.875rem;
    }
    
    .order-status {
        padding: 0.5rem 1.5rem;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 600;
        text-transform: uppercase;
    }
    
    .order-status.pending { background: #fef3c7; color: #92400e; }
    .order-status.confirmed { background: #dbeafe; color: #1e40af; }
    .order-status.processing { background: #dbeafe; color: #1e40af; }
    .order-status.shipped { background: #c7d2fe; color: #3730a3; }
    .order-status.delivered { background: #d1fae5; color: #065f46; }
    .order-status.cancelled { background: #fee2e2; color: #991b1b; }
    
    .order-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
        margin-bottom: 2rem;
    }
    
    .info-card {
        background: var(--light);
        padding: 1.5rem;
        border-radius: 12px;
    }
    
    .info-card h4 {
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: 1rem;
        color: var(--gray);
    }
    
    .info-card p {
        margin: 0;
        line-height: 1.8;
    }
    
    .items-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .items-table th,
    .items-table td {
        padding: 1rem;
        text-align: left;
        border-bottom: 1px solid var(--border);
    }
    
    .items-table th {
        background: var(--light);
        font-weight: 600;
        font-size: 0.875rem;
        text-transform: uppercase;
        color: var(--gray);
    }
    
    .items-table td {
        vertical-align: middle;
    }
    
    .product-cell {
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    
    .product-image {
        width: 60px;
        height: 60px;
        border-radius: 8px;
        background: var(--light);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .order-totals {
        margin-top: 2rem;
        padding-top: 1rem;
        border-top: 2px solid var(--border);
    }
    
    .total-row {
        display: flex;
        justify-content: flex-end;
        gap: 4rem;
        padding: 0.5rem 0;
        font-size: 0.9375rem;
    }
    
    .total-row.final {
        font-size: 1.25rem;
        font-weight: 700;
        margin-top: 0.5rem;
        padding-top: 0.5rem;
        border-top: 1px solid var(--border);
    }
    
    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--primary);
        text-decoration: none;
        margin-bottom: 1rem;
    }
    
    .back-link:hover {
        text-decoration: underline;
    }
    
    @media (max-width: 768px) {
        .account-grid {
            grid-template-columns: 1fr;
        }
        
        .order-grid {
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
            <a href="{{ route('account.orders') }}" class="back-link">
                <i class="fas fa-arrow-left"></i> Powrót do zamówień
            </a>
            
            <div class="order-header">
                <div>
                    <div class="order-title">#{{ $order->order_number }}</div>
                    <div class="order-date">Złożone: {{ $order->created_at->format('d.m.Y H:i') }}</div>
                </div>
                <span class="order-status {{ $order->status }}">{{ $order->status }}</span>
            </div>
            
            <div class="order-grid">
                @if($order->shipping)
                <div class="info-card">
                    <h4><i class="fas fa-map-marker-alt"></i> Adres dostawy</h4>
                    <p>
                        {{ $order->shipping->first_name }} {{ $order->shipping->last_name }}<br>
                        {{ $order->shipping->street_address }}
                        @if($order->shipping->apartment), {{ $order->shipping->apartment }}@endif<br>
                        {{ $order->shipping->postal_code }} {{ $order->shipping->city }}<br>
                        {{ $order->shipping->country }}<br>
                        <br>
                        <strong>Tel:</strong> {{ $order->shipping->phone }}<br>
                        <strong>Email:</strong> {{ $order->shipping->email }}
                    </p>
                </div>
                @endif
                
                <div class="info-card">
                    <h4><i class="fas fa-credit-card"></i> Płatność</h4>
                    <p>
                        <strong>Metoda:</strong> 
                        @switch($order->payment_method)
                            @case('transfer') Przelew online @break
                            @case('card') Karta płatnicza @break
                            @case('cod') Płatność przy odbiorze @break
                            @default {{ $order->payment_method }}
                        @endswitch
                        <br>
                        <strong>Status:</strong> 
                        @switch($order->payment_status)
                            @case('pending') Oczekuje @break
                            @case('paid') Opłacone @break
                            @case('failed') Nieudane @break
                            @default {{ $order->payment_status }}
                        @endswitch
                        @if($order->paid_at)
                        <br><strong>Data płatności:</strong> {{ $order->paid_at->format('d.m.Y H:i') }}
                        @endif
                    </p>
                </div>
            </div>
            
            <h4 style="margin-bottom: 1rem;"><i class="fas fa-box"></i> Zamówione produkty</h4>
            
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Produkt</th>
                        <th>Cena</th>
                        <th>Ilość</th>
                        <th style="text-align: right;">Razem</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                    <tr>
                        <td>
                            <div class="product-cell">
                                <div class="product-image">
                                    <i class="fas fa-compact-disc" style="color: var(--gray);"></i>
                                </div>
                                <div>
                                    <strong>{{ $item->product_name }}</strong>
                                    @if($item->variant_name)
                                    <div style="font-size: 0.875rem; color: var(--gray);">{{ $item->variant_name }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>{{ number_format($item->price, 2) }} zł</td>
                        <td>{{ $item->quantity }}</td>
                        <td style="text-align: right; font-weight: 600;">{{ number_format($item->total, 2) }} zł</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div class="order-totals">
                <div class="total-row">
                    <span>Produkty:</span>
                    <span>{{ number_format($order->subtotal, 2) }} zł</span>
                </div>
                <div class="total-row">
                    <span>Dostawa:</span>
                    <span>{{ $order->shipping_cost > 0 ? number_format($order->shipping_cost, 2) . ' zł' : 'Bezpłatnie' }}</span>
                </div>
                @if($order->discount > 0)
                <div class="total-row">
                    <span>Rabat:</span>
                    <span style="color: var(--success);">-{{ number_format($order->discount, 2) }} zł</span>
                </div>
                @endif
                <div class="total-row final">
                    <span>Razem:</span>
                    <span>{{ number_format($order->total, 2) }} zł</span>
                </div>
            </div>
            
            @if($order->customer_notes)
            <div class="info-card" style="margin-top: 2rem;">
                <h4><i class="fas fa-comment"></i> Uwagi do zamówienia</h4>
                <p>{{ $order->customer_notes }}</p>
            </div>
            @endif
            
            @if($order->tracking_number)
            <div class="info-card" style="margin-top: 1rem;">
                <h4><i class="fas fa-truck"></i> Śledzenie przesyłki</h4>
                <p>
                    <strong>Przewoźnik:</strong> {{ $order->carrier ?? 'N/A' }}<br>
                    <strong>Numer przesyłki:</strong> {{ $order->tracking_number }}
                </p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
