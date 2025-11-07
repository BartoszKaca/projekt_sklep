@extends('layouts.admin')

@section('title', isset($product) ? 'Edytuj produkt' : 'Dodaj produkt')

@push('styles')
<style>
    .form-container {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 1.5rem;
    }

    .form-card {
        background: white;
        padding: 2rem;
        border-radius: 16px;
        border: 1px solid var(--border);
        height: fit-content;
    }

    .form-section {
        margin-bottom: 2rem;
    }

    .form-section:last-child {
        margin-bottom: 0;
    }

    .section-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 1rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid var(--light-gray);
    }

    .form-row {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group.full-width {
        grid-column: 1 / -1;
    }

    .form-label {
        display: block;
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: var(--dark);
        font-size: 0.875rem;
    }

    .form-label.required::after {
        content: '*';
        color: var(--danger);
        margin-left: 0.25rem;
    }

    .form-help {
        font-size: 0.75rem;
        color: var(--gray);
        margin-top: 0.25rem;
    }

    .form-input,
    .form-select,
    .form-textarea {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid var(--border);
        border-radius: 10px;
        font-size: 0.875rem;
        transition: all 0.2s;
        font-family: inherit;
    }

    .form-textarea {
        min-height: 120px;
        resize: vertical;
    }

    .form-input:focus,
    .form-select:focus,
    .form-textarea:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }

    .form-input.error,
    .form-select.error,
    .form-textarea.error {
        border-color: var(--danger);
    }

    .error-message {
        color: var(--danger);
        font-size: 0.75rem;
        margin-top: 0.25rem;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    .checkbox-group {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem;
        background: var(--light-gray);
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.2s;
    }

    .checkbox-group:hover {
        background: var(--border);
    }

    .checkbox-group input[type="checkbox"] {
        width: 20px;
        height: 20px;
        cursor: pointer;
    }

    .checkbox-group label {
        cursor: pointer;
        margin: 0;
        font-weight: 500;
    }

    .input-group {
        position: relative;
    }

    .input-addon {
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--gray);
        font-size: 0.875rem;
        font-weight: 600;
    }

    .input-group .form-input {
        padding-right: 3rem;
    }

    .image-upload {
        border: 2px dashed var(--border);
        border-radius: 12px;
        padding: 2rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
    }

    .image-upload:hover {
        border-color: var(--primary);
        background: rgba(99, 102, 241, 0.05);
    }

    .image-upload input[type="file"] {
        display: none;
    }

    .image-upload-icon {
        font-size: 3rem;
        color: var(--gray);
        margin-bottom: 1rem;
    }

    .image-preview {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        gap: 1rem;
        margin-top: 1rem;
    }

    .image-preview-item {
        position: relative;
        aspect-ratio: 1;
        border-radius: 10px;
        overflow: hidden;
        background: var(--light-gray);
    }

    .image-preview-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .image-preview-item .remove-btn {
        position: absolute;
        top: 0.5rem;
        right: 0.5rem;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: var(--danger);
        color: white;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: all 0.2s;
    }

    .image-preview-item:hover .remove-btn {
        opacity: 1;
    }

    .variants-list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .variant-item {
        display: grid;
        grid-template-columns: 1fr 1fr auto;
        gap: 0.75rem;
        padding: 1rem;
        background: var(--light-gray);
        border-radius: 10px;
        align-items: end;
    }

    .btn-add-variant {
        width: 100%;
        padding: 0.75rem;
        border: 2px dashed var(--border);
        background: transparent;
        border-radius: 10px;
        color: var(--primary);
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-add-variant:hover {
        border-color: var(--primary);
        background: rgba(99, 102, 241, 0.05);
    }

    .form-actions {
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
        padding-top: 1.5rem;
        border-top: 1px solid var(--border);
        margin-top: 2rem;
    }

    .type-toggle {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.5rem;
        padding: 0.25rem;
        background: var(--light-gray);
        border-radius: 10px;
    }

    .type-toggle input[type="radio"] {
        display: none;
    }

    .type-toggle label {
        padding: 0.75rem;
        text-align: center;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s;
        font-weight: 600;
        font-size: 0.875rem;
    }

    .type-toggle input[type="radio"]:checked + label {
        background: var(--primary);
        color: white;
    }

    .conditional-fields {
        display: none;
    }

    .conditional-fields.active {
        display: block;
    }

    @media (max-width: 1024px) {
        .form-container {
            grid-template-columns: 1fr;
        }

        .form-row {
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
        <a href="{{ route('admin.products.index') }}">Produkty</a>
        <span>/</span>
        <span>{{ isset($product) ? 'Edytuj' : 'Dodaj' }}</span>
    </div>
    <h1 class="page-title">{{ isset($product) ? 'Edytuj produkt' : 'Dodaj nowy produkt' }}</h1>
    <p class="page-subtitle">{{ isset($product) ? 'Zaktualizuj informacje o produkcie' : 'Wypełnij formularz aby dodać produkt do sklepu' }}</p>
</div>

<form method="POST" action="{{ isset($product) ? route('admin.products.update', $product) : route('admin.products.store') }}" enctype="multipart/form-data">
    @csrf
    @if(isset($product))
        @method('PUT')
    @endif

    <div class="form-container">
        <!-- Main Form -->
        <div>
            <!-- Basic Info -->
            <div class="form-card">
                <div class="form-section">
                    <h3 class="section-title">Podstawowe informacje</h3>

                    <div class="form-group">
                        <label class="form-label required">Nazwa produktu</label>
                        <input type="text" name="name" class="form-input @error('name') error @enderror" 
                               value="{{ old('name', $product->name ?? '') }}" 
                               placeholder="Np. Taco Hemingway - Café Belga" required>
                        @error('name')
                            <div class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label required">Opis</label>
                        <textarea name="description" class="form-textarea @error('description') error @enderror" 
                                  placeholder="Pełny opis produktu..." required>{{ old('description', $product->description ?? '') }}</textarea>
                        @error('description')
                            <div class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                        <div class="form-help">Szczegółowy opis pomoże klientom w podjęciu decyzji zakupowej</div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label required">Kategoria</label>
                            <select name="category_id" class="form-select @error('category_id') error @enderror" required>
                                <option value="">Wybierz kategorię</option>
                                @foreach($categories ?? [] as $category)
                                    <option value="{{ $category->id }}" 
                                        {{ old('category_id', $product->category_id ?? '') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label required">Typ produktu</label>
                            <div class="type-toggle">
                                <input type="radio" name="type" id="type-album" value="album" 
                                       {{ old('type', $product->type ?? 'album') == 'album' ? 'checked' : '' }}>
                                <label for="type-album"><i class="fas fa-compact-disc"></i> Płyta</label>
                                
                                <input type="radio" name="type" id="type-merch" value="merch" 
                                       {{ old('type', $product->type ?? '') == 'merch' ? 'checked' : '' }}>
                                <label for="type-merch"><i class="fas fa-tshirt"></i> Merch</label>
                            </div>
                        </div>
                    </div>

                    <!-- Album-specific fields -->
                    <div id="album-fields" class="conditional-fields {{ old('type', $product->type ?? 'album') == 'album' ? 'active' : '' }}">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Artysta</label>
                                <input type="text" name="artist" class="form-input" 
                                       value="{{ old('artist', $product->artist ?? '') }}" 
                                       placeholder="Nazwa artysty">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Rok wydania</label>
                                <input type="number" name="release_year" class="form-input" 
                                       value="{{ old('release_year', $product->release_year ?? '') }}" 
                                       min="1900" max="{{ date('Y') }}" placeholder="{{ date('Y') }}">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Format</label>
                                <select name="format" class="form-select">
                                    <option value="">Wybierz format</option>
                                    <option value="CD" {{ old('format', $product->format ?? '') == 'CD' ? 'selected' : '' }}>CD</option>
                                    <option value="Vinyl" {{ old('format', $product->format ?? '') == 'Vinyl' ? 'selected' : '' }}>Vinyl</option>
                                    <option value="Cassette" {{ old('format', $product->format ?? '') == 'Cassette' ? 'selected' : '' }}>Kaseta</option>
                                    <option value="Digital" {{ old('format', $product->format ?? '') == 'Digital' ? 'selected' : '' }}>Digital</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Wytwórnia</label>
                                <input type="text" name="label" class="form-input" 
                                       value="{{ old('label', $product->label ?? '') }}" 
                                       placeholder="Nazwa wytwórni">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pricing -->
                <div class="form-section">
                    <h3 class="section-title">Ceny</h3>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label required">Cena regularna</label>
                            <div class="input-group">
                                <input type="number" name="price" class="form-input @error('price') error @enderror" 
                                       value="{{ old('price', $product->price ?? '') }}" 
                                       step="0.01" min="0" placeholder="0.00" required>
                                <span class="input-addon">PLN</span>
                            </div>
                            @error('price')
                                <div class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">Cena promocyjna</label>
                            <div class="input-group">
                                <input type="number" name="discount_price" class="form-input" 
                                       value="{{ old('discount_price', $product->discount_price ?? '') }}" 
                                       step="0.01" min="0" placeholder="0.00">
                                <span class="input-addon">PLN</span>
                            </div>
                            <div class="form-help">Zostaw puste jeśli nie ma promocji</div>
                        </div>
                    </div>
                </div>

                <!-- Inventory -->
                <div class="form-section">
                    <h3 class="section-title">Magazyn</h3>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label required">SKU</label>
                            <input type="text" name="sku" class="form-input @error('sku') error @enderror" 
                                   value="{{ old('sku', $product->sku ?? 'SKU-' . strtoupper(substr(uniqid(), -6))) }}" 
                                   placeholder="SKU-ABC123" required>
                            @error('sku')
                                <div class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">Kod kreskowy</label>
                            <input type="text" name="barcode" class="form-input" 
                                   value="{{ old('barcode', $product->barcode ?? '') }}" 
                                   placeholder="5901234567890">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label required">Stan magazynowy</label>
                            <input type="number" name="stock_quantity" class="form-input @error('stock_quantity') error @enderror" 
                                   value="{{ old('stock_quantity', $product->stock_quantity ?? 0) }}" 
                                   min="0" required>
                            @error('stock_quantity')
                                <div class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label required">Próg niskiego stanu</label>
                            <input type="number" name="low_stock_threshold" class="form-input" 
                                   value="{{ old('low_stock_threshold', $product->low_stock_threshold ?? 5) }}" 
                                   min="1" required>
                            <div class="form-help">Powiadomienie gdy stan spadnie poniżej tej wartości</div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Waga (kg)</label>
                        <input type="number" name="weight" class="form-input" 
                               value="{{ old('weight', $product->weight ?? '') }}" 
                               step="0.01" min="0" placeholder="0.00">
                        <div class="form-help">Potrzebne do obliczenia kosztów wysyłki</div>
                    </div>
                </div>

                <!-- Images -->
                <div class="form-section">
                    <h3 class="section-title">Zdjęcia produktu</h3>
                    
                    <label class="image-upload" for="images">
                        <div class="image-upload-icon">
                            <i class="fas fa-cloud-upload-alt"></i>
                        </div>
                        <div style="font-weight: 600; margin-bottom: 0.5rem;">Kliknij aby dodać zdjęcia</div>
                        <div style="font-size: 0.875rem; color: var(--gray);">lub przeciągnij i upuść pliki tutaj</div>
                        <input type="file" id="images" name="images[]" multiple accept="image/*">
                    </label>

                    <div id="imagePreview" class="image-preview"></div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div>
            <!-- Status -->
            <div class="form-card">
                <h3 class="section-title">Status publikacji</h3>
                
                <div class="form-group">
                    <div class="checkbox-group">
                        <input type="checkbox" name="is_active" id="is_active" value="1" 
                               {{ old('is_active', $product->is_active ?? true) ? 'checked' : '' }}>
                        <label for="is_active">Aktywny (widoczny w sklepie)</label>
                    </div>
                </div>

                <div class="form-group">
                    <div class="checkbox-group">
                        <input type="checkbox" name="is_featured" id="is_featured" value="1" 
                               {{ old('is_featured', $product->is_featured ?? false) ? 'checked' : '' }}>
                        <label for="is_featured">Wyróżniony (na stronie głównej)</label>
                    </div>
                </div>
            </div>

            <!-- Quick Stats (Edit mode) -->
            @if(isset($product))
            <div class="form-card">
                <h3 class="section-title">Statystyki</h3>
                
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <div style="display: flex; justify-content: space-between; padding: 0.75rem; background: var(--light-gray); border-radius: 8px;">
                        <span style="color: var(--gray);">Wyświetlenia:</span>
                        <strong>{{ $product->views_count }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 0.75rem; background: var(--light-gray); border-radius: 8px;">
                        <span style="color: var(--gray);">Utworzono:</span>
                        <strong>{{ $product->created_at->format('d.m.Y') }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 0.75rem; background: var(--light-gray); border-radius: 8px;">
                        <span style="color: var(--gray);">Ostatnia edycja:</span>
                        <strong>{{ $product->updated_at->format('d.m.Y') }}</strong>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Form Actions -->
    <div class="form-card" style="margin-top: 1.5rem;">
        <div class="form-actions">
            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Anuluj
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> {{ isset($product) ? 'Zaktualizuj produkt' : 'Dodaj produkt' }}
            </button>
        </div>
    </div>
</form>

@push('scripts')
<script>
    // Type toggle
    document.querySelectorAll('input[name="type"]').forEach(radio => {
        radio.addEventListener('change', function() {
            document.getElementById('album-fields').classList.toggle('active', this.value === 'album');
        });
    });

    // Image preview
    document.getElementById('images').addEventListener('change', function(e) {
        const preview = document.getElementById('imagePreview');
        preview.innerHTML = '';
        
        Array.from(e.target.files).forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'image-preview-item';
                div.innerHTML = `
                    <img src="${e.target.result}" alt="Preview">
                    <button type="button" class="remove-btn" onclick="removeImage(${index})">
                        <i class="fas fa-times"></i>
                    </button>
                `;
                preview.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
    });

    function removeImage(index) {
        const input = document.getElementById('images');
        const dt = new DataTransfer();
        const files = Array.from(input.files);
        
        files.forEach((file, i) => {
            if (i !== index) dt.items.add(file);
        });
        
        input.files = dt.files;
        input.dispatchEvent(new Event('change'));
    }
</script>
@endpush
@endsection

<!-- 
Zapisz jako: 
- resources/views/admin/products/create.blade.php
- resources/views/admin/products/edit.blade.php
(ten sam plik obsługuje obie operacje)
-->