{{-- resources/views/admin/users/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Użytkownicy')

@push('styles')
<style>
    .users-filters {
        background: white;
        padding: 1.5rem;
        border-radius: 12px;
        border: 1px solid var(--border);
        margin-bottom: 1.5rem;
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
        align-items: end;
    }

    .user-card {
        background: white;
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1rem;
        display: grid;
        grid-template-columns: auto 1fr auto;
        gap: 1.5rem;
        align-items: center;
        transition: all 0.2s;
    }

    .user-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }

    .user-avatar-large {
        width: 80px;
        height: 80px;
        border-radius: 16px;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 2rem;
        font-weight: 700;
    }

    .user-details h3 {
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .user-meta {
        display: flex;
        gap: 1.5rem;
        color: var(--gray);
        font-size: 0.875rem;
        margin-bottom: 0.5rem;
    }

    .user-meta i {
        margin-right: 0.25rem;
    }

    .user-stats {
        display: flex;
        gap: 1rem;
        margin-top: 0.75rem;
    }

    .user-stat {
        padding: 0.5rem 1rem;
        background: var(--light-gray);
        border-radius: 8px;
        text-align: center;
    }

    .user-stat strong {
        display: block;
        font-weight: 700;
        margin-bottom: 0.25rem;
    }

    .user-stat span {
        font-size: 0.75rem;
        color: var(--gray);
    }

    .role-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 600;
    }

    .role-badge.admin {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white;
    }

    .role-badge.customer {
        background: var(--light-gray);
        color: var(--dark);
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <div class="breadcrumb">
        <a href="{{ route('admin.dashboard') }}">Admin</a>
        <span>/</span>
        <span>Użytkownicy</span>
    </div>
    <h1 class="page-title">Użytkownicy</h1>
    <p class="page-subtitle">Zarządzaj kontami klientów i administratorów</p>
</div>

<!-- Filters -->
<form method="GET" class="users-filters">
    <div class="filter-group" style="flex: 1; min-width: 250px;">
        <label class="filter-label">Szukaj</label>
        <input type="text" name="search" class="filter-input" 
               placeholder="Imię, email..." value="{{ request('search') }}">
    </div>

    <div class="filter-group">
        <label class="filter-label">Rola</label>
        <select name="role" class="filter-select">
            <option value="">Wszystkie role</option>
            <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Administrator</option>
            <option value="customer" {{ request('role') == 'customer' ? 'selected' : '' }}>Klient</option>
        </select>
    </div>

    <div class="filter-group">
        <label class="filter-label">&nbsp;</label>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-search"></i> Filtruj
        </button>
    </div>
</form>

<!-- Users List -->
@forelse($users as $user)
<div class="user-card">
    <div class="user-avatar-large">
        {{ strtoupper(substr($user->name, 0, 1)) }}
    </div>

    <div class="user-details">
        <h3>{{ $user->name }}</h3>
        
        <div class="user-meta">
            <span><i class="fas fa-envelope"></i> {{ $user->email }}</span>
            @if($user->phone)
            <span><i class="fas fa-phone"></i> {{ $user->phone }}</span>
            @endif
            <span><i class="fas fa-calendar"></i> Dołączył {{ $user->created_at->format('d.m.Y') }}</span>
        </div>

        <div style="display: flex; gap: 0.5rem; align-items: center;">
            <span class="role-badge {{ $user->role }}">
                <i class="fas fa-{{ $user->role == 'admin' ? 'user-shield' : 'user' }}"></i>
                {{ $user->role == 'admin' ? 'Administrator' : 'Klient' }}
            </span>

            <span class="badge {{ $user->is_active ? 'badge-success' : 'badge-danger' }}">
                {{ $user->is_active ? 'Aktywny' : 'Nieaktywny' }}
            </span>
        </div>

        <div class="user-stats">
            <div class="user-stat">
                <strong>{{ $user->orders_count ?? 0 }}</strong>
                <span>Zamówień</span>
            </div>
            <div class="user-stat">
                <strong>{{ number_format($user->orders->sum('total') ?? 0, 2) }} zł</strong>
                <span>Łączna wartość</span>
            </div>
            <div class="user-stat">
                <strong>{{ $user->reviews->count() ?? 0 }}</strong>
                <span>Opinii</span>
            </div>
        </div>
    </div>

    <div class="action-btns" style="flex-direction: column;">
        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-primary" style="white-space: nowrap;">
            <i class="fas fa-eye"></i> Zobacz profil
        </a>
    </div>
</div>
@empty
<div class="empty-state" style="background: white; padding: 4rem; border-radius: 16px;">
    <i class="fas fa-users"></i>
    <h3>Brak użytkowników</h3>
    <p>Nie znaleziono użytkowników pasujących do kryteriów</p>
</div>
@endforelse

<!-- Pagination -->
@if($users->hasPages())
<div style="display: flex; justify-content: center; margin-top: 2rem;">
    {{ $users->links() }}
</div>
@endif
@endsection


{{-- =========================================== --}}
{{-- resources/views/admin/users/show.blade.php --}}
@extends('layouts.admin')

@section('title', 'Profil użytkownika')

@push('styles')
<style>
    .profile-grid {
        display: grid;
        grid-template-columns: 1fr 2fr;
        gap: 1.5rem;
    }

    .profile-card {
        background: white;
        padding: 2rem;
        border-radius: 16px;
        border: 1px solid var(--border);
        text-align: center;
    }

    .profile-avatar {
        width: 120px;
        height: 120px;
        border-radius: 24px;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 3rem;
        font-weight: 700;
        margin: 0 auto 1.5rem;
    }

    .profile-name {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .profile-email {
        color: var(--gray);
        margin-bottom: 1rem;
    }

    .profile-stats-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
        margin-top: 1.5rem;
    }

    .stat-box {
        padding: 1rem;
        background: var(--light-gray);
        border-radius: 12px;
        text-align: center;
    }

    .stat-box strong {
        display: block;
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--primary);
        margin-bottom: 0.25rem;
    }

    .stat-box span {
        font-size: 0.875rem;
        color: var(--gray);
    }

    .order-history-item {
        padding: 1rem;
        background: var(--light-gray);
        border-radius: 10px;
        margin-bottom: 0.75rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.2s;
    }

    .order-history-item:hover {
        background: var(--border);
    }

    .address-card {
        padding: 1.25rem;
        background: var(--light-gray);
        border-radius: 12px;
        margin-bottom: 1rem;
        position: relative;
    }

    .address-card.default::before {
        content: 'Domyślny';
        position: absolute;
        top: 0.75rem;
        right: 0.75rem;
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.25rem 0.75rem;
        background: var(--primary);
        color: white;
        border-radius: 6px;
    }

    @media (max-width: 1024px) {
        .profile-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <div class="breadcrumb">
        <a href="{{ route('admin.dashboard') }}">Admin</a>
        <span>/</span>
        <a href="{{ route('admin.users.index') }}">Użytkownicy</a>
        <span>/</span>
        <span>{{ $user->name }}</span>
    </div>
    <h1 class="page-title">Profil użytkownika</h1>
    <p class="page-subtitle">Szczegóły konta i historia aktywności</p>
</div>

<div class="profile-grid">
    <!-- Sidebar -->
    <div>
        <!-- Profile Card -->
        <div class="profile-card">
            <div class="profile-avatar">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>

            <h2 class="profile-name">{{ $user->name }}</h2>
            <p class="profile-email">{{ $user->email }}</p>

            <div style="display: flex; gap: 0.5rem; justify-content: center; margin-bottom: 1rem;">
                <span class="role-badge {{ $user->role }}">
                    <i class="fas fa-{{ $user->role == 'admin' ? 'user-shield' : 'user' }}"></i>
                    {{ $user->role == 'admin' ? 'Administrator' : 'Klient' }}
                </span>

                <span class="badge {{ $user->is_active ? 'badge-success' : 'badge-danger' }}">
                    {{ $user->is_active ? 'Aktywny' : 'Nieaktywny' }}
                </span>
            </div>

            <div class="profile-stats-grid">
                <div class="stat-box">
                    <strong>{{ $user->orders->count() }}</strong>
                    <span>Zamówień</span>
                </div>
                <div class="stat-box">
                    <strong>{{ number_format($user->orders->sum('total'), 0) }} zł</strong>
                    <span>Wydane</span>
                </div>
                <div class="stat-box">
                    <strong>{{ $user->reviews->count() }}</strong>
                    <span>Opinii</span>
                </div>
                <div class="stat-box">
                    <strong>{{ $user->wishlist->count() }}</strong>
                    <span>Lista życzeń</span>
                </div>
            </div>
        </div>

        <!-- Edit User -->
        <div class="order-section" style="margin-top: 1.5rem;">
            <h3 class="section-title">Edycja użytkownika</h3>
            
            <form method="POST" action="{{ route('admin.users.update', $user) }}">
                @csrf
                @method('PATCH')
                
                <div class="form-group">
                    <label class="form-label">Imię i nazwisko</label>
                    <input type="text" name="name" class="form-input" value="{{ $user->name }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-input" value="{{ $user->email }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Telefon</label>
                    <input type="text" name="phone" class="form-input" value="{{ $user->phone }}">
                </div>

                <div class="form-group">
                    <label class="form-label">Rola</label>
                    <select name="role" class="form-select">
                        <option value="customer" {{ $user->role == 'customer' ? 'selected' : '' }}>Klient</option>
                        <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Administrator</option>
                    </select>
                </div>

                <div class="form-group">
                    <div class="checkbox-group">
                        <input type="checkbox" name="is_active" id="is_active" value="1" {{ $user->is_active ? 'checked' : '' }}>
                        <label for="is_active">Konto aktywne</label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    <i class="fas fa-save"></i> Zapisz zmiany
                </button>
            </form>
        </div>

        <!-- Info -->
        <div class="order-section" style="margin-top: 1.5rem;">
            <h3 class="section-title">Informacje</h3>
            
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Data rejestracji</div>
                    <div class="info-value">{{ $user->created_at->format('d.m.Y H:i') }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Ostatnia aktualizacja</div>
                    <div class="info-value">{{ $user->updated_at->format('d.m.Y H:i') }}</div>
                </div>
                @if($user->email_verified_at)
                <div class="info-item">
                    <div class="info-label">Email zweryfikowany</div>
                    <div class="info-value">
                        <i class="fas fa-check-circle" style="color: var(--success);"></i>
                        {{ $user->email_verified_at->format('d.m.Y') }}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div>
        <!-- Order History -->
        <div class="order-section">
            <div class="section-header">
                <h3 class="section-title">Historia zamówień ({{ $user->orders->count() }})</h3>
                <a href="{{ route('admin.orders.index') }}?user_id={{ $user->id }}" class="card-action">
                    Zobacz wszystkie <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            @forelse($user->orders->take(10) as $order)
            <div class="order-history-item">
                <div>
                    <div style="font-weight: 600; margin-bottom: 0.25rem;">
                        <a href="{{ route('admin.orders.show', $order) }}" style="color: var(--primary); text-decoration: none;">
                            #{{ $order->order_number }}
                        </a>
                    </div>
                    <div style="font-size: 0.875rem; color: var(--gray);">
                        {{ $order->created_at->format('d.m.Y H:i') }} • {{ $order->items->count() }} produktów
                    </div>
                </div>
                <div style="text-align: right;">
                    <div style="font-weight: 700; font-size: 1.125rem; margin-bottom: 0.25rem;">
                        {{ number_format($order->total, 2) }} zł
                    </div>
                    <span class="status-badge {{ $order->status }}">{{ $order->status }}</span>
                </div>
            </div>
            @empty
            <div style="text-align: center; padding: 3rem; color: var(--gray);">
                <i class="fas fa-shopping-bag" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.3;"></i>
                <p>Brak zamówień</p>
            </div>
            @endforelse
        </div>

        <!-- Addresses -->
        <div class="order-section" style="margin-top: 1.5rem;">
            <h3 class="section-title">Adresy ({{ $user->addresses->count() }})</h3>

            @forelse($user->addresses as $address)
            <div class="address-card {{ $address->is_default ? 'default' : '' }}">
                <h4 style="font-weight: 600; margin-bottom: 0.5rem;">
                    {{ $address->first_name }} {{ $address->last_name }}
                </h4>
                <p style="color: var(--gray); font-size: 0.875rem; margin-bottom: 0.5rem;">
                    {{ $address->street_address }}
                    @if($address->apartment), {{ $address->apartment }}@endif
                    <br>{{ $address->postal_code }} {{ $address->city }}
                    <br>{{ $address->country }}
                </p>
                <p style="color: var(--gray); font-size: 0.875rem;">
                    <i class="fas fa-phone"></i> {{ $address->phone }}
                </p>
            </div>
            @empty
            <div style="text-align: center; padding: 2rem; color: var(--gray); background: var(--light-gray); border-radius: 10px;">
                <i class="fas fa-map-marker-alt" style="font-size: 2rem; margin-bottom: 0.5rem; opacity: 0.3;"></i>
                <p>Brak zapisanych adresów</p>
            </div>
            @endforelse
        </div>

        <!-- Reviews -->
        @if($user->reviews->count() > 0)
        <div class="order-section" style="margin-top: 1.5rem;">
            <h3 class="section-title">Ostatnie opinie ({{ $user->reviews->count() }})</h3>

            @foreach($user->reviews->take(5) as $review)
            <div style="padding: 1rem; background: var(--light-gray); border-radius: 10px; margin-bottom: 0.75rem;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                    <div style="font-weight: 600;">{{ $review->product->name ?? 'Produkt usunięty' }}</div>
                    <div>
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star" style="color: {{ $i <= $review->rating ? '#f59e0b' : '#e5e7eb' }};"></i>
                        @endfor
                    </div>
                </div>
                @if($review->title)
                <div style="font-weight: 600; margin-bottom: 0.25rem;">{{ $review->title }}</div>
                @endif
                <p style="font-size: 0.875rem; color: var(--gray);">{{ $review->comment }}</p>
                <div style="margin-top: 0.5rem; font-size: 0.75rem; color: var(--gray);">
                    {{ $review->created_at->format('d.m.Y') }}
                    @if($review->is_approved)
                        <span class="badge badge-success" style="margin-left: 0.5rem;">Zatwierdzona</span>
                    @else
                        <span class="badge badge-warning" style="margin-left: 0.5rem;">Oczekuje</span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
@endsection