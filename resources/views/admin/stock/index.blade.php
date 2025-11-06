@extends('layouts.admin')

@section('title', 'Magazyn')

@push('styles')
<style>
    .stock-summary {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .summary-card {
        background: white;
        padding: 1.25rem;
        border-radius: 12px;
        border: 1px solid var(--border);
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .summary-icon {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }

    .summary-icon.total {
        background: #dbeafe;
        color: #1e40af;
    }

    .summary-icon.low {
        background: #fef3c7;
        color: #92400e;
    }

    .summary-icon.out {
        background: #fee2e2;
        color: #991b1b;
    }

    .summary-info h3 {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 0.25rem;
    }

    .summary-info p {
        font-size: 0.875rem;
        color: var(--gray);
    }

    .stock-tabs {
        background: white;
        border-radius: 12px;
        border: 1px solid var(--border);
        padding: 0.5rem;
        display: inline-flex;
        gap: 0.5rem;
        margin-bottom: 1.5rem;
    }

    .tab-btn {
        padding: 0.75rem 1.5rem;
        border: none;
        background: transparent;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.875rem;
        color: var(--gray);
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-block;
    }

    .tab-btn:hover {
        background: var(--light-gray);
        color: var(--dark);
    }

    .tab-btn.active {
        background: var(--primary);
        color: white;
    }

    .stock-table-card {
        background: white;
        border-radius: 16px;
        border: 1px solid var(--border);
        overflow: hidden;
    }

    .stock-item {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr 1fr auto;
        gap: 1rem;
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--light-gray);
        align-items: center;
        transition: all 0.2s;
    }

    .stock-item:hover {
        background: var(--light-gray);
    }

    .stock-product {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .stock-img {
        width: 50px;
        height: 50px;
        border-radius: 8px;
        background: var(--light-gray);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--gray);
    }

    .stock-details h4 {
        font-weight: 600;
        color: var(--dark);
        margin-bottom: 0.25rem;
    }

    .stock-details p {
        font-size: 0.875rem;
        color: var(--gray);
    }

    .stock-level {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .stock-bar-wrapper {
        flex: 1;
        height: 8px;
        background: var(--light-gray);
        border-radius: 4px;
        overflow: hidden;
    }

    .stock-bar {
        height: 100%;
        transition: width 0.3s ease;
    }

    .stock-bar.high {
        background: var(--success);
    }

    .stock-bar.medium {
        background: var(--warning);
    }

    .stock-bar.low {
        background: var(--danger);
    }

    .stock-number {
        font-weight: 700;
        font-size: 1.125rem;
    }

    .stock-actions {
        display: flex;
        gap: 0.5rem;
    }

    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 9999;
        align-items: center;
        justify-content: center;
    }

    .modal.active {
        display: flex;
    }

    .modal-content {
        background: white;
        border-radius: 16px;
        width: 90%;
        max-width: 500px;
        max-height: 90vh;
        overflow-y: auto;
    }

    .modal-header {
        padding: 1.5rem;
        border-bottom: 1px solid var(--border);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-title {
        font-size: 1.25rem;
        font-weight: 700;
    }

    .modal-close {
        width: 32px;
        height: 32px;
        border: none;
        background: var(--light-gray);
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s;
    }

    .modal-close:hover {
        background: var(--danger);
        color: white;
    }

    .modal-body {
        padding: 1.5rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        display: block;
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: var(--dark);
    }

    .form-input, .form-select, .form-textarea {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid var(--border);
        border-radius: 10px;
        font-size: 0.875rem;
        transition: all 0.2s;
    }

    .form-textarea {
        min-height: 100px;
        resize: vertical;
    }

    .form-input:focus, .form-select:focus, .form-textarea:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(99,102,241,0.1);
    }

    .modal-footer {
        padding: 1.5rem;
        border-top: 1px solid var(--border);
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
    }

    @media (max-width: 768px) {
        .stock-item {
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
        <span>Magazyn</span>
    </div>
    <h1 class="page-title">Stan magazynu</h1>
    <p class="page-subtitle">Monitoruj i zarządzaj stanami magazynowymi</p>
</div>

<!-- Summary Cards -->
<div class="stock-summary">
    <div class="summary-card">
        <div class="summary-icon total">
            <i class="fas fa-boxes"></i>
        </div>
        <div class="summary-info">
            <h3>{{ $products->count() }}</h3>
            <p>Wszystkie produkty</p>
        </div>
    </div>

    <div class="summary-card">
        <div class="summary-icon low">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div class="summary-info">
            <h3>{{ $products->filter(fn($p) => $p->isLowStock())->count() }}</h3>
            <p>Niski stan</p>
        </div>
    </div>

    <div class="summary-card">
        <div class="summary-icon out">
            <i class="fas fa-times-circle"></i>
        </div>
        <div class="summary-info">
            <h3>{{ $products->where('stock_quantity', 0)->count() }}</h3>
            <p>Brak w magazynie</p>
        </div>
    </div>

    <div class="summary-card">
        <div class="summary-icon total">
            <i class="fas fa-download"></i>
        </div>
        <div class="summary-info">
            <h3>
                <a href="{{ route('admin.stock.export') }}" class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.875rem;">
                    <i class="fas fa-file-csv"></i> Eksport CSV
                </a>
            </h3>
        </div>
    </div>
</div>

<!-- Tabs -->
<div class="stock-tabs">
    <a href="{{ route('admin.stock.index') }}" class="tab-btn {{ !request('status') ? 'active' : '' }}">
        <i class="fas fa-list"></i> Wszystkie
    </a>
    <a href="{{ route('admin.stock.index') }}?status=low" class="tab-btn {{ request('status') == 'low' ? 'active' : '' }}">
        <i class="fas fa-exclamation-triangle"></i> Niski stan
    </a>
    <a href="{{ route('admin.stock.index') }}?status=out" class="tab-btn {{ request('status') == 'out' ? 'active' : '' }}">
        <i class="fas fa-times-circle"></i> Brak
    </a>
    <a href="{{ route('admin.stock.history') }}" class="tab-btn">
        <i class="fas fa-history"></i> Historia
    </a>
</div>

<!-- Stock Table -->
<div class="stock-table-card">
    @forelse($products as $product)
    <div class="stock-item">
        <div class="stock-product">
            <div class="stock-img">
                <i class="fas fa-compact-disc"></i>
            </div>
            <div class="stock-details">
                <h4>{{ $product->name }}</h4>
                <p>SKU: {{ $product->sku }} • {{ $product->category->name }}</p>
            </div>
        </div>

        <div>
            <div style="font-size: 0.75rem; color: var(--gray); margin-bottom: 0.25rem;">Stan obecny</div>
            <div class="stock-number" style="color: {{ $product->stock_quantity == 0 ? 'var(--danger)' : ($product->isLowStock() ? 'var(--warning)' : 'var(--success)') }}">
                {{ $product->stock_quantity }}
            </div>
        </div>

        <div>
            <div style="font-size: 0.75rem; color: var(--gray); margin-bottom: 0.25rem;">Próg ostrzeżenia</div>
            <div style="font-weight: 600;">{{ $product->low_stock_threshold }}</div>
        </div>

        <div class="stock-level">
            <div class="stock-bar-wrapper">
                @php
                    $percentage = $product->low_stock_threshold > 0 
                        ? min(100, ($product->stock_quantity / $product->low_stock_threshold) * 100) 
                        : 0;
                    $barClass = $percentage > 100 ? 'high' : ($percentage > 50 ? 'medium' : 'low');
                @endphp
                <div class="stock-bar {{ $barClass }}" style="width: '{{ $percentage }}''%'"></div>
            </div>
        </div>

        <div class="stock-actions">
            <button class="action-btn edit" onclick="openAdjustModal('{{ $product->id }}', '{{ $product->name }}', '{{ $product->stock_quantity }}')" title="Korekta stanu">
                <i class="fas fa-edit"></i>
            </button>
            <a href="{{ route('admin.products.stock', $product) }}" class="action-btn stock" title="Historia">
                <i class="fas fa-history"></i>
            </a>
        </div>
    </div>
    @empty
    <div class="empty-state">
        <i class="fas fa-box-open"></i>
        <h3>Brak produktów</h3>
        <p>Nie znaleziono produktów w tej kategorii.</p>
    </div>
    @endforelse
</div>

<!-- Adjust Stock Modal -->
<div id="adjustModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Korekta stanu magazynowego</h3>
            <button class="modal-close" onclick="closeAdjustModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="adjustForm" method="POST">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Produkt</label>
                    <input type="text" id="productName" class="form-input" readonly>
                </div>

                <div class="form-group">
                    <label class="form-label">Obecny stan: <strong id="currentStock"></strong></label>
                </div>

                <div class="form-group">
                    <label class="form-label">Typ operacji</label>
                    <select name="type" class="form-select" required>
                        <option value="in">Przyjęcie (+)</option>
                        <option value="out">Wydanie (-)</option>
                        <option value="adjustment">Korekta (ustaw wartość)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Ilość</label>
                    <input type="number" name="quantity" class="form-input" required min="1">
                </div>

                <div class="form-group">
                    <label class="form-label">Powód</label>
                    <textarea name="reason" class="form-textarea" required placeholder="Np. Dostawa od dostawcy, Reklamacja, Inwentaryzacja..."></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Numer referencyjny (opcjonalnie)</label>
                    <input type="text" name="reference" class="form-input" placeholder="Np. numer faktury, dokumentu...">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeAdjustModal()">
                    Anuluj
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Zapisz korektę
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    let adjustModal = document.getElementById('adjustModal');
    let adjustForm = document.getElementById('adjustForm');

    function openAdjustModal(productId, productName, currentStock) {
        document.getElementById('productName').value = productName;
        document.getElementById('currentStock').textContent = currentStock;
        adjustForm.action = `/admin/products/${productId}/adjust-stock`;
        adjustModal.classList.add('active');
    }

    function closeAdjustModal() {
        adjustModal.classList.remove('active');
        adjustForm.reset();
    }

    // Close modal on background click
    adjustModal.addEventListener('click', function(e) {
        if (e.target === adjustModal) {
            closeAdjustModal();
        }
    });
</script>
@endpush
@endsection

<!-- 
Zapisz jako: resources/views/admin/stock/index.blade.php
-->