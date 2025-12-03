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

    .order-section {
        background: white;
        padding: 1.5rem;
        border-radius: 12px;
        border: 1px solid var(--border);
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }

    .section-title {
        font-size: 1.125rem;
        font-weight: 700;
        margin-bottom: 1rem;
    }

    .card-action {
        color: var(--primary);
        text-decoration: none;
        font-size: 0.875rem;
        font-weight: 600;
        transition: all 0.2s;
    }

    .card-action:hover {
        color: var(--primary-dark);
    }

    .status-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .status-badge.pending {
        background: #fef3c7;
        color: #92400e;
    }

    .status-badge.processing {
        background: #dbeafe;
        color: #1e40af;
    }

    .status-badge.shipped {
        background: #e0e7ff;
        color: #3730a3;
    }

    .status-badge.delivered {
        background: #d1fae5;
        color: #065f46;
    }

    .status-badge.cancelled {
        background: #fee2e2;
        color: #991b1b;
    }

    .info-grid {
        display: grid;
        gap: 1rem;
    }

    .info-item {
        display: flex;
        justify-content: space-between;
        padding: 0.75rem;
        background: var(--light-gray);
        border-radius: 8px;
    }

    .info-label {
        font-size: 0.875rem;
        color: var(--gray);
    }

    .info-value {
        font-weight: 600;
    }

    .form-group {
        margin-bottom: 1rem;
    }

    .form-label {
        display: block;
        font-weight: 600;
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
    }

    .form-input, .form-select {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid var(--border);
        border-radius: 8px;
        font-size: 0.875rem;
        transition: all 0.2s;
    }

    .form-input:focus, .form-select:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
    }

    .checkbox-group {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .checkbox-group input[type="checkbox"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
    }

    .checkbox-group label {
        cursor: pointer;
        font-weight: 500;
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
                    <input type="text" name="name" class="form-input" value="{{ old('name', $user->name) }}" required>
                    @error('name')
                        <span style="color: var(--danger); font-size: 0.75rem;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-input" value="{{ old('email', $user->email) }}" required>
                    @error('email')
                        <span style="color: var(--danger); font-size: 0.75rem;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Telefon</label>
                    <input type="text" name="phone" class="form-input" value="{{ old('phone', $user->phone) }}">
                    @error('phone')
                        <span style="color: var(--danger); font-size: 0.75rem;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Rola</label>
                    <select name="role" class="form-select">
                        <option value="customer" {{ old('role', $user->role) == 'customer' ? 'selected' : '' }}>Klient</option>
                        <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Administrator</option>
                    </select>
                    @error('role')
                        <span style="color: var(--danger); font-size: 0.75rem;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <div class="checkbox-group">
                        <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                        <label for="is_active">Konto aktywne</label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    <i class="fas fa-save"></i> Zapisz zmiany
                </button>
            </form>

            @if(session('success'))
                <div style="margin-top: 1rem; padding: 0.75rem; background: #d1fae5; color: #065f46; border-radius: 8px; font-size: 0.875rem;">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif
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

        <!-- Danger Zone -->
        <div class="order-section" style="margin-top: 1.5rem; border-color: #fee2e2;">
            <h3 class="section-title" style="color: #991b1b;">Strefa niebezpieczna</h3>
            
            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" 
                  onsubmit="return confirm('Czy na pewno chcesz usunąć tego użytkownika? Ta operacja jest nieodwracalna!');">
                @csrf
                @method('DELETE')
                
                <p style="font-size: 0.875rem; color: var(--gray); margin-bottom: 1rem;">
                    Usunięcie użytkownika jest operacją nieodwracalną. Wszystkie dane użytkownika zostaną trwale usunięte.
                </p>
                
                <button type="submit" class="btn" style="width: 100%; background: #fee2e2; color: #991b1b; border: 1px solid #fecaca;">
                    <i class="fas fa-trash"></i> Usuń użytkownika
                </button>
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <div>
        <!-- Order History -->
        <div class="order-section">
            <div class="section-header">
                <h3 class="section-title">Historia zamówień ({{ $user->orders->count() }})</h3>
                @if($user->orders->count() > 0)
                <a href="{{ route('admin.orders.index') }}?user_id={{ $user->id }}" class="card-action">
                    Zobacz wszystkie <i class="fas fa-arrow-right"></i>
                </a>
                @endif
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
                    <div style="font-weight: 600;">
                        @if($review->product)
                            <a href="{{ route('admin.products.edit', $review->product) }}" style="color: var(--primary); text-decoration: none;">
                                {{ $review->product->name }}
                            </a>
                        @else
                            <span style="color: var(--gray);">Produkt usunięty</span>
                        @endif
                    </div>
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
