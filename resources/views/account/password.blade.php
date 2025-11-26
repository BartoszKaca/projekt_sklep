@extends('layouts.shop')

@section('title', 'Zmiana hasła')

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
    
    .page-title {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 2rem;
    }
    
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 600;
    }
    
    .form-group input {
        width: 100%;
        padding: 0.875rem 1rem;
        border: 2px solid var(--border);
        border-radius: 8px;
        font-size: 1rem;
    }
    
    .form-group input:focus {
        outline: none;
        border-color: var(--primary);
    }
    
    .form-group.error input {
        border-color: var(--danger);
    }
    
    .error-message {
        color: var(--danger);
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }
    
    .password-requirements {
        background: var(--light);
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1.5rem;
        font-size: 0.875rem;
        color: var(--gray);
    }
    
    .password-requirements ul {
        margin: 0.5rem 0 0 1.5rem;
    }
    
    .btn {
        padding: 1rem 2rem;
        border-radius: 50px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: white;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
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
    
    @media (max-width: 1024px) {
        .account-container {
            grid-template-columns: 1fr;
        }
        
        .account-sidebar {
            display: none;
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
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="user-name">{{ auth()->user()->name }}</div>
                <div class="user-email">{{ auth()->user()->email }}</div>
            </div>
            
            <nav>
                <ul class="sidebar-nav">
                    <li><a href="{{ route('account.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Panel główny</a></li>
                    <li><a href="{{ route('account.orders') }}"><i class="fas fa-shopping-bag"></i> Zamówienia</a></li>
                    <li><a href="{{ route('account.wishlist') }}"><i class="fas fa-heart"></i> Ulubione</a></li>
                    <li><a href="{{ route('account.addresses') }}"><i class="fas fa-map-marker-alt"></i> Adresy</a></li>
                    <li><a href="{{ route('account.profile') }}"><i class="fas fa-user"></i> Profil</a></li>
                    <li><a href="{{ route('account.password') }}" class="active"><i class="fas fa-lock"></i> Zmień hasło</a></li>
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
            <h1 class="page-title">Zmiana hasła</h1>
            
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            
            <div class="password-requirements">
                <strong>Wymagania hasła:</strong>
                <ul>
                    <li>Minimum 8 znaków</li>
                    <li>Przynajmniej jedna wielka litera</li>
                    <li>Przynajmniej jedna mała litera</li>
                    <li>Przynajmniej jedna cyfra</li>
                </ul>
            </div>
            
            <form action="{{ route('account.password.update') }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="form-group @error('current_password') error @enderror">
                    <label for="current_password">Aktualne hasło</label>
                    <input type="password" id="current_password" name="current_password" required>
                    @error('current_password')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group @error('password') error @enderror">
                    <label for="password">Nowe hasło</label>
                    <input type="password" id="password" name="password" required>
                    @error('password')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="password_confirmation">Potwierdź nowe hasło</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-lock"></i> Zmień hasło
                </button>
            </form>
        </main>
    </div>
</div>
@endsection
