@extends('layouts.admin')

@section('title', 'Historia Ruchów Magazynowych')

@push('styles')
<style>
    .history-card {
        background: white;
        border-radius: 16px;
        border: 1px solid var(--border);
        overflow: hidden;
    }

    .history-header {
        padding: 1.5rem;
        border-bottom: 1px solid var(--border);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .history-table {
        width: 100%;
    }

    .history-table thead {
        background: var(--light-gray);
    }

    .history-table th {
        padding: 1rem 1.5rem;
        text-align: left;
        font-weight: 600;
        color: var(--dark);
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .history-table td {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid var(--light-gray);
        color: var(--gray);
    }

    .history-table tbody tr:hover {
        background: var(--light-gray);
    }

    .type-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.875rem;
    }

    .type-badge.in {
        background: #dcfce7;
        color: #166534;
    }

    .type-badge.out {
        background: #fee2e2;
        color: #991b1b;
    }

    .type-badge.adjustment {
        background: #dbeafe;
        color: #1e40af;
    }

    .product-cell {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .product-name {
        font-weight: 600;
        color: var(--dark);
    }

    .product-sku {
        font-size: 0.75rem;
        color: var(--gray);
    }

    .quantity-cell {
        font-weight: 700;
        font-size: 1.125rem;
    }

    .quantity-cell.positive {
        color: var(--success);
    }

    .quantity-cell.negative {
        color: var(--danger);
    }

    .pagination-wrapper {
        padding: 1.5rem;
        border-top: 1px solid var(--border);
        display: flex;
        justify-content: center;
    }

    @media (max-width: 768px) {
        .history-table {
            display: block;
            overflow-x: auto;
        }
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <div class="breadcrumb">
        <a href="{{ route('admin.dashboard') }}">Admin</a>
        <span>/</span>
        <a href="{{ route('admin.stock.index') }}">Magazyn</a>
        <span>/</span>
        <span>Historia</span>
    </div>
    <h1 class="page-title">Historia ruchów magazynowych</h1>
    <p class="page-subtitle">Przeglądaj wszystkie operacje na stanach magazynowych</p>
</div>

<div class="card-actions" style="margin-bottom: 1.5rem;">
    <a href="{{ route('admin.stock.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Powrót do magazynu
    </a>
</div>

<div class="history-card">
    <div class="history-header">
        <h3 class="card-title">
            <i class="fas fa-history"></i>
            Wszystkie ruchy magazynowe
        </h3>
        <div>
            <span class="badge badge-primary">{{ $movements->total() }} rekordów</span>
        </div>
    </div>

    <table class="history-table">
        <thead>
            <tr>
                <th>Data</th>
                <th>Produkt</th>
                <th>Typ ruchu</th>
                <th>Ilość</th>
                <th>Poprzedni stan</th>
                <th>Nowy stan</th>
                <th>Użytkownik</th>
                <th>Powód</th>
            </tr>
        </thead>
        <tbody>
            @forelse($movements as $movement)
                <tr>
                    <td>
                        <div style="font-weight: 600;">{{ $movement->created_at->format('d.m.Y') }}</div>
                        <div style="font-size: 0.75rem; color: var(--gray);">{{ $movement->created_at->format('H:i:s') }}</div>
                    </td>
                    <td>
                        <div class="product-cell">
                            <span class="product-name">{{ $movement->product->name ?? 'N/A' }}</span>
                            @if($movement->product)
                                <span class="product-sku">SKU: {{ $movement->product->sku }}</span>
                            @endif
                            @if($movement->variant)
                                <span class="product-sku">Wariant: {{ $movement->variant->name }}</span>
                            @endif
                        </div>
                    </td>
                    <td>
                        @php
                            $typeLabels = [
                                'in' => ['label' => 'Przyjęcie', 'icon' => 'plus'],
                                'out' => ['label' => 'Wydanie', 'icon' => 'minus'],
                                'adjustment' => ['label' => 'Korekta', 'icon' => 'edit'],
                            ];
                            $type = $typeLabels[$movement->type] ?? ['label' => $movement->type, 'icon' => 'circle'];
                        @endphp
                        <span class="type-badge {{ $movement->type }}">
                            <i class="fas fa-{{ $type['icon'] }}"></i>
                            {{ $type['label'] }}
                        </span>
                    </td>
                    <td>
                        <span class="quantity-cell {{ $movement->type == 'in' ? 'positive' : 'negative' }}">
                            {{ $movement->type == 'out' ? '-' : '+' }}{{ $movement->quantity }}
                        </span>
                    </td>
                    <td style="font-weight: 600;">{{ $movement->stock_before ?? '-' }}</td>
                    <td style="font-weight: 600; color: var(--primary);">{{ $movement->stock_after ?? '-' }}</td>
                    <td>
                        @if($movement->user)
                            <div style="font-weight: 600;">{{ $movement->user->name }}</div>
                            <div style="font-size: 0.75rem; color: var(--gray);">{{ $movement->user->email }}</div>
                        @else
                            <span style="color: var(--gray);">System</span>
                        @endif
                    </td>
                    <td>
                        <div>{{ $movement->reason ?? '-' }}</div>
                        @if($movement->reference)
                            <div style="font-size: 0.75rem; color: var(--gray); margin-top: 0.25rem;">
                                Ref: {{ $movement->reference }}
                            </div>
                        @endif
                        @if($movement->order)
                            <div style="font-size: 0.75rem; color: var(--gray); margin-top: 0.25rem;">
                                Zamówienie: #{{ $movement->order->order_number }}
                            </div>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 3rem;">
                        <div class="empty-state">
                            <i class="fas fa-history" style="font-size: 3rem; color: var(--gray); margin-bottom: 1rem;"></i>
                            <h3>Brak historii ruchów</h3>
                            <p style="color: var(--gray);">Nie znaleziono żadnych operacji magazynowych.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if($movements->hasPages())
    <div class="pagination-wrapper">
        {{ $movements->links() }}
    </div>
    @endif
</div>

@endsection
