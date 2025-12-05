{{-- resources/views/admin/coupons/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Kupony rabatowe')

@push('styles')
<style>
    .coupons-grid {
        display: grid;
        gap: 1rem;
    }

    .coupon-card {
        background: white;
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 1.5rem;
        display: grid;
        grid-template-columns: auto 1fr auto auto;
        gap: 1.5rem;
        align-items: center;
        transition: all 0.3s;
        position: relative;
        overflow: hidden;
    }

    .coupon-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: linear-gradient(180deg, var(--primary), var(--secondary));
    }

    .coupon-card:hover {
        box-shadow: 0 8px 24px rgba(99, 102, 241, 0.15);
        transform: translateY(-2px);
    }

    .coupon-card.inactive {
        opacity: 0.6;
    }

    .coupon-card.inactive::before {
        background: var(--gray);
    }

    .coupon-code-wrapper {
        position: relative;
    }

    .coupon-code {
        font-family: 'JetBrains Mono', 'Fira Code', 'Courier New', monospace;
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--primary);
        padding: 0.75rem 1.25rem;
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.1), rgba(236, 72, 153, 0.1));
        border-radius: 10px;
        border: 2px dashed var(--primary);
        letter-spacing: 0.1em;
        text-transform: uppercase;
    }

    .coupon-type-badge {
        position: absolute;
        top: -8px;
        right: -8px;
        background: var(--primary);
        color: white;
        font-size: 0.65rem;
        padding: 0.25rem 0.5rem;
        border-radius: 6px;
        font-weight: 600;
    }

    .coupon-details h4 {
        font-weight: 700;
        font-size: 1.125rem;
        margin-bottom: 0.5rem;
        color: var(--dark);
    }

    .coupon-value {
        display: inline-block;
        background: linear-gradient(135deg, var(--success), #059669);
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 6px;
        font-weight: 700;
        font-size: 1rem;
        margin-right: 0.5rem;
    }

    .coupon-min-order {
        color: var(--gray);
        font-size: 0.875rem;
    }

    .coupon-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        font-size: 0.8rem;
        color: var(--gray);
        margin-top: 0.75rem;
    }

    .coupon-meta-item {
        display: flex;
        align-items: center;
        gap: 0.35rem;
    }

    .coupon-meta-item i {
        color: var(--primary);
    }

    .coupon-stats {
        text-align: center;
        padding: 1rem 1.5rem;
        background: linear-gradient(135deg, var(--light-gray), rgba(99, 102, 241, 0.05));
        border-radius: 12px;
        min-width: 100px;
    }

    .coupon-stats strong {
        display: block;
        font-size: 1.5rem;
        font-weight: 800;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 0.25rem;
    }

    .coupon-stats span {
        font-size: 0.7rem;
        color: var(--gray);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .coupon-actions {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        align-items: flex-end;
    }

    @media (max-width: 1024px) {
        .coupon-card {
            grid-template-columns: 1fr;
            gap: 1rem;
        }

        .coupon-code-wrapper {
            justify-self: start;
        }

        .coupon-actions {
            flex-direction: row;
            justify-content: space-between;
            width: 100%;
        }
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
<div class="coupons-grid">
@forelse($coupons ?? [] as $coupon)
<div class="coupon-card {{ !$coupon->is_active ? 'inactive' : '' }}">
    <div class="coupon-code-wrapper">
        <div class="coupon-code">{{ $coupon->code }}</div>
        <span class="coupon-type-badge">
            {{ $coupon->type == 'percentage' ? '%' : 'PLN' }}
        </span>
    </div>

    <div class="coupon-details">
        <h4>
            <span class="coupon-value">
                @if($coupon->type == 'percentage')
                    -{{ number_format($coupon->value, 0) }}%
                @else
                    -{{ number_format($coupon->value, 2) }} zł
                @endif
            </span>
            @if($coupon->min_order_value)
                <span class="coupon-min-order">
                    min. zamówienie {{ number_format($coupon->min_order_value, 2) }} zł
                </span>
            @endif
        </h4>
        <div class="coupon-meta">
            @if($coupon->valid_from)
                <span class="coupon-meta-item">
                    <i class="fas fa-play-circle"></i> 
                    Od: {{ $coupon->valid_from->format('d.m.Y H:i') }}
                </span>
            @endif
            @if($coupon->valid_until)
                <span class="coupon-meta-item">
                    <i class="fas fa-stop-circle"></i> 
                    Do: {{ $coupon->valid_until->format('d.m.Y H:i') }}
                </span>
            @else
                <span class="coupon-meta-item">
                    <i class="fas fa-infinity"></i> 
                    Bez limitu czasowego
                </span>
            @endif
            @if($coupon->usage_limit)
                <span class="coupon-meta-item">
                    <i class="fas fa-users"></i> 
                    Limit: {{ $coupon->usage_limit }} użyć
                </span>
            @endif
        </div>
    </div>

    <div class="coupon-stats">
        <strong>{{ $coupon->usage_count ?? 0 }}/{{ $coupon->usage_limit ?? '∞' }}</strong>
        <span>wykorzystań</span>
    </div>

    <div class="coupon-actions">
        <span class="badge {{ $coupon->is_active ? 'badge-success' : 'badge-danger' }}">
            <i class="fas fa-{{ $coupon->is_active ? 'check' : 'times' }}"></i>
            {{ $coupon->is_active ? 'Aktywny' : 'Nieaktywny' }}
        </span>
        
        <div class="action-btns">
            <button onclick="editCoupon({{ $coupon->id }}, {{ json_encode($coupon) }})"  
                    class="action-btn edit" title="Edytuj">
                <i class="fas fa-edit"></i>
            </button>
            
            <form method="POST" action="{{ route('admin.coupons.destroy', $coupon) }}" style="margin: 0;"  
                  onsubmit="return confirm('Czy na pewno chcesz usunąć kupon {{ $coupon->code }}?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="action-btn delete" title="Usuń">
                    <i class="fas fa-trash"></i>
                </button>
            </form>
        </div>
    </div>
</div>
@empty
<div class="empty-state" style="background: white; padding: 4rem 2rem; border-radius: 16px; text-align: center;">
    <i class="fas fa-ticket-alt" style="font-size: 5rem; opacity: 0.2; color: var(--primary);"></i>
    <h3 style="margin-top: 1.5rem; color: var(--dark);">Brak kuponów rabatowych</h3>
    <p style="color: var(--gray); margin-top: 0.5rem;">Utwórz pierwszy kupon rabatowy używając formularza powyżej, aby zachęcić klientów do zakupów!</p>
</div>
@endforelse
</div>

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
                    <label class="form-label">Limit użyć</label>
                    <input type="number" name="usage_limit" id="edit_usage_limit" class="form-input" min="1" placeholder="Bez limitu">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Ważny od</label>
                        <input type="datetime-local" name="valid_from" id="edit_valid_from" class="form-input">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Ważny do</label>
                        <input type="datetime-local" name="valid_until" id="edit_valid_until" class="form-input">
                    </div>
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
        document.getElementById('edit_usage_limit').value = coupon.usage_limit || '';
        
        // Format dates for datetime-local input
        if (coupon.valid_from) {
            const validFrom = new Date(coupon.valid_from);
            document.getElementById('edit_valid_from').value = validFrom.toISOString().slice(0, 16);
        } else {
            document.getElementById('edit_valid_from').value = '';
        }
        
        if (coupon.valid_until) {
            const validUntil = new Date(coupon.valid_until);
            document.getElementById('edit_valid_until').value = validUntil.toISOString().slice(0, 16);
        } else {
            document.getElementById('edit_valid_until').value = '';
        }
        
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