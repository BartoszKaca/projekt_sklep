@extends('layouts.shop')

@section('title', 'Zmień hasło')

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
    
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    .form-group label {
        display: block;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    
    .form-group input {
        width: 100%;
        padding: 0.875rem 1rem;
        border: 2px solid var(--border);
        border-radius: 8px;
        font-size: 1rem;
        transition: all 0.2s;
    }
    
    .form-group input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }
    
    .form-group .error {
        color: var(--danger);
        font-size: 0.875rem;
        margin-top: 0.5rem;
    }
    
    .btn-save {
        padding: 1rem 2rem;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .btn-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(99, 102, 241, 0.3);
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
                    <a href="{{ route('account.orders') }}">
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
                    <a href="{{ route('account.password') }}" class="active">
                        <i class="fas fa-lock"></i> Zmień hasło
                    </a>
                </li>
            </ul>
        </aside>
        
        <div class="account-content">
            <h2 class="section-title">Zmień hasło</h2>
            
            <form method="POST" action="{{ route('account.password.update') }}">
                @csrf
                @method('PUT')
                
                <div class="form-group">
                    <label for="current_password">Aktualne hasło</label>
                    <input type="password" name="current_password" id="current_password" required>
                    @error('current_password')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="password">Nowe hasło</label>
                    <input type="password" name="password" id="password" required>
                    @error('password')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="password_confirmation">Potwierdź nowe hasło</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required>
                </div>
                
                <button type="submit" class="btn-save">
                    <i class="fas fa-save"></i> Zmień hasło
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
