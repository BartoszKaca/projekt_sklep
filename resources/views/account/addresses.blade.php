@extends('layouts.shop')

@section('title', 'Moje adresy')

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
    .sidebar-nav a:hover, .sidebar-nav a.active {
        background: rgba(99, 102, 241, 0.1);
        color: var(--primary);
    }
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
    
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }
    
    .page-title { font-size: 1.5rem; font-weight: 700; margin: 0; }
    
    .btn {
        padding: 0.75rem 1.5rem;
        border-radius: 50px;
        font-weight: 600;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: white;
    }
    
    .btn-sm { padding: 0.5rem 1rem; font-size: 0.875rem; }
    .btn-secondary { background: var(--light); color: var(--dark); }
    .btn-danger { background: rgba(239, 68, 68, 0.1); color: var(--danger); }
    
    .addresses-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 1.5rem;
    }
    
    .address-card {
        background: var(--light);
        border-radius: 12px;
        padding: 1.5rem;
        position: relative;
    }
    
    .address-card.default {
        border: 2px solid var(--primary);
    }
    
    .default-badge {
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: var(--primary);
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    
    .address-name {
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
    
    .address-details {
        color: var(--gray);
        line-height: 1.8;
        margin-bottom: 1rem;
    }
    
    .address-actions {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    
    .empty-state {
        text-align: center;
        padding: 3rem;
        color: var(--gray);
    }
    
    .empty-state i { font-size: 3rem; opacity: 0.3; margin-bottom: 1rem; }
    
    .alert {
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
    }
    
    .alert-success { background: rgba(16, 185, 129, 0.1); color: var(--success); border: 1px solid var(--success); }
    .alert-error { background: rgba(239, 68, 68, 0.1); color: var(--danger); border: 1px solid var(--danger); }
    
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
                    <li><a href="{{ route('account.orders') }}"><i class="fas fa-shopping-bag"></i> Zamówienia</a></li>
                    <li><a href="{{ route('account.wishlist') }}"><i class="fas fa-heart"></i> Ulubione</a></li>
                    <li><a href="{{ route('account.addresses') }}" class="active"><i class="fas fa-map-marker-alt"></i> Adresy</a></li>
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
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-error">{{ session('error') }}</div>
            @endif
            
            <div class="page-header">
                <h1 class="page-title">Moje adresy</h1>
                <a href="{{ route('account.addresses.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Dodaj adres
                </a>
            </div>
            
            @if($addresses->count() > 0)
            <div class="addresses-grid">
                @foreach($addresses as $address)
                <div class="address-card {{ $address->is_default ? 'default' : '' }}">
                    @if($address->is_default)
                        <span class="default-badge">Domyślny</span>
                    @endif
                    
                    <div class="address-name">{{ $address->first_name }} {{ $address->last_name }}</div>
                    <div class="address-details">
                        {{ $address->street_address }}
                        @if($address->apartment) {{ $address->apartment }} @endif<br>
                        {{ $address->postal_code }} {{ $address->city }}<br>
                        {{ $address->country }}
                        @if($address->phone)<br>Tel: {{ $address->phone }}@endif
                    </div>
                    
                    <div class="address-actions">
                        <a href="{{ route('account.addresses.edit', $address) }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-edit"></i> Edytuj
                        </a>
                        @if(!$address->is_default)
                        <form action="{{ route('account.addresses.default', $address) }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-secondary">
                                <i class="fas fa-star"></i> Ustaw domyślny
                            </button>
                        </form>
                        @endif
                        <form action="{{ route('account.addresses.destroy', $address) }}" method="POST" style="display: inline;" onsubmit="return confirm('Czy na pewno chcesz usunąć ten adres?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="empty-state">
                <i class="fas fa-map-marker-alt"></i>
                <p>Nie masz jeszcze żadnych zapisanych adresów</p>
                <a href="{{ route('account.addresses.create') }}" class="btn btn-primary" style="margin-top: 1rem;">
                    <i class="fas fa-plus"></i> Dodaj pierwszy adres
                </a>
            </div>
            @endif
        </main>
    </div>
</div>
@endsection
