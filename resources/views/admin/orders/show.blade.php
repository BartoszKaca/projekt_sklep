{{-- resources/views/admin/orders/show.blade.php --}}
@extends('layouts.admin')

@section('title', 'Zamówienie #' . $order->order_number)

@push('styles')
<style>
    .order-detail-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 1.5rem;
    }

    .order-section {
        background: white;
        padding: 1.5rem;
        border-radius: 16px;
        border: 1px solid var(--border);
        margin-bottom: 1.5rem;
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid var(--light-gray);
    }

    .section-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: var(--dark);
    }

    .order-item-row {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        background: var(--light-gray);
        border-radius: 10px;
        margin-bottom: 0.75rem;
    }

    .item-image {
        width: 60px;
        height: 60px;
        border-radius: 8px;
        background: white;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--gray);
    }

    .item-details {
        flex: 1;
    }

    .item-name {
        font-weight: 600;
        margin-bottom: 0.25rem;
    }

    .item-meta {
        font-size: 0.875rem;
        color: var(--gray);
    }

    .item-price {
        text-align: right;
    }

    .price-row {
        display: flex;
        justify-content: space-between;
        padding: 0.75rem 0;
        font-size: 0.875rem;
    }

    .price-row.total {
        border-top: 2px solid var(--border);
        margin-top: 0.75rem;
        padding-top: 1rem;
        font-size: 1.125rem;
        font-weight: 700;
    }

    .info-grid {
        display: grid;
        gap: 1rem;
    }

    .info-item {
        padding: 1rem;
        background: var(--light-gray);
        border-radius: 10px;
    }

    .info-label {
        font-size: 0.75rem;
        color: var(--gray);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.5rem;
    }

    .info-value {
        font-weight: 600;
        color: var(--dark);
    }

    .status-timeline {
        position: relative;
        padding-left: 2rem;
    }

    .timeline-item {
        position: relative;
        padding-bottom: 1.5rem;
    }

    .timeline-item:last-child {
        padding-bottom: 0;
    }

    .timeline-item::before {
        content: '';
        position: absolute;
        left: -2rem;
        top: 0;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: var(--light-gray);
        border: 2px solid var(--border);
    }

    .timeline-item.active::before {
        background: var(--primary);
        border-color: var(--primary);
    }

    .timeline-item.completed::before {
        background: var(--success);
        border-color: var(--success);
    }

    .timeline-item::after {
        content: '';
        position: absolute;
        left: calc(-2rem + 5px);
        top: 12px;
        width: 2px;
        height: calc(100% - 12px);
        background: var(--light-gray);
    }

    .timeline-item:last-child::after {
        display: none;
    }

    .timeline-status {
        font-weight: 600;
        margin-bottom: 0.25rem;
    }

    .timeline-date {
        font-size: 0.875rem;
        color: var(--gray);
    }

    .status-form {
        background: var(--light-gray);
        padding: 1.5rem;
        border-radius: 12px;
    }

    .form-row-inline {
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 1rem;
        align-items: end;
    }

    .notes-box {
        background: #fef3c7;
        border: 1px solid #fde68a;
        border-radius: 10px;
        padding: 1rem;
        margin-top: 1rem;
    }

    .notes-box h4 {
        font-weight: 600;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    @media (max-width: 1024px) {
        .order-detail-grid {
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
        <a href="{{ route('admin.orders.index') }}">Zamówienia</a>
        <span>/</span>
        <span>#{{ $order->order_number }}</span>
    </div>
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 class="page-title">Zamówienie #{{ $order->order_number }}</h1>
            <p class="page-subtitle">Złożone {{ $order->created_at->format('d.m.Y o H:i') }}</p>
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <button onclick="window.print()" class="btn btn-secondary">
                <i class="fas fa-print"></i> Drukuj
            </button>
        </div>
    </div>
</div>

<div class="order-detail-grid">
    <!-- Main Content -->
    <div>
        <!-- Order Items -->
        <div class="order-section">
            <div class="section-header">
                <h3 class="section-title">Zamówione produkty ({{ $order->items->count() }})</h3>
            </div>

            @foreach($order->items as $item)
            <div class="order-item-row">
                <div class="item-image">
                    <i class="fas fa-compact-disc"></i>
                </div>
                <div class="item-details">
                    <div class="item-name">{{ $item->product_name }}</div>
                    <div class="item-meta">
                        SKU: {{ $item->sku }}
                        @if($item->variant_name)
                        • {{ $item->variant_name }}
                        @endif
                    </div>
                </div>
                <div class="item-price">
                    <div style="font-size: 0.875rem; color: var(--gray);">{{ $item->quantity }} × {{ number_format($item->price, 2) }} zł</div>
                    <div style="font-weight: 700; font-size: 1.125rem;">{{ number_format($item->total, 2) }} zł</div>
                </div>
            </div>
            @endforeach

            <!-- Order Summary -->
            <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid var(--border);">
                <div class="price-row">
                    <span>Suma produktów:</span>
                    <strong>{{ number_format($order->subtotal, 2) }} zł</strong>
                </div>
                <div class="price-row">
                    <span>Dostawa:</span>
                    <strong>{{ number_format($order->shipping_cost, 2) }} zł</strong>
                </div>
                @if($order->discount > 0)
                <div class="price-row" style="color: var(--success);">
                    <span>Rabat @if($order->coupon_code)({{ $order->coupon_code }})@endif:</span>
                    <strong>-{{ number_format($order->discount, 2) }} zł</strong>
                </div>
                @endif
                @if($order->tax > 0)
                <div class="price-row">
                    <span>Podatek:</span>
                    <strong>{{ number_format($order->tax, 2) }} zł</strong>
                </div>
                @endif
                <div class="price-row total">
                    <span>Razem do zapłaty:</span>
                    <strong style="color: var(--primary);">{{ number_format($order->total, 2) }} zł</strong>
                </div>
            </div>
        </div>

        <!-- Shipping Info -->
        <div class="order-section">
            <div class="section-header">
                <h3 class="section-title">Dane do wysyłki</h3>
            </div>

            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Odbiorca</div>
                    <div class="info-value">{{ $order->shipping->first_name }} {{ $order->shipping->last_name }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Adres</div>
                    <div class="info-value">
                        {{ $order->shipping->street_address }}
                        @if($order->shipping->apartment), {{ $order->shipping->apartment }}@endif
                        <br>{{ $order->shipping->postal_code }} {{ $order->shipping->city }}
                        <br>{{ $order->shipping->country }}
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Telefon</div>
                    <div class="info-value">{{ $order->shipping->phone }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Email</div>
                    <div class="info-value">{{ $order->shipping->email }}</div>
                </div>
            </div>

            @if($order->customer_notes)
            <div class="notes-box" style="background: #dbeafe; border-color: #bfdbfe;">
                <h4><i class="fas fa-comment"></i> Uwagi klienta</h4>
                <p style="margin: 0; color: var(--dark);">{{ $order->customer_notes }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Sidebar -->
    <div>
        <!-- Status & Payment -->
        <div class="order-section">
            <h3 class="section-title" style="margin-bottom: 1.5rem;">Status zamówienia</h3>

            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Status zamówienia</div>
                    <div class="info-value">
                        <span class="status-badge {{ $order->status }}">{{ $order->status }}</span>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Status płatności</div>
                    <div class="info-value">
                        <span class="payment-badge {{ $order->payment_status }}">
                            {{ $order->payment_status }}
                        </span>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Metoda płatności</div>
                    <div class="info-value" style="text-transform: uppercase;">{{ $order->payment_method }}</div>
                </div>
            </div>

            <!-- Update Status Form -->
            <form method="POST" action="{{ route('admin.orders.update-status', $order) }}" class="status-form" style="margin-top: 1.5rem;">
                @csrf
                @method('PATCH')
                
                <div class="form-group">
                    <label class="form-label">Zmień status</label>
                    <select name="status" class="form-select">
                        <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Oczekujące</option>
                        <option value="confirmed" {{ $order->status == 'confirmed' ? 'selected' : '' }}>Potwierdzone</option>
                        <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>W realizacji</option>
                        <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Wysłane</option>
                        <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Dostarczone</option>
                        <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Anulowane</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Notatki admina</label>
                    <textarea name="admin_notes" class="form-textarea" placeholder="Opcjonalne notatki...">{{ $order->admin_notes }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    <i class="fas fa-save"></i> Aktualizuj status
                </button>
            </form>

            <!-- Update Payment Status -->
            <form method="POST" action="{{ route('admin.orders.update-payment', $order) }}" style="margin-top: 1rem;">
                @csrf
                @method('PATCH')
                
                <div class="form-row-inline">
                    <select name="payment_status" class="form-select">
                        <option value="pending" {{ $order->payment_status == 'pending' ? 'selected' : '' }}>Oczekuje</option>
                        <option value="paid" {{ $order->payment_status == 'paid' ? 'selected' : '' }}>Opłacone</option>
                        <option value="failed" {{ $order->payment_status == 'failed' ? 'selected' : '' }}>Błąd</option>
                        <option value="refunded" {{ $order->payment_status == 'refunded' ? 'selected' : '' }}>Zwrot</option>
                    </select>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check"></i>
                    </button>
                </div>
            </form>
        </div>

        <!-- Tracking -->
        @if($order->status == 'shipped' || $order->tracking_number)
        <div class="order-section">
            <h3 class="section-title" style="margin-bottom: 1.5rem;">Śledzenie przesyłki</h3>

            @if($order->tracking_number)
            <div class="info-item" style="margin-bottom: 1rem;">
                <div class="info-label">Numer przesyłki</div>
                <div class="info-value" style="font-family: monospace; font-size: 1.125rem;">
                    {{ $order->tracking_number }}
                </div>
            </div>
            <div class="info-item">
                <div class="info-label">Kurier</div>
                <div class="info-value">{{ $order->carrier ?? 'InPost' }}</div>
            </div>
            @else
            <form method="POST" action="{{ route('admin.orders.update-status', $order) }}">
                @csrf
                @method('PATCH')
                <input type="hidden" name="status" value="shipped">
                
                <div class="form-group">
                    <label class="form-label">Numer tracking</label>
                    <input type="text" name="tracking_number" class="form-input" placeholder="Np. PL123456789" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Kurier</label>
                    <select name="carrier" class="form-select">
                        <option value="InPost">InPost</option>
                        <option value="DPD">DPD</option>
                        <option value="DHL">DHL</option>
                        <option value="Poczta Polska">Poczta Polska</option>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    <i class="fas fa-truck"></i> Oznacz jako wysłane
                </button>
            </form>
            @endif
        </div>
        @endif

        <!-- Timeline -->
        <div class="order-section">
            <h3 class="section-title" style="margin-bottom: 1.5rem;">Historia</h3>

            <div class="status-timeline">
                <div class="timeline-item completed">
                    <div class="timeline-status">Zamówienie złożone</div>
                    <div class="timeline-date">{{ $order->created_at->format('d.m.Y H:i') }}</div>
                </div>

                @if($order->paid_at)
                <div class="timeline-item completed">
                    <div class="timeline-status">Płatność otrzymana</div>
                    <div class="timeline-date">{{ $order->paid_at->format('d.m.Y H:i') }}</div>
                </div>
                @endif

                @if($order->shipped_at)
                <div class="timeline-item completed">
                    <div class="timeline-status">Przesyłka wysłana</div>
                    <div class="timeline-date">{{ $order->shipped_at->format('d.m.Y H:i') }}</div>
                </div>
                @endif

                @if($order->delivered_at)
                <div class="timeline-item completed">
                    <div class="timeline-status">Dostarczone</div>
                    <div class="timeline-date">{{ $order->delivered_at->format('d.m.Y H:i') }}</div>
                </div>
                @endif
            </div>
        </div>

        <!-- Customer Info -->
        <div class="order-section">
            <h3 class="section-title" style="margin-bottom: 1.5rem;">Klient</h3>

            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Nazwa</div>
                    <div class="info-value">{{ $order->user->name ?? 'Gość' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Email</div>
                    <div class="info-value">{{ $order->user->email ?? '-' }}</div>
                </div>
                @if($order->user)
                <div class="info-item">
                    <div class="info-label">Poprzednie zamówienia</div>
                    <div class="info-value">{{ $order->user->orders->count() }}</div>
                </div>
                <a href="{{ route('admin.users.show', $order->user) }}" class="btn btn-secondary" style="width: 100%;">
                    <i class="fas fa-user"></i> Zobacz profil
                </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection