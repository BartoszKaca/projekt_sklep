@extends('layouts.shop')

@section('title','Moje konto')

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
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .stat-card {
        background: var(--light);
        padding: 1.5rem;
        border-radius: 12px;
        text-align: center;
    }
    
    .stat-value {
        font-size: 2rem;
        font-weight: 800;
        color: var(--primary);
    }
    
    .stat-label {
        color: var(--gray);
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }
    
    .recent-orders {
        margin-top: 2rem;
    }
    
    .order-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem;
        background: var(--light);
        border-radius: 8px;
        margin-bottom: 0.75rem;
    }
    
    .order-number {
        font-weight: 600;
    }
    
    .order-date {
        color: var(--gray);
        font-size: 0.875rem;
    }
    
    .order-status {
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    
    .order-status.pending { background: #fef3c7; color: #92400e; }
    .order-status.confirmed { background: #dbeafe; color: #1e40af; }
    .order-status.shipped { background: #d1fae5; color: #065f46; }
    .order-status.delivered { background: #d1fae5; color: #065f46; }
    
    .alert {
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1rem;
    }
    
    .alert-success {
        background: #d1fae5;
        color: #065f46;
    }
    
    @media (max-width: 768px) {
        .account-grid {
            grid-template-columns: 1fr;
        }
        
        .stats-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="container">
    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <div class="account-grid">
        <aside class="account-sidebar">
            <div class="user-info">
                <div class="user-avatar">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div class="user-name">{{ $user->name }}</div>
                <div class="user-email">{{ $user->email }}</div>
            </div>
            
            <ul class="account-nav">
                <li>
                    <a href="{{ route('account.dashboard') }}" class="{{ request()->routeIs('account.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-home"></i> Dashboard
                    </a>
                </li>
                <li>
                    <a href="{{ route('account.orders') }}" class="{{ request()->routeIs('account.orders') ? 'active' : '' }}">
                        <i class="fas fa-shopping-bag"></i> Moje zamówienia
                    </a>
                </li>
                <li>
                    <a href="{{ route('account.wishlist') }}" class="{{ request()->routeIs('account.wishlist') ? 'active' : '' }}">
                        <i class="fas fa-heart"></i> Ulubione
                    </a>
                </li>
                <li>
                    <a href="{{ route('account.edit') }}" class="{{ request()->routeIs('account.edit') ? 'active' : '' }}">
                        <i class="fas fa-user-edit"></i> Edytuj profil
                    </a>
                </li>
                <li>
                    <a href="{{ route('account.password') }}" class="{{ request()->routeIs('account.password') ? 'active' : '' }}">
                        <i class="fas fa-lock"></i> Zmień hasło
                    </a>
                </li>
                <li>
                    <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                        @csrf
                        <a href="#" onclick="this.closest('form').submit(); return false;">
                            <i class="fas fa-sign-out-alt"></i> Wyloguj
                        </a>
                    </form>
                </li>
            </ul>
        </aside>
        
        <div class="account-content">
            <h2 class="section-title">Witaj, {{ $user->name }}!</h2>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value">{{ $user->orders()->count() }}</div>
                    <div class="stat-label">Zamówień</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ $wishlistCount ?? 0 }}</div>
                    <div class="stat-label">Ulubionych</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ number_format($user->orders()->where('payment_status', 'paid')->sum('total'), 0) }} zł</div>
                    <div class="stat-label">Wydano</div>
                </div>
            </div>
            
            <div class="recent-orders">
                <h3 style="margin-bottom: 1rem;">Ostatnie zamówienia</h3>
                
                @forelse($recentOrders ?? [] as $order)
                <div class="order-row">
                    <div>
                        <div class="order-number">#{{ $order->order_number }}</div>
                        <div class="order-date">{{ $order->created_at->format('d.m.Y H:i') }}</div>
                    </div>
                    <div style="font-weight: 600;">{{ number_format($order->total, 2) }} zł</div>
                    <span class="order-status {{ $order->status }}">{{ ucfirst($order->status) }}</span>
                    <a href="{{ route('account.orders.show', $order) }}" style="color: var(--primary);">
                        <i class="fas fa-eye"></i>
                    </a>
                </div>
                @empty
                <p style="text-align: center; color: var(--gray); padding: 2rem;">
                    Nie masz jeszcze żadnych zamówień.
                    <a href="{{ route('products.index') }}" style="color: var(--primary);">Rozpocznij zakupy!</a>
                </p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection