@extends('layouts.admin')

@section('title', 'Ruchy magazynowe - ' . $product->name)

@push('styles')
<style>
    .stock-movement-item {
        display: grid;
        grid-template-columns: auto 1fr auto auto;
        gap: 1rem;
        align-items: center;
        padding: 1rem;
        background: var(--light-gray);
        border-radius: 10px;
        margin-bottom: 0.75rem;
        transition: all 0.2s;
    }

    .stock-movement-item:hover {
        background: var(--border);
    }

    .movement-type {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        color: white;
    }

    .movement-type.in {
        background: var(--success);
    }

    .movement-type.out {
        background: var(--danger);
    }

    .adjustment-form {
        background: white;
        padding: 1.5rem;
        border-radius: 12px;
        border: 1px solid var(--border);
        margin-bottom: 2rem;
    }

    .form-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <div class="breadcrumb">
        <a href="{{ route('admin.dashboard') }}">Admin</a>
        <span>/</span>
        <a href="{{ route('admin.products.index') }}">Produkty</a>
        <span>/</span>
        <a href="{{ route('admin.products.edit', $product) }}">{{ $product->name }}</a>
        <span>/</span>
        <span>Magazyn</span>
    </div>
    <h1 class="page-title">Ruchy magazynowe</h1>
    <p class="page-subtitle">{{ $product->name }}</p>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
    <div>
        <!-- Add Stock Adjustment Form -->
        <div class="adjustment-form">
            <h3 class="section-title" style="margin-bottom: 1.5rem;">Dodaj ruch magazynowy</h3>

            <form method="POST" action="{{ route('admin.products.adjust-stock', $product) }}">
                @csrf

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Typ ruchu</label>
                        <select name="type" class="form-select" required>
                            <option value="">Wybierz typ...</option>
                            <option value="in">Przychód (dodaj)</option>
                            <option value="out">Rozchód (odejmij)</option>
                            <option value="adjustment">Korekcja (ustaw)</option>
                        </select>
                        @error('type')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Ilość</label>
                        <input type="number" name="quantity" class="form-input" 
                               min="1" required placeholder="0">
                        @error('quantity')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Powód</label>
                        <select name="reason" class="form-select" required>
                            <option value="">Wybierz powód...</option>
                            <option value="restock">Uzupełnienie</option>
                            <option value="return">Zwrot</option>
                            <option value="damage">Uszkodzenie</option>
                            <option value="loss">Strata</option>
                            <option value="transfer">Transfer</option>
                            <option value="inventory_count">Inwentaryzacja</option>
                            <option value="other">Inne</option>
                        </select>
                        @error('reason')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Reference (opcjonalnie)</label>
                        <input type="text" name="reference" class="form-input" 
                               placeholder="Nr dokumentu, PO, itp.">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    <i class="fas fa-plus"></i> Dodaj ruch
                </button>
            </form>
        </div>

        <!-- Stock Movements List -->
        <div>
            <h3 class="section-title" style="margin-bottom: 1.5rem;">Historia ruchów ({{ $movements->count() }})</h3>

            @forelse($movements as $movement)
            <div class="stock-movement-item">
                <div class="movement-type {{ $movement->type }}">
                    {{ $movement->type === 'in' ? '↑' : '↓' }}
                </div>
                <div>
                    <div style="font-weight: 600; margin-bottom: 0.25rem;">
                        {{ $movement->type === 'in' ? 'Przychód' : 'Rozchód' }}:
                        <strong>{{ $movement->quantity }}</strong> szt.
                    </div>
                    <div style="font-size: 0.875rem; color: var(--gray);">
                        <strong>{{ ucfirst(str_replace('_', ' ', $movement->reason)) }}</strong>
                        @if($movement->reference)
                        • {{ $movement->reference }}
                        @endif
                    </div>
                    @if($movement->user)
                    <div style="font-size: 0.8rem; color: var(--gray);">
                        {{ $movement->user->name ?? 'System' }}
                    </div>
                    @endif
                </div>
                <div style="text-align: right;">
                    <div style="font-size: 0.875rem; color: var(--gray); margin-bottom: 0.25rem;">
                        {{ $movement->created_at->format('d.m.Y') }}
                    </div>
                    <div style="font-size: 0.875rem; color: var(--gray);">
                        {{ $movement->created_at->format('H:i') }}
                    </div>
                </div>
            </div>
            @empty
            <div style="text-align: center; padding: 3rem; color: var(--gray);">
                <i class="fas fa-inbox" style="font-size: 2.5rem; opacity: 0.3; margin-bottom: 1rem;"></i>
                <p>Brak ruchów magazynowych</p>
            </div>
            @endforelse

            @if($movements->hasPages())
            <div style="margin-top: 1.5rem;">
                {{ $movements->links() }}
            </div>
            @endif
        </div>
    </div>

    <!-- Sidebar with Product Info -->
    <div>
        <div style="background: white; padding: 1.5rem; border-radius: 12px; border: 1px solid var(--border);">
            <h4 style="font-weight: 600; margin-bottom: 1.5rem;">Informacje o produkcie</h4>

            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <div>
                    <div style="font-size: 0.75rem; color: var(--gray); text-transform: uppercase;">Nazwa</div>
                    <div style="font-weight: 600;">{{ $product->name }}</div>
                </div>

                <div>
                    <div style="font-size: 0.75rem; color: var(--gray); text-transform: uppercase;">SKU</div>
                    <div style="font-family: monospace; font-weight: 600;">{{ $product->sku }}</div>
                </div>

                <div>
                    <div style="font-size: 0.75rem; color: var(--gray); text-transform: uppercase;">Stan obecny</div>
                    <div style="font-size: 1.5rem; font-weight: 700;">
                        {{ $product->stock_quantity }}
                        <span style="font-size: 0.875rem; font-weight: 400;">szt.</span>
                    </div>
                </div>

                <div style="padding: 1rem; background: var(--light-gray); border-radius: 10px;">
                    <div style="font-size: 0.75rem; color: var(--gray); text-transform: uppercase; margin-bottom: 0.5rem;">Próg niskiego stanu</div>
                    <div style="font-size: 1.25rem; font-weight: 700;">{{ $product->low_stock_threshold }}</div>
                    <div style="font-size: 0.875rem; color: var(--gray); margin-top: 0.5rem;">
                        @if($product->isLowStock())
                        <i class="fas fa-exclamation-triangle" style="color: var(--warning);"></i>
                        Niski stan
                        @else
                        <i class="fas fa-check-circle" style="color: var(--success);"></i>
                        OK
                        @endif
                    </div>
                </div>

                <div>
                    <div style="font-size: 0.75rem; color: var(--gray); text-transform: uppercase;">Cena</div>
                    <div style="font-weight: 600;">{{ number_format($product->price, 2) }} zł</div>
                </div>

                <div style="padding-top: 1rem; border-top: 1px solid var(--border);">
                    <div style="font-size: 0.875rem; color: var(--gray); margin-bottom: 0.5rem;">Wartość zapasów</div>
                    <div style="font-size: 1.25rem; font-weight: 700; color: var(--primary);">
                        {{ number_format($product->stock_quantity * $product->price, 2) }} zł
                    </div>
                </div>

                <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-secondary" style="width: 100%;">
                    <i class="fas fa-edit"></i> Edytuj produkt
                </a>
            </div>
        </div>
    </div>
</div>
@endsection