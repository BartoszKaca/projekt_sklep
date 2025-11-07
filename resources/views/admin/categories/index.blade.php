{{-- resources/views/admin/categories/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Kategorie')

@push('styles')
<style>
    .categories-grid {
        display: grid;
        gap: 1rem;
    }

    .category-card {
        background: white;
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 1.25rem;
        display: grid;
        grid-template-columns: auto 1fr auto auto;
        gap: 1rem;
        align-items: center;
        transition: all 0.2s;
    }

    .category-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }

    .category-icon {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.25rem;
    }

    .category-info h4 {
        font-weight: 600;
        margin-bottom: 0.25rem;
    }

    .category-info p {
        font-size: 0.875rem;
        color: var(--gray);
    }

    .category-count {
        text-align: center;
        padding: 0.5rem 1rem;
        background: var(--light-gray);
        border-radius: 8px;
    }

    .category-count strong {
        display: block;
        font-size: 1.5rem;
        font-weight: 700;
    }

    .category-count span {
        font-size: 0.75rem;
        color: var(--gray);
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <div class="breadcrumb">
        <a href="{{ route('admin.dashboard') }}">Admin</a>
        <span>/</span>
        <span>Kategorie</span>
    </div>
    <h1 class="page-title">Kategorie produktów</h1>
    <p class="page-subtitle">Organizuj swój asortyment</p>
</div>

<!-- Add Category Card -->
<div class="form-card" style="margin-bottom: 2rem;">
    <h3 class="section-title">Dodaj nową kategorię</h3>
    
    <form method="POST" action="{{ route('admin.categories.store') }}">
        @csrf
        <div class="form-row">
            <div class="form-group">
                <label class="form-label required">Nazwa kategorii</label>
                <input type="text" name="name" class="form-input @error('name') error @enderror" 
                       value="{{ old('name') }}" placeholder="Np. Płyty CD" required>
                @error('name')
                    <div class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Opis</label>
                <input type="text" name="description" class="form-input" 
                       value="{{ old('description') }}" placeholder="Krótki opis kategorii">
            </div>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div class="checkbox-group">
                <input type="checkbox" name="is_active" id="is_active" value="1" checked>
                <label for="is_active">Aktywna (widoczna w sklepie)</label>
            </div>
            
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-plus"></i> Dodaj kategorię
            </button>
        </div>
    </form>
</div>

<!-- Categories List -->
<div class="categories-grid">
    @forelse($categories ?? [] as $category)
    <div class="category-card">
        <div class="category-icon">
            <i class="fas fa-tag"></i>
        </div>

        <div class="category-info">
            <h4>{{ $category->name }}</h4>
            <p>{{ $category->description ?: 'Brak opisu' }}</p>
        </div>

        <div class="category-count">
            <strong>{{ $category->products_count ?? 0 }}</strong>
            <span>produktów</span>
        </div>

        <div class="action-btns">
            <button onclick="editCategory('{{ $category->id }}', '{{ e($category->name) }}', '{{ e($category->description) }}', {{ $category->is_active ? 'true' : 'false' }})" 
                    class="action-btn edit" title="Edytuj">
                <i class="fas fa-edit"></i>
            </button>
            
            @if($category->products_count == 0)
            <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" style="margin: 0;" 
                  onsubmit="return confirm('Czy na pewno chcesz usunąć tę kategorię?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="action-btn delete" title="Usuń">
                    <i class="fas fa-trash"></i>
                </button>
            </form>
            @endif

            <span class="badge {{ $category->is_active ? 'badge-success' : 'badge-danger' }}">
                {{ $category->is_active ? 'Aktywna' : 'Nieaktywna' }}
            </span>
        </div>
    </div>
    @empty
    <div class="empty-state" style="background: white; padding: 3rem; border-radius: 16px;">
        <i class="fas fa-tags"></i>
        <h3>Brak kategorii</h3>
        <p>Dodaj pierwszą kategorię używając formularza powyżej</p>
    </div>
    @endforelse
</div>

<!-- Edit Category Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Edytuj kategorię</h3>
            <button class="modal-close" onclick="closeEditModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label required">Nazwa kategorii</label>
                    <input type="text" name="name" id="edit_name" class="form-input" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Opis</label>
                    <input type="text" name="description" id="edit_description" class="form-input">
                </div>

                <div class="form-group">
                    <div class="checkbox-group">
                        <input type="checkbox" name="is_active" id="edit_is_active" value="1">
                        <label for="edit_is_active">Aktywna</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeEditModal()">Anuluj</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Zapisz zmiany
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function editCategory(id, name, description, isActive) {
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_description').value = description || '';
        document.getElementById('edit_is_active').checked = isActive;
        document.getElementById('editForm').action = `/admin/categories/${id}`;
        document.getElementById('editModal').classList.add('active');
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.remove('active');
    }

    document.getElementById('editModal').addEventListener('click', function(e) {
        if (e.target === this) closeEditModal();
    });
</script>
@endpush
@endsection


{{-- =========================================== --}}
{{-- resources/views/admin/coupons/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Kupony rabatowe')

@push('styles')
<style>
    .coupon-card {
        background: white;
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1rem;
        display: grid;
        grid-template-columns: auto 1fr auto auto;
        gap: 1.5rem;
        align-items: center;
        transition: all 0.2s;
    }

    .coupon-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }

    .coupon-code {
        font-family: 'Courier New', monospace;
        font-size: 1.5rem;
        font-weight: 700;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        padding: 0.5rem 1rem;
        background-color: var(--light-gray);
        border-radius: 8px;
        border: 2px dashed var(--border);
    }

    .coupon-details h4 {
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .coupon-meta {
        display: flex;
        gap: 1rem;
        font-size: 0.875rem;
        color: var(--gray);
    }

    .coupon-stats {
        text-align: center;
        padding: 0.75rem 1.25rem;
        background: var(--light-gray);
        border-radius: 10px;
    }

    .coupon-stats strong {
        display: block;
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
    }

    .coupon-stats span {
        font-size: 0.75rem;
        color: var(--gray);
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <div class="breadcrumb">
        <a href="{{ route('admin.dashboard') }}">Admin</a>
        <span>/</span>
        <span>Kupony</span>
    </div>
    <h1 class="page-title">Kupony rabatowe</h1>
    <p class="page-subtitle">Zarządzaj kodami promocyjnymi</p>
</div>

<!-- Add Coupon Form -->
<div class="form-card" style="margin-bottom: 2rem;">
    <h3 class="section-title">Utwórz nowy kupon</h3>
    
    <form method="POST" action="{{ route('admin.coupons.store') }}">
        @csrf
        
        <div class="form-row">
            <div class="form-group">
                <label class="form-label required">Kod kuponu</label>
                <input type="text" name="code" class="form-input @error('code') error @enderror" 
                       value="{{ old('code') }}" placeholder="WELCOME10" style="text-transform: uppercase;" required>
                @error('code')
                    <div class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                @enderror
                <div class="form-help">Kod musi być unikalny, bez spacji</div>
            </div>

            <div class="form-group">
                <label class="form-label required">Typ rabatu</label>
                <select name="type" class="form-select" required>
                    <option value="percentage">Procent (%)</option>
                    <option value="fixed">Kwota (PLN)</option>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label required">Wartość rabatu</label>
                <input type="number" name="value" class="form-input" 
                       value="{{ old('value') }}" step="0.01" min="0" placeholder="10" required>
                <div class="form-help">Np. 10 (dla 10% lub 10 zł)</div>
            </div>

            <div class="form-group">
                <label class="form-label">Minimalna wartość zamówienia</label>
                <div class="input-group">
                    <input type="number" name="min_order_value" class="form-input" 
                           value="{{ old('min_order_value') }}" step="0.01" min="0" placeholder="0">
                    <span class="input-addon">PLN</span>
                </div>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Limit użyć</label>
                <input type="number" name="usage_limit" class="form-input" 
                       value="{{ old('usage_limit') }}" min="1" placeholder="Bez limitu">
                <div class="form-help">Zostaw puste dla nielimitowanego</div>
            </div>

            <div class="form-group">
                <label class="form-label">Ważny od</label>
                <input type="datetime-local" name="valid_from" class="form-input" 
                       value="{{ old('valid_from') }}">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Ważny do</label>
                <input type="datetime-local" name="valid_until" class="form-input" 
                       value="{{ old('valid_until') }}">
            </div>

            <div class="form-group">
                <label class="form-label">&nbsp;</label>
                <div class="checkbox-group">
                    <input type="checkbox" name="is_active" id="new_is_active" value="1" checked>
                    <label for="new_is_active">Aktywny</label>
                </div>
            </div>
        </div>

        <div style="display: flex; justify-content: flex-end;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-plus"></i> Utwórz kupon
            </button>
        </div>
    </form>
</div>

<!-- Coupons List -->
@forelse($coupons ?? [] as $coupon)
<div class="coupon-card">
    <div class="coupon-code">{{ $coupon->code }}</div>

    <div class="coupon-details">
        <h4>
            {{ $coupon->type == 'percentage' ? $coupon->value . '%' : $coupon->value . ' zł' }} rabatu
            @if($coupon->min_order_value)
                • Min. {{ $coupon->min_order_value }} zł
            @endif
        </h4>
        <div class="coupon-meta">
            @if($coupon->valid_from)
                <span><i class="fas fa-calendar-alt"></i> Od: {{ $coupon->valid_from->format('d.m.Y') }}</span>
            @endif
            @if($coupon->valid_until)
                <span><i class="fas fa-calendar-times"></i> Do: {{ $coupon->valid_until->format('d.m.Y') }}</span>
            @endif
        </div>
    </div>

    <div class="coupon-stats">
        <strong>{{ $coupon->usage_count }}/{{ $coupon->usage_limit ?? '∞' }}</strong>
        <span>użyć</span>
    </div>

    <div class="action-btns">
        <button onclick="editCoupon('{{ $coupon->id }}', '{{ json_encode($coupon) }}')" 
                class="action-btn edit" title="Edytuj">
            <i class="fas fa-edit"></i>
        </button>
        
        <form method="POST" action="{{ route('admin.coupons.destroy', $coupon) }}" style="margin: 0;" 
              onsubmit="return confirm('Czy na pewno chcesz usunąć ten kupon?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="action-btn delete" title="Usuń">
                <i class="fas fa-trash"></i>
            </button>
        </form>

        <span class="badge {{ $coupon->is_active ? 'badge-success' : 'badge-danger' }}">
            {{ $coupon->is_active ? 'Aktywny' : 'Nieaktywny' }}
        </span>
    </div>
</div>
@empty
<div class="empty-state" style="background: white; padding: 3rem; border-radius: 16px;">
    <i class="fas fa-ticket-alt"></i>
    <h3>Brak kuponów</h3>
    <p>Utwórz pierwszy kupon rabatowy używając formularza powyżej</p>
</div>
@endforelse

<!-- Edit Coupon Modal -->
<div id="editCouponModal" class="modal">
    <div class="modal-content" style="max-width: 600px;">
        <div class="modal-header">
            <h3 class="modal-title">Edytuj kupon</h3>
            <button class="modal-close" onclick="closeEditCouponModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="editCouponForm" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Kod kuponu</label>
                    <input type="text" class="form-input" id="edit_coupon_code" readonly style="background: var(--light-gray);">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Typ</label>
                        <select name="type" id="edit_type" class="form-select">
                            <option value="percentage">Procent (%)</option>
                            <option value="fixed">Kwota (PLN)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Wartość</label>
                        <input type="number" name="value" id="edit_value" class="form-input" step="0.01" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Min. wartość zamówienia</label>
                    <input type="number" name="min_order_value" id="edit_min_order_value" class="form-input" step="0.01">
                </div>

                <div class="form-group">
                    <div class="checkbox-group">
                        <input type="checkbox" name="is_active" id="edit_coupon_is_active" value="1">
                        <label for="edit_coupon_is_active">Aktywny</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeEditCouponModal()">Anuluj</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Zapisz
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function editCoupon(id, coupon) {
        document.getElementById('edit_coupon_code').value = coupon.code;
        document.getElementById('edit_type').value = coupon.type;
        document.getElementById('edit_value').value = coupon.value;
        document.getElementById('edit_min_order_value').value = coupon.min_order_value || '';
        document.getElementById('edit_coupon_is_active').checked = coupon.is_active;
        document.getElementById('editCouponForm').action = `/admin/coupons/${id}`;
        document.getElementById('editCouponModal').classList.add('active');
    }

    function closeEditCouponModal() {
        document.getElementById('editCouponModal').classList.remove('active');
    }

    document.getElementById('editCouponModal').addEventListener('click', function(e) {
        if (e.target === this) closeEditCouponModal();
    });
</script>
@endpush
@endsection