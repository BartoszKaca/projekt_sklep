@extends('layouts.shop')

@section('title', 'Moje konto')

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
    
    .user-name {
        font-size: 1.125rem;
        font-weight: 700;
    }
    
    .user-email {
        color: var(--gray);
        font-size: 0.875rem;
    }
    
    .sidebar-nav {
        list-style: none;
    }
    
    .sidebar-nav li {
        margin-bottom: 0.5rem;
    }
    
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
    
    .sidebar-nav a:hover,
    .sidebar-nav a.active {
        background: rgba(99, 102, 241, 0.1);
        color: var(--primary);
    }
    
    .sidebar-nav a.active {
        font-weight: 600;
    }
    
    .sidebar-nav i {
        width: 20px;
        text-align: center;
    }
    
    .logout-link {
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid var(--border);
    }
    
    .logout-link a {
        color: var(--danger) !important;
    }
    
    .account-content {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        border: 1px solid var(--border);
    }
    
    .welcome-section {
        margin-bottom: 2rem;
    }
    
    .welcome-section h1 {
        font-size: 1.75rem;
        font-weight: 800;
        margin-bottom: 0.5rem;
    }
    
    .welcome-section p {
        color: var(--gray);
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .stat-card {
        background: var(--light);
        border-radius: 12px;
        padding: 1.5rem;
        text-align: center;
    }
    
    .stat-icon {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        color: white;
        font-size: 1.25rem;
    }
    
    .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
    }
    
    .stat-label {
        color: var(--gray);
        font-size: 0.875rem;
    }
    
    .recent-orders {
        margin-top: 2rem;
    }
    
    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }
    
    .section-title {
        font-size: 1.25rem;
        font-weight: 700;
    }
    
    .view-all {
        color: var(--primary);
        text-decoration: none;
        font-weight: 600;
    }
    
    .orders-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .orders-table th,
    .orders-table td {
        padding: 1rem;
        text-align: left;
        border-bottom: 1px solid var(--border);
    }
    
    .orders-table th {
        font-weight: 600;
        color: var(--gray);
        font-size: 0.875rem;
    }
    
    .order-number {
        font-weight: 600;
        color: var(--primary);
    }
    
    .status-badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    
    .status-pending {
        background: rgba(245, 158, 11, 0.1);
        color: var(--warning);
    }
    
    .status-processing {
        background: rgba(99, 102, 241, 0.1);
        color: var(--primary);
    }
    
    .status-shipped {
        background: rgba(16, 185, 129, 0.1);
        color: var(--success);
    }
    
    .status-delivered {
        background: rgba(16, 185, 129, 0.2);
        color: var(--success);
    }
    
    .status-cancelled {
        background: rgba(239, 68, 68, 0.1);
        color: var(--danger);
    }
    
    .empty-state {
        text-align: center;
        padding: 3rem;
        color: var(--gray);
    }
    
    .empty-state i {
        font-size: 3rem;
        opacity: 0.3;
        margin-bottom: 1rem;
    }
    
    .alert {
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
    }
    
    .alert-success {
        background: rgba(16, 185, 129, 0.1);
        color: var(--success);
        border: 1px solid var(--success);
    }
    
    .alert-error {
        background: rgba(239, 68, 68, 0.1);
        color: var(--danger);
        border: 1px solid var(--danger);
    }
    
    @media (max-width: 1024px) {
        .account-container {
            grid-template-columns: 1fr;
        }
        
        .account-sidebar {
            display: none;
        }
        
        .stats-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="account-container">
        <aside class="account-sidebar">
            <div class="user-info">
                <div class="user-avatar">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div class="user-name">{{ $user->name }}</div>
                <div class="user-email">{{ $user->email }}</div>
            </div>
            
            <nav>
                <ul class="sidebar-nav">
                    <li>
                        <a href="{{ route('account.dashboard') }}" class="active">
                            <i class="fas fa-tachometer-alt"></i> Panel główny
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('account.orders') }}">
                            <i class="fas fa-shopping-bag"></i> Zamówienia
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('account.wishlist') }}">
                            <i class="fas fa-heart"></i> Ulubione
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('account.addresses') }}">
                            <i class="fas fa-map-marker-alt"></i> Adresy
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('account.profile') }}">
                            <i class="fas fa-user"></i> Profil
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('account.password') }}">
                            <i class="fas fa-lock"></i> Zmień hasło
                        </a>
                    </li>
                    <li class="logout-link">
                        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fas fa-sign-out-alt"></i> Wyloguj
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </li>
                </ul>
            </nav>
        </aside>
        
        <main class="account-content">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-error">{{ session('error') }}</div>
            @endif
            
            <div class="welcome-section">
                <h1>Witaj, {{ $user->name }}! 👋</h1>
                <p>Zarządzaj swoim kontem i śledź zamówienia</p>
            </div>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                    <div class="stat-value">{{ $recentOrders->count() }}</div>
                    <div class="stat-label">Ostatnie zamówienia</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-heart"></i>
                    </div>
                    <div class="stat-value">{{ $wishlistCount }}</div>
                    <div class="stat-label">Ulubione produkty</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="stat-value">{{ $addressCount }}</div>
                    <div class="stat-label">Zapisane adresy</div>
                </div>
            </div>
            
            <div class="recent-orders">
                <div class="section-header">
                    <h2 class="section-title">Ostatnie zamówienia</h2>
                    <a href="{{ route('account.orders') }}" class="view-all">Zobacz wszystkie →</a>
                </div>
                
                @if($recentOrders->count() > 0)
                <table class="orders-table">
                    <thead>
                        <tr>
                            <th>Numer</th>
                            <th>Data</th>
                            <th>Status</th>
                            <th>Kwota</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentOrders as $order)
                        <tr>
                            <td>
                                <a href="{{ route('account.orders.show', $order->id) }}" class="order-number">
                                    {{ $order->order_number }}
                                </a>
                            </td>
                            <td>{{ $order->created_at->format('d.m.Y') }}</td>
                            <td>
                                <span class="status-badge status-{{ $order->status }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td>{{ number_format($order->total, 2) }} zł</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="empty-state">
                    <i class="fas fa-shopping-bag"></i>
                    <p>Nie masz jeszcze żadnych zamówień</p>
                    <a href="{{ route('home') }}" style="color: var(--primary); font-weight: 600;">Przejdź do sklepu →</a>
                </div>
                @endif
            </div>
        </main>
    </div>
</div>
@endsection