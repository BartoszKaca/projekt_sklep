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
        flex-wrap: wrap;
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

    .action-btns {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .action-btns .btn {
        white-space: nowrap;
    }

    .stats-overview {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .stat-card {
        background: white;
        padding: 1.5rem;
        border-radius: 12px;
        border: 1px solid var(--border);
    }

    .stat-card-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 1rem;
    }

    .stat-card-value {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
    }

    .stat-card-label {
        color: var(--gray);
        font-size: 0.875rem;
    }

    @media (max-width: 768px) {
        .user-card {
            grid-template-columns: 1fr;
        }

        .user-avatar-large {
            width: 60px;
            height: 60px;
            font-size: 1.5rem;
        }

        .action-btns {
            flex-direction: row;
        }
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

<!-- Statistics Overview -->
<div class="stats-overview">
    <div class="stat-card">
        <div class="stat-card-icon" style="background: #dbeafe; color: #1e40af;">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-card-value">{{ $users->total() }}</div>
        <div class="stat-card-label">Wszystkich użytkowników</div>
    </div>

    <div class="stat-card">
        <div class="stat-card-icon" style="background: #fef3c7; color: #92400e;">
            <i class="fas fa-user-shield"></i>
        </div>
        <div class="stat-card-value">{{ $users->where('role', 'admin')->count() }}</div>
        <div class="stat-card-label">Administratorów</div>
    </div>

    <div class="stat-card">
        <div class="stat-card-icon" style="background: #d1fae5; color: #065f46;">
            <i class="fas fa-user"></i>
        </div>
        <div class="stat-card-value">{{ $users->where('role', 'customer')->count() }}</div>
        <div class="stat-card-label">Klientów</div>
    </div>

    <div class="stat-card">
        <div class="stat-card-icon" style="background: #e0e7ff; color: #3730a3;">
            <i class="fas fa-user-check"></i>
        </div>
        <div class="stat-card-value">{{ $users->where('is_active', true)->count() }}</div>
        <div class="stat-card-label">Aktywnych</div>
    </div>
</div>

<!-- Filters -->
<form method="GET" class="users-filters">
    <div class="filter-group" style="flex: 1; min-width: 250px;">
        <label class="filter-label">Szukaj</label>
        <input type="text" name="search" class="filter-input" 
               placeholder="Imię, email, telefon..." value="{{ request('search') }}">
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
        <label class="filter-label">Status</label>
        <select name="status" class="filter-select">
            <option value="">Wszystkie statusy</option>
            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktywny</option>
            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Nieaktywny</option>
        </select>
    </div>

    <div class="filter-group">
        <label class="filter-label">Sortuj</label>
        <select name="sort" class="filter-select">
            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Najnowsi</option>
            <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Najstarsi</option>
            <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Nazwa A-Z</option>
            <option value="orders" {{ request('sort') == 'orders' ? 'selected' : '' }}>Najwięcej zamówień</option>
        </select>
    </div>

    <div class="filter-group">
        <label class="filter-label">&nbsp;</label>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-search"></i> Filtruj
        </button>
    </div>

    @if(request()->anyFilled(['search', 'role', 'status', 'sort']))
    <div class="filter-group">
        <label class="filter-label">&nbsp;</label>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
            <i class="fas fa-times"></i> Wyczyść
        </a>
    </div>
    @endif
</form>

@if(session('success'))
<div style="background: #d1fae5; color: #065f46; padding: 1rem 1.5rem; border-radius: 12px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem;">
    <i class="fas fa-check-circle"></i>
    <span>{{ session('success') }}</span>
</div>
@endif

@if(session('error'))
<div style="background: #fee2e2; color: #991b1b; padding: 1rem 1.5rem; border-radius: 12px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem;">
    <i class="fas fa-exclamation-circle"></i>
    <span>{{ session('error') }}</span>
</div>
@endif

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

        <div style="display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap;">
            <span class="role-badge {{ $user->role }}">
                <i class="fas fa-{{ $user->role == 'admin' ? 'user-shield' : 'user' }}"></i>
                {{ $user->role == 'admin' ? 'Administrator' : 'Klient' }}
            </span>

            <span class="badge {{ $user->is_active ? 'badge-success' : 'badge-danger' }}">
                {{ $user->is_active ? 'Aktywny' : 'Nieaktywny' }}
            </span>

            @if($user->email_verified_at)
            <span class="badge badge-info">
                <i class="fas fa-check-circle"></i> Zweryfikowany
            </span>
            @endif
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

    <div class="action-btns">
        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-primary">
            <i class="fas fa-eye"></i> Zobacz profil
        </a>
        
        @if($user->is_active)
        <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}" style="display: inline;">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn btn-warning" onclick="return confirm('Czy na pewno chcesz dezaktywować tego użytkownika?');">
                <i class="fas fa-ban"></i> Dezaktywuj
            </button>
        </form>
        @else
        <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}" style="display: inline;">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn btn-success">
                <i class="fas fa-check"></i> Aktywuj
            </button>
        </form>
        @endif
    </div>
</div>
@empty
<div class="empty-state" style="background: white; padding: 4rem; border-radius: 16px; text-align: center;">
    <i class="fas fa-users" style="font-size: 4rem; color: var(--gray); opacity: 0.3; margin-bottom: 1.5rem;"></i>
    <h3>Brak użytkowników</h3>
    <p style="color: var(--gray);">Nie znaleziono użytkowników pasujących do kryteriów</p>
    
    @if(request()->anyFilled(['search', 'role', 'status']))
    <a href="{{ route('admin.users.index') }}" class="btn btn-primary" style="margin-top: 1.5rem;">
        <i class="fas fa-arrow-left"></i> Wyczyść filtry
    </a>
    @endif
</div>
@endforelse

<!-- Pagination -->
@if($users->hasPages())
<div style="display: flex; justify-content: center; margin-top: 2rem;">
    {{ $users->appends(request()->query())->links() }}
</div>
@endif
@endsection
