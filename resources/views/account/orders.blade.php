@extends('layouts.shop')

@section('title', 'Moje zamówienia')

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
    
    .page-title { font-size: 1.5rem; font-weight: 700; margin-bottom: 2rem; }
    
    .orders-list { display: flex; flex-direction: column; gap: 1rem; }
    
    .order-card {
        background: var(--light);
        border-radius: 12px;
        padding: 1.5rem;
    }
    
    .order-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        flex-wrap: wrap;
        gap: 1rem;
    }
    
    .order-number {
        font-weight: 700;
        color: var(--primary);
        font-size: 1.125rem;
    }
    
    .order-date { color: var(--gray); font-size: 0.875rem; }
    
    .status-badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    
    .status-pending { background: rgba(245, 158, 11, 0.1); color: var(--warning); }
    .status-processing { background: rgba(99, 102, 241, 0.1); color: var(--primary); }
    .status-shipped { background: rgba(16, 185, 129, 0.1); color: var(--success); }
    .status-delivered { background: rgba(16, 185, 129, 0.2); color: var(--success); }
    .status-cancelled { background: rgba(239, 68, 68, 0.1); color: var(--danger); }
    
    .order-items {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
        margin-bottom: 1rem;
    }
    
    .order-item-preview {
        width: 50px;
        height: 50px;
        border-radius: 8px;
        background: white;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }
    
    .order-item-preview img { width: 100%; height: 100%; object-fit: cover; }
    .order-item-preview i { color: var(--gray); }
    
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
    
    .btn {
        padding: 0.75rem 1.5rem;
        border-radius: 50px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .btn-primary { background: linear-gradient(135deg, var(--primary), var(--secondary)); color: white; }
    
    .empty-state {
        text-align: center;
        padding: 3rem;
        color: var(--gray);
    }
    
    .empty-state i { font-size: 3rem; opacity: 0.3; margin-bottom: 1rem; display: block; }
    
    .pagination { margin-top: 2rem; }
    
    @media (max-width: 1024px) {
        .account-container { grid-template-columns: 1fr; }
        .account-sidebar { display: none; }
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
            <h1 class="page-title">Moje zamówienia</h1>
            
            @if($orders->count() > 0)
            <div class="orders-list">
                @foreach($orders as $order)
                <div class="order-card">
                    <div class="order-header">
                        <div>
                            <a href="{{ route('account.orders.show', $order->id) }}" class="order-number">{{ $order->order_number }}</a>
                            <div class="order-date">{{ $order->created_at->format('d.m.Y H:i') }}</div>
                        </div>
                        <span class="status-badge status-{{ $order->status }}">{{ ucfirst($order->status) }}</span>
                    </div>
                    
                    <div class="order-items">
                        @foreach($order->items->take(4) as $item)
                        <div class="order-item-preview">
                            @if($item->product && $item->product->primaryImage)
                                <img src="{{ asset('storage/' . $item->product->primaryImage->path) }}" alt="">
                            @else
                                <i class="fas fa-compact-disc"></i>
                            @endif
                        </div>
                        @endforeach
                        @if($order->items->count() > 4)
                        <div class="order-item-preview" style="background: var(--primary); color: white; font-weight: 600;">
                            +{{ $order->items->count() - 4 }}
                        </div>
                        @endif
                    </div>
                    
                    <div class="order-footer">
                        <div class="order-total">{{ number_format($order->total, 2) }} zł</div>
                        <a href="{{ route('account.orders.show', $order->id) }}" class="btn btn-primary">
                            Zobacz szczegóły <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
            
            <div class="pagination">
                {{ $orders->links() }}
            </div>
            @else
            <div class="empty-state">
                <i class="fas fa-shopping-bag"></i>
                <p>Nie masz jeszcze żadnych zamówień</p>
                <a href="{{ route('home') }}" class="btn btn-primary" style="margin-top: 1rem;">Przejdź do sklepu</a>
            </div>
            @endif
        </main>
    </div>
</div>
@endsection