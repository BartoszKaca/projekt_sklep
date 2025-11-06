
@extends('layouts.admin')

@section('title', 'Produkty')

@push('styles')
<style>
    .filters-bar {
        background: white;
        padding: 1.5rem;
        border-radius: 16px;
        border: 1px solid var(--border);
        margin-bottom: 1.5rem;
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
        align-items: center;
    }

    .filter-group {
        flex: 1;
        min-width: 200px;
    }

    .filter-label {
        display: block;
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--dark);
        margin-bottom: 0.5rem;
    }

    .filter-input, .filter-select {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid var(--border);
        border-radius: 10px;
        font-size: 0.875rem;
        transition: all 0.2s;
    }

    .filter-input:focus, .filter-select:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(99,102,241,0.1);
    }

    .btn {
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        border: none;
        font-weight: 600;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
    }

    .btn-primary {
        background: var(--primary);
        color: white;
    }

    .btn-primary:hover {
        background: var(--primary-dark);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(99,102,241,0.3);
    }

    .btn-secondary {
        background: var(--light-gray);
        color: var(--dark);
    }

    .btn-secondary:hover {
        background: var(--border);
    }

    .table-card {
        background: white;
        border-radius: 16px;
        border: 1px solid var(--border);
        overflow: hidden;
    }

    .table-header {
        padding: 1.5rem;
        border-bottom: 1px solid var(--border);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
    }

    .table th {
        background: var(--light-gray);
        padding: 1rem 1.5rem;
        text-align: left;
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--gray);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .table td {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid var(--light-gray);
    }

    .table tr:hover td {
        background: var(--light-gray);
    }

    .product-cell {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .product-image {
        width: 60px;
        height: 60px;
        border-radius: 10px;
        object-fit: cover;
        background: var(--light-gray);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--gray);
        font-size: 1.5rem;
    }

    .product-info {
        flex: 1;
    }

    .product-name {
        font-weight: 600;
        color: var(--dark);
        margin-bottom: 0.25rem;
    }

    .product-meta {
        font-size: 0.875rem;
        color: var(--gray);
    }

    .badge {
        display: inline-block;
        padding: 0.375rem 0.75rem;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .badge-success {
        background: #d1fae5;
        color: #065f46;
    }

    .badge-warning {
        background: #fef3c7;
        color: #92400e;
    }

    .badge-danger {
        background: #fee2e2;
        color: #991b1b;
    }

    .badge-info {
        background: #dbeafe;
        color: #1e40af;
    }

    .stock-indicator {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .stock-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
    }

    .stock-dot.high {
        background: var(--success);
    }

    .stock-dot.low {
        background: var(--warning);
    }

    .stock-dot.out {
        background: var(--danger);
    }

    .action-btns {
        display: flex;
        gap: 0.5rem;
    }

    .action-btn {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        border: none;
        background: var(--light-gray);
        color: var(--dark);
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .action-btn:hover {
        transform: translateY(-2px);
    }

    .action-btn.edit:hover {
        background: var(--info);
        color: white;
    }

    .action-btn.stock:hover {
        background: var(--warning);
        color: white;
    }

    .action-btn.delete:hover {
        background: var(--danger);
        color: white;
    }

    .pagination {
        display: flex;
        justify-content: center;
        gap: 0.5rem;
        padding: 1.5rem;
    }

    .page-link {
        padding: 0.5rem 1rem;
        border-radius: 8px;
        border: 1px solid var(--border);
        background: white;
        color: var(--dark);
        text-decoration: none;
        transition: all 0.2s;
    }

    .page-link:hover {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
    }

    .page-link.active {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
    }

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        color: var(--gray);
    }

    .empty-state i {
        font-size: 4rem;
        opacity: 0.3;
        margin-bottom: 1rem;
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <div class="breadcrumb">
        <a href="{{ route('admin.dashboard') }}">Admin</a>
        <span>/</span>
        <span>Produkty</span>
    </div>
    <h1 class="page-title">Produkty</h1>
    <p class="page-subtitle">Zarządzaj swoim asortymentem płyt i mercha</p>
</div>

<!-- Filters -->
<form method="GET" class="filters-bar">
    <div class="filter-group">
        <label class="filter-label">Szukaj</label>
        <input type="text" name="search" class="filter-input" placeholder="Nazwa, SKU, artysta..." value="{{ request('search') }}">
    </div>

    <div class="filter-group">
        <label class="filter-label">Kategoria</label>
        <select name="category" class="filter-select">
            <option value="">Wszystkie kategorie</option>
            @foreach($categories ?? [] as $category)
            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                {{ $category->name }}
            </option>
            @endforeach
        </select>
    </div>

    <div class="filter-group">
        <label class="filter-label">Typ</label>
        <select name="type" class="filter-select">
            <option value="">Wszystkie typy</option>
            <option value="album" {{ request('type') == 'album' ? 'selected' : '' }}>Płyty</option>
            <option value="merch" {{ request('type') == 'merch' ? 'selected' : '' }}>Merch</option>
        </select>
    </div>

    <div class="filter-group">
        <label class="filter-label">Stan magazynowy</label>
        <select name="stock" class="filter-select">
            <option value="">Wszystkie</option>
            <option value="low" {{ request('stock') == 'low' ? 'selected' : '' }}>Niski stan</option>
            <option value="out" {{ request('stock') == 'out' ? 'selected' : '' }}>Brak w magazynie</option>
        </select>
    </div>

    <div class="filter-group">
        <label class="filter-label">&nbsp;</label>
        <div style="display: flex; gap: 0.5rem;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i> Filtruj
            </button>
            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Wyczyść
            </a>
        </div>
    </div>
</form>

<!-- Products Table -->
<div class="table-card">
    <div class="table-header">
        <h2 class="card-title">
            Lista produktów ({{ $products->total() ?? 0 }})
        </h2>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Dodaj produkt
        </a>
    </div>

    @if(($products->count() ?? 0) > 0)
    <table class="table">
        <thead>
            <tr>
                <th>Produkt</th>
                <th>Kategoria</th>
                <th>Cena</th>
                <th>Stan</th>
                <th>Status</th>
                <th style="text-align: right;">Akcje</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
            <tr>
                <td>
                    <div class="product-cell">
                        <div class="product-image">
                            @if($product->primaryImage)
                            <img src="{{ asset('storage/' . $product->primaryImage->path) }}" alt="{{ $product->name }}">
                            @else
                            <i class="fas fa-compact-disc"></i>
                            @endif
                        </div>
                        <div class="product-info">
                            <div class="product-name">{{ $product->name }}</div>
                            <div class="product-meta">
                                SKU: {{ $product->sku }}
                                @if($product->artist)
                                • {{ $product->artist }}
                                @endif
                            </div>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="badge badge-info">{{ $product->category->name }}</span>
                </td>
                <td>
                    <div style="font-weight: 600; color: var(--dark);">{{ number_format($product->getFinalPrice(), 2) }} zł</div>
                    @if($product->discount_price)
                    <div style="font-size: 0.875rem; color: var(--gray); text-decoration: line-through;">
                        {{ number_format($product->price, 2) }} zł
                    </div>
                    @endif
                </td>
                <td>
                    <div class="stock-indicator">
                        <span class="stock-dot {{ $product->stock_quantity == 0 ? 'out' : ($product->isLowStock() ? 'low' : 'high') }}"></span>
                        <span style="font-weight: 600;">{{ $product->stock_quantity }}</span>
                        @if($product->isLowStock())
                        <span class="badge badge-warning">
                            <i class="fas fa-exclamation-triangle"></i> Niski
                        </span>
                        @endif
                    </div>
                </td>
                <td>
                    @if($product->is_active)
                    <span class="badge badge-success">
                        <i class="fas fa-check"></i> Aktywny
                    </span>
                    @else
                    <span class="badge badge-danger">
                        <i class="fas fa-times"></i> Nieaktywny
                    </span>
                    @endif
                </td>
                <td>
                    <div class="action-btns" style="justify-content: flex-end;">
                        <a href="{{ route('admin.products.edit', $product) }}" class="action-btn edit" title="Edytuj">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="{{ route('admin.products.stock', $product) }}" class="action-btn stock" title="Stan magazynowy">
                            <i class="fas fa-warehouse"></i>
                        </a>
                        <form method="POST" action="{{ route('admin.products.destroy', $product) }}" style="margin: 0;" onsubmit="return confirm('Czy na pewno chcesz usunąć ten produkt?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="action-btn delete" title="Usuń">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Pagination -->
    <div class="pagination">
        {{ $products->links() }}
    </div>
    @else
    <div class="empty-state">
        <i class="fas fa-box-open"></i>
        <h3>Brak produktów</h3>
        <p>Nie znaleziono produktów pasujących do wybranych filtrów.</p>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary" style="margin-top: 1rem;">
            <i class="fas fa-plus"></i> Dodaj pierwszy produkt
        </a>
    </div>
    @endif
</div>
@endsection

<!-- 
Zapisz jako: resources/views/admin/products/index.blade.php
-->