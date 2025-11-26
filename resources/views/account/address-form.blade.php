@extends('layouts.shop')

@section('title', $address ? 'Edytuj adres' : 'Dodaj adres')

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
    
    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }
    
    .form-group { margin-bottom: 1.5rem; }
    .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 600; }
    
    .form-group input,
    .form-group select {
        width: 100%;
        padding: 0.875rem 1rem;
        border: 2px solid var(--border);
        border-radius: 8px;
        font-size: 1rem;
    }
    
    .form-group input:focus,
    .form-group select:focus { outline: none; border-color: var(--primary); }
    .form-group.error input { border-color: var(--danger); }
    .error-message { color: var(--danger); font-size: 0.875rem; margin-top: 0.25rem; }
    
    .checkbox-group {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .checkbox-group input[type="checkbox"] { width: auto; }
    
    .btn {
        padding: 1rem 2rem;
        border-radius: 50px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.3s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .btn-primary { background: linear-gradient(135deg, var(--primary), var(--secondary)); color: white; }
    .btn-secondary { background: var(--light); color: var(--dark); }
    
    .form-actions { display: flex; gap: 1rem; margin-top: 1rem; }
    
    @media (max-width: 1024px) {
        .account-container { grid-template-columns: 1fr; }
        .account-sidebar { display: none; }
        .form-row { grid-template-columns: 1fr; }
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
            <h1 class="page-title">{{ $address ? 'Edytuj adres' : 'Dodaj nowy adres' }}</h1>
            
            <form action="{{ $address ? route('account.addresses.update', $address) : route('account.addresses.store') }}" method="POST">
                @csrf
                @if($address)
                    @method('PUT')
                @endif
                
                <div class="form-row">
                    <div class="form-group @error('first_name') error @enderror">
                        <label for="first_name">Imię *</label>
                        <input type="text" id="first_name" name="first_name" value="{{ old('first_name', $address->first_name ?? '') }}" required>
                        @error('first_name')<div class="error-message">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="form-group @error('last_name') error @enderror">
                        <label for="last_name">Nazwisko *</label>
                        <input type="text" id="last_name" name="last_name" value="{{ old('last_name', $address->last_name ?? '') }}" required>
                        @error('last_name')<div class="error-message">{{ $message }}</div>@enderror
                    </div>
                </div>
                
                <div class="form-group @error('street_address') error @enderror">
                    <label for="street_address">Ulica i numer *</label>
                    <input type="text" id="street_address" name="street_address" value="{{ old('street_address', $address->street_address ?? '') }}" required>
                    @error('street_address')<div class="error-message">{{ $message }}</div>@enderror
                </div>
                
                <div class="form-group">
                    <label for="apartment">Mieszkanie / Piętro</label>
                    <input type="text" id="apartment" name="apartment" value="{{ old('apartment', $address->apartment ?? '') }}">
                </div>
                
                <div class="form-row">
                    <div class="form-group @error('city') error @enderror">
                        <label for="city">Miasto *</label>
                        <input type="text" id="city" name="city" value="{{ old('city', $address->city ?? '') }}" required>
                        @error('city')<div class="error-message">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="form-group @error('postal_code') error @enderror">
                        <label for="postal_code">Kod pocztowy *</label>
                        <input type="text" id="postal_code" name="postal_code" value="{{ old('postal_code', $address->postal_code ?? '') }}" pattern="[0-9]{2}-[0-9]{3}" placeholder="00-000" required>
                        @error('postal_code')<div class="error-message">{{ $message }}</div>@enderror
                    </div>
                </div>
                
                <div class="form-group @error('country') error @enderror">
                    <label for="country">Kraj *</label>
                    <select id="country" name="country" required>
                        <option value="Polska" {{ old('country', $address->country ?? 'Polska') == 'Polska' ? 'selected' : '' }}>Polska</option>
                    </select>
                    @error('country')<div class="error-message">{{ $message }}</div>@enderror
                </div>
                
                <div class="form-group">
                    <label for="phone">Telefon</label>
                    <input type="tel" id="phone" name="phone" value="{{ old('phone', $address->phone ?? '') }}">
                </div>
                
                <div class="form-group">
                    <div class="checkbox-group">
                        <input type="checkbox" id="is_default" name="is_default" value="1" {{ old('is_default', $address->is_default ?? false) ? 'checked' : '' }}>
                        <label for="is_default" style="margin-bottom: 0;">Ustaw jako adres domyślny</label>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> {{ $address ? 'Zapisz zmiany' : 'Dodaj adres' }}
                    </button>
                    <a href="{{ route('account.addresses') }}" class="btn btn-secondary">Anuluj</a>
                </div>
            </form>
        </main>
    </div>
</div>
@endsection
