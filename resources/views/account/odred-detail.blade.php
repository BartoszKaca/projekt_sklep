@extends('layouts.shop')

@section('title', 'Szczegóły zamówienia #' . $order->order_number)

@push('styles')
<style>
    .account-container {
        display: grid;
        grid-template-columns: 250px 1fr;
        gap: 2rem;
        margin-top: 2rem;
    }
    
    .account-sidebar {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        border: 1px solid var(--border);
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
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        font-size: 2rem;
        color: white;
        font-weight: 700;
    }
    
    .user-name { font-size: 1.125rem; font-weight: 700; }
    .user-email { color: var(--gray); font-size: 0.875rem; }
    
    .sidebar-nav { list-style: none; }
    .sidebar-nav li { margin-bottom: 0.5rem; }
    .sidebar-nav a {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 1rem;
        border-radius: 8px;
        color: var(--dark);
        text-decoration: none;
        transition: all 0.2s;
    }
    .sidebar-nav a:hover, .sidebar-nav a.active { background: rgba(99, 102, 241, 0.1); color: var(--primary); }
    .sidebar-nav a.active { font-weight: 600; }
    .sidebar-nav i { width: 20px; text-align: center; }
    .logout-link { margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--border); }
    .logout-link a { color: var(--danger) !important; }
    
    .account-content {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        border: 1px solid var(--border);
    }
    
    .breadcrumb {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 1.5rem;
        font-size: 0.875rem;
    }
    
    .breadcrumb a { color: var(--gray); text-decoration: none; }
    .breadcrumb a:hover { color: var(--primary); }
    .breadcrumb span { color: var(--gray); }
    
    .order-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 2rem;
        flex-wrap: wrap;
        gap: 1rem;
    }
    
    .order-title { font-size: 1.5rem; font-weight: 700; margin: 0; }
    .order-date { color: var(--gray); margin-top: 0.25rem; }
    
    .status-badge {
        display: inline-block;
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-size: 0.875rem;
        font-weight: 600;
    }
    
    .status-pending { background: rgba(245, 158, 11, 0.1); color: var(--warning); }
    .status-processing { background: rgba(99, 102, 241, 0.1); color: var(--primary); }
    .status-shipped { background: rgba(16, 185, 129, 0.1); color: var(--success); }
    .status-delivered { background: rgba(16, 185, 129, 0.2); color: var(--success); }
    .status-cancelled { background: rgba(239, 68, 68, 0.1); color: var(--danger); }
    
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .info-card {
        background: var(--light);
        border-radius: 12px;
        padding: 1.5rem;
    }
    
    .info-card h3 {
        font-size: 0.875rem;
        text-transform: uppercase;
        color: var(--gray);
        margin-bottom: 0.75rem;
    }
    
    .info-card p { margin: 0; line-height: 1.8; }
    
    .order-items-section {
        margin-top: 2rem;
    }
    
    .section-title { font-size: 1.125rem; font-weight: 700; margin-bottom: 1rem; }
    
    .order-items-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .order-items-table th,
    .order-items-table td {
        padding: 1rem;
        text-align: left;
        border-bottom: 1px solid var(--border);
    }
    
    .order-items-table th { font-weight: 600; color: var(--gray); font-size: 0.875rem; }
    
    .item-info { display: flex; align-items: center; gap: 1rem; }
    
    .item-image {
        width: 60px;
        height: 60px;
        border-radius: 8px;
        background: var(--light);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }
    
    .item-image img { width: 100%; height: 100%; object-fit: cover; }
    .item-image i { color: var(--gray); }
    
    .item-name { font-weight: 600; }
    .item-variant { font-size: 0.875rem; color: var(--gray); }
    
    .order-totals {
        margin-top: 2rem;
        max-width: 300px;
        margin-left: auto;
    }
    
    .total-row {
        display: flex;
        justify-content: space-between;
        padding: 0.75rem 0;
        border-bottom: 1px solid var(--border);
    }
    
    .total-row.grand-total {
        font-size: 1.25rem;
        font-weight: 700;
        border-bottom: none;
        padding-top: 1rem;
    }
    
    .total-row.discount { color: var(--success); }
    
    @media (max-width: 1024px) {
        .account-container { grid-template-columns: 1fr; }
        .account-sidebar { display: none; }
        .order-items-table { font-size: 0.875rem; }
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="account-container">
        <aside class="account-sidebar">
            <div class="user-info">
                <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                <div class="user-name">{{ auth()->user()->name }}</div>
                <div class="user-email">{{ auth()->user()->email }}</div>
            </div>
            <nav>
                <ul class="sidebar-nav">
                    <li><a href="{{ route('account.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Panel główny</a></li>
                    <li><a href="{{ route('account.orders') }}" class="active"><i class="fas fa-shopping-bag"></i> Zamówienia</a></li>
                    <li><a href="{{ route('account.wishlist') }}"><i class="fas fa-heart"></i> Ulubione</a></li>
                    <li><a href="{{ route('account.addresses') }}"><i class="fas fa-map-marker-alt"></i> Adresy</a></li>
                    <li><a href="{{ route('account.profile') }}"><i class="fas fa-user"></i> Profil</a></li>
                    <li><a href="{{ route('account.password') }}"><i class="fas fa-lock"></i> Zmień hasło</a></li>
                    <li class="logout-link">
                        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fas fa-sign-out-alt"></i> Wyloguj
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>
                    </li>
                </ul>
            </nav>
        </aside>

        <main class="account-content">
            <div class="breadcrumb">
                <a href="{{ route('account.orders') }}">Zamówienia</a>
                <span>/</span>
                <span>{{ $order->order_number }}</span>
            </div>

            <div class="order-header">
                <div>
                    <h1 class="order-title">Zamówienie {{ $order->order_number }}</h1>
                    <div class="order-date">{{ $order->created_at->format('d.m.Y H:i') }}</div>
                </div>
                <span class="status-badge status-{{ $order->status }}">{{ ucfirst($order->status) }}</span>
            </div>

            <div class="info-grid">
                @if($order->shipping)
                <div class="info-card">
                    <h3><i class="fas fa-truck"></i> Adres dostawy</h3>
                    <p>
                        {{ $order->shipping->first_name }} {{ $order->shipping->last_name }}<br>
                        {{ $order->shipping->street_address }}
                        @if($order->shipping->apartment) {{ $order->shipping->apartment }} @endif<br>
                        {{ $order->shipping->postal_code }} {{ $order->shipping->city }}<br>
                        {{ $order->shipping->country }}
                        @if($order->shipping->phone)<br>Tel: {{ $order->shipping->phone }}@endif
                    </p>
                </div>
                @endif

                <div class="info-card">
                    <h3><i class="fas fa-credit-card"></i> Płatność</h3>
                    <p>
                        <strong>Metoda:</strong> 
                        @switch($order->payment_method)
                            @case('cash_on_delivery') Płatność przy odbiorze @break
                            @case('bank_transfer') Przelew bankowy @break
                            @case('payu') PayU @break
                            @default {{ $order->payment_method }}
                        @endswitch
                        <br>
                        <strong>Status:</strong>
                        @switch($order->payment_status)
                            @case('paid') <span style="color: var(--success);">Opłacone</span> @break
                            @case('pending') <span style="color: var(--warning);">Oczekuje</span> @break
                            @case('failed') <span style="color: var(--danger);">Nieudana</span> @break
                            @default {{ $order->payment_status }}
                        @endswitch
                    </p>
                </div>

                @if($order->tracking_number)
                <div class="info-card">
                    <h3><i class="fas fa-shipping-fast"></i> Śledzenie przesyłki</h3>
                    <p>
                        <strong>Przewoźnik:</strong> {{ $order->carrier ?? 'Nieznany' }}<br>
                        <strong>Numer:</strong> {{ $order->tracking_number }}
                    </p>
                </div>
                @endif
            </div>

            <div class="order-items-section">
                <h2 class="section-title">Zamówione produkty</h2>

                <table class="order-items-table">
                    <thead>
                        <tr>
                            <th>Produkt</th>
                            <th>Cena</th>
                            <th>Ilość</th>
                            <th>Razem</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                        <tr>
                            <td>
                                <div class="item-info">
                                    <div class="item-image">
                                        @if($item->product && $item->product->primaryImage)
                                            <img src="{{ asset('storage/' . $item->product->primaryImage->path) }}" alt="">
                                        @else
                                            <i class="fas fa-compact-disc"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="item-name">{{ $item->product_name }}</div>
                                        @if($item->variant_name)
                                            <div class="item-variant">{{ $item->variant_name }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>{{ number_format($item->price, 2) }} zł</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ number_format($item->total, 2) }} zł</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="order-totals">
                    <div class="total-row">
                        <span>Produkty</span>
                        <span>{{ number_format($order->subtotal, 2) }} zł</span>
                    </div>
                    <div class="total-row">
                        <span>Dostawa</span>
                        <span>{{ number_format($order->shipping_cost, 2) }} zł</span>
                    </div>
                    @if($order->discount > 0)
                    <div class="total-row discount">
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
        </main>
    </div>
</div>
@endsection