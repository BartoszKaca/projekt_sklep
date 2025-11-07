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
    <div class="category-card" data-category-id="{{ $category->id }}">
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
            <button onclick="editCategory({{ $category->id }}, {{ json_encode($category->name) }}, {{ json_encode($category->description ?? '') }}, {{ $category->is_active ? 'true' : 'false' }})"  
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
