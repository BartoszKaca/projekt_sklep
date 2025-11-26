@extends('layouts.shop')

@section('title', 'Zamówienie')

@push('styles')
<style>
    .checkout-grid {
        display: grid;
        grid-template-columns: 1fr 400px;
        gap: 2rem;
        align-items: start;
    }
    
    .checkout-section {
        background: white;
        padding: 1.5rem;
        border-radius: 12px;
        border: 1px solid var(--border);
        margin-bottom: 1.5rem;
    }
    
    .checkout-section h3 {
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid var(--border);
    }
    
    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }
    
    .form-group {
        margin-bottom: 1rem;
    }
    
    .form-group label {
        display: block;
        font-weight: 600;
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
        color: var(--dark);
    }
    
    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 2px solid var(--border);
        border-radius: 8px;
        font-size: 0.9375rem;
        transition: all 0.2s;
    }
    
    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }
    
    .form-group.full-width {
        grid-column: 1 / -1;
    }
    
    .shipping-options, .payment-options {
        display: grid;
        gap: 0.75rem;
    }
    
    .option-card {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        border: 2px solid var(--border);
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .option-card:hover {
        border-color: var(--primary);
    }
    
    .option-card.selected {
        border-color: var(--primary);
        background: rgba(99, 102, 241, 0.05);
    }
    
    .option-card input[type="radio"] {
        width: auto;
    }
    
    .option-info {
        flex: 1;
    }
    
    .option-name {
        font-weight: 600;
        color: var(--dark);
    }
    
    .option-desc {
        font-size: 0.875rem;
        color: var(--gray);
    }
    
    .option-price {
        font-weight: 700;
        color: var(--primary);
    }
    
    .order-summary {
        background: white;
        padding: 1.5rem;
        border-radius: 12px;
        border: 1px solid var(--border);
        position: sticky;
        top: 100px;
    }
    
    .order-summary h3 {
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid var(--border);
    }
    
    .summary-item {
        display: flex;
        gap: 1rem;
        padding: 0.75rem 0;
        border-bottom: 1px solid var(--light);
    }
    
    .summary-item-image {
        width: 60px;
        height: 60px;
        border-radius: 8px;
        background: var(--light);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .summary-item-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 8px;
    }
    
    .summary-item-info {
        flex: 1;
    }
    
    .summary-item-name {
        font-weight: 600;
        font-size: 0.875rem;
    }
    
    .summary-item-qty {
        font-size: 0.75rem;
        color: var(--gray);
    }
    
    .summary-item-price {
        font-weight: 600;
    }
    
    .summary-totals {
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid var(--border);
    }
    
    .summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.5rem;
        font-size: 0.9375rem;
    }
    
    .summary-row.total {
        font-size: 1.25rem;
        font-weight: 700;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 2px solid var(--border);
    }
    
    .btn-checkout {
        width: 100%;
        padding: 1rem;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 1rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s;
        margin-top: 1rem;
    }
    
    .btn-checkout:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(99, 102, 241, 0.3);
    }
    
    .alert {
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1rem;
    }
    
    .alert-danger {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }
    
    @media (max-width: 1024px) {
        .checkout-grid {
            grid-template-columns: 1fr;
        }
        
        .order-summary {
            position: static;
        }
    }
</style>
@endpush

@section('content')
<div class="container section">
    <div class="section-header">
        <h2 class="section-title">Finalizacja zamówienia</h2>
        <p class="section-subtitle">Wypełnij dane i wybierz metodę dostawy oraz płatności</p>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul style="margin: 0; padding-left: 1rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('checkout.store') }}">
        @csrf
        <div class="checkout-grid">
            <div>
                <!-- Dane kontaktowe -->
                <div class="checkout-section">
                    <h3><i class="fas fa-user"></i> Dane kontaktowe</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="first_name">Imię *</label>
                            <input type="text" name="first_name" id="first_name" 
                                   value="{{ old('first_name', auth()->user()->name ?? '') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="last_name">Nazwisko *</label>
                            <input type="text" name="last_name" id="last_name" 
                                   value="{{ old('last_name') }}" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" name="email" id="email" 
                                   value="{{ old('email', auth()->user()->email ?? '') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Telefon *</label>
                            <input type="tel" name="phone" id="phone" 
                                   value="{{ old('phone', auth()->user()->phone ?? '') }}" required>
                        </div>
                    </div>
                </div>

                <!-- Adres dostawy -->
                <div class="checkout-section">
                    <h3><i class="fas fa-map-marker-alt"></i> Adres dostawy</h3>
                    <div class="form-group">
                        <label for="street_address">Ulica i numer *</label>
                        <input type="text" name="street_address" id="street_address" 
                               value="{{ old('street_address') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="apartment">Mieszkanie / lokal</label>
                        <input type="text" name="apartment" id="apartment" 
                               value="{{ old('apartment') }}">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="postal_code">Kod pocztowy *</label>
                            <input type="text" name="postal_code" id="postal_code" 
                                   value="{{ old('postal_code') }}" placeholder="00-000" required>
                        </div>
                        <div class="form-group">
                            <label for="city">Miasto *</label>
                            <input type="text" name="city" id="city" 
                                   value="{{ old('city') }}" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="country">Kraj *</label>
                        <select name="country" id="country" required>
                            <option value="Polska" {{ old('country', 'Polska') == 'Polska' ? 'selected' : '' }}>Polska</option>
                        </select>
                    </div>
                </div>

                <!-- Metoda dostawy -->
                <div class="checkout-section">
                    <h3><i class="fas fa-truck"></i> Metoda dostawy</h3>
                    <div class="shipping-options">
                        <label class="option-card {{ old('shipping_method') == 'inpost' ? 'selected' : '' }}">
                            <input type="radio" name="shipping_method" value="inpost" 
                                   {{ old('shipping_method', 'inpost') == 'inpost' ? 'checked' : '' }} required>
                            <div class="option-info">
                                <div class="option-name">Paczkomat InPost</div>
                                <div class="option-desc">Dostawa do 2 dni roboczych</div>
                            </div>
                            <div class="option-price">9,99 zł</div>
                        </label>
                        <label class="option-card {{ old('shipping_method') == 'courier' ? 'selected' : '' }}">
                            <input type="radio" name="shipping_method" value="courier" 
                                   {{ old('shipping_method') == 'courier' ? 'checked' : '' }}>
                            <div class="option-info">
                                <div class="option-name">Kurier DPD</div>
                                <div class="option-desc">Dostawa do 1-2 dni roboczych</div>
                            </div>
                            <div class="option-price">14,99 zł</div>
                        </label>
                        <label class="option-card {{ old('shipping_method') == 'personal' ? 'selected' : '' }}">
                            <input type="radio" name="shipping_method" value="personal" 
                                   {{ old('shipping_method') == 'personal' ? 'checked' : '' }}>
                            <div class="option-info">
                                <div class="option-name">Odbiór osobisty</div>
                                <div class="option-desc">Warszawa, ul. Rapowa 123</div>
                            </div>
                            <div class="option-price">0,00 zł</div>
                        </label>
                    </div>
                </div>

                <!-- Metoda płatności -->
                <div class="checkout-section">
                    <h3><i class="fas fa-credit-card"></i> Metoda płatności</h3>
                    <div class="payment-options">
                        <label class="option-card {{ old('payment_method') == 'transfer' ? 'selected' : '' }}">
                            <input type="radio" name="payment_method" value="transfer" 
                                   {{ old('payment_method', 'transfer') == 'transfer' ? 'checked' : '' }} required>
                            <div class="option-info">
                                <div class="option-name">Przelew online</div>
                                <div class="option-desc">Szybki przelew przez PayU</div>
                            </div>
                            <i class="fas fa-university" style="font-size: 1.5rem; color: var(--gray);"></i>
                        </label>
                        <label class="option-card {{ old('payment_method') == 'card' ? 'selected' : '' }}">
                            <input type="radio" name="payment_method" value="card" 
                                   {{ old('payment_method') == 'card' ? 'checked' : '' }}>
                            <div class="option-info">
                                <div class="option-name">Karta płatnicza</div>
                                <div class="option-desc">Visa, Mastercard, BLIK</div>
                            </div>
                            <i class="fab fa-cc-visa" style="font-size: 1.5rem; color: var(--gray);"></i>
                        </label>
                        <label class="option-card {{ old('payment_method') == 'cod' ? 'selected' : '' }}">
                            <input type="radio" name="payment_method" value="cod" 
                                   {{ old('payment_method') == 'cod' ? 'checked' : '' }}>
                            <div class="option-info">
                                <div class="option-name">Płatność przy odbiorze</div>
                                <div class="option-desc">Zapłać gotówką lub kartą</div>
                            </div>
                            <i class="fas fa-money-bill-wave" style="font-size: 1.5rem; color: var(--gray);"></i>
                        </label>
                    </div>
                </div>

                <!-- Uwagi -->
                <div class="checkout-section">
                    <h3><i class="fas fa-comment"></i> Uwagi do zamówienia</h3>
                    <div class="form-group">
                        <textarea name="customer_notes" id="customer_notes" rows="3" 
                                  placeholder="Dodatkowe informacje dla sprzedawcy...">{{ old('customer_notes') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Podsumowanie zamówienia -->
            <div>
                <div class="order-summary">
                    <h3><i class="fas fa-shopping-bag"></i> Twoje zamówienie</h3>
                    
                    @foreach($cart['items'] as $key => $item)
                    <div class="summary-item">
                        <div class="summary-item-image">
                            @if($item['image'])
                                <img src="{{ asset('storage/' . $item['image']) }}" alt="{{ $item['name'] }}">
                            @else
                                <i class="fas fa-compact-disc" style="color: var(--gray);"></i>
                            @endif
                        </div>
                        <div class="summary-item-info">
                            <div class="summary-item-name">{{ $item['name'] }}</div>
                            <div class="summary-item-qty">Ilość: {{ $item['quantity'] }}</div>
                        </div>
                        <div class="summary-item-price">{{ number_format($item['price'] * $item['quantity'], 2) }} zł</div>
                    </div>
                    @endforeach

                    <div class="summary-totals">
                        <div class="summary-row">
                            <span>Produkty</span>
                            <span>{{ number_format($cart['total'], 2) }} zł</span>
                        </div>
                        <div class="summary-row" id="shipping-cost">
                            <span>Dostawa</span>
                            <span>9,99 zł</span>
                        </div>
                        <div class="summary-row total" id="total-price">
                            <span>Razem</span>
                            <span>{{ number_format($cart['total'] + 9.99, 2) }} zł</span>
                        </div>
                    </div>

                    <button type="submit" class="btn-checkout">
                        <i class="fas fa-lock"></i> Złóż zamówienie
                    </button>

                    <p style="font-size: 0.75rem; color: var(--gray); text-align: center; margin-top: 1rem;">
                        Składając zamówienie, akceptujesz nasz regulamin i politykę prywatności.
                    </p>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    // Update shipping cost and total on shipping method change
    const shippingCosts = {
        'inpost': 9.99,
        'courier': 14.99,
        'personal': 0
    };
    
    const subtotal = {{ $cart['total'] }};
    
    document.querySelectorAll('input[name="shipping_method"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const cost = shippingCosts[this.value];
            const total = subtotal + cost;
            
            document.getElementById('shipping-cost').querySelector('span:last-child').textContent = 
                cost > 0 ? cost.toFixed(2).replace('.', ',') + ' zł' : 'Bezpłatnie';
            document.getElementById('total-price').querySelector('span:last-child').textContent = 
                total.toFixed(2).replace('.', ',') + ' zł';
            
            // Update selected class
            document.querySelectorAll('.shipping-options .option-card').forEach(card => {
                card.classList.remove('selected');
            });
            this.closest('.option-card').classList.add('selected');
        });
    });
    
    // Update selected class for payment options
    document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
        radio.addEventListener('change', function() {
            document.querySelectorAll('.payment-options .option-card').forEach(card => {
                card.classList.remove('selected');
            });
            this.closest('.option-card').classList.add('selected');
        });
    });
</script>
@endpush
