@extends('layouts.shop')

@section('title', 'Finalizacja zamówienia')

@push('styles')
<style>
    .checkout-container {
        display: grid;
        grid-template-columns: 1fr 400px;
        gap: 2rem;
        margin-top: 2rem;
    }
    
    .checkout-form {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        border: 1px solid var(--border);
    }
    
    .form-section {
        margin-bottom: 2rem;
    }
    
    .form-section h2 {
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 1.5rem;
        color: var(--dark);
        display: flex;
        align-items: center;
        gap: 0.5rem;
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
        margin-bottom: 0.5rem;
        font-weight: 600;
        color: var(--dark);
    }
    
    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 0.875rem 1rem;
        border: 2px solid var(--border);
        border-radius: 8px;
        font-size: 1rem;
        transition: all 0.2s;
    }
    
    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }
    
    .form-group.error input,
    .form-group.error select {
        border-color: var(--danger);
    }
    
    .error-message {
        color: var(--danger);
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }
    
    .shipping-options,
    .payment-options {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }
    
    .option-card {
        display: flex;
        align-items: center;
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
        margin-right: 1rem;
    }
    
    .option-info {
        flex: 1;
    }
    
    .option-name {
        font-weight: 600;
    }
    
    .option-price {
        color: var(--primary);
        font-weight: 700;
    }
    
    .order-summary {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        border: 1px solid var(--border);
        position: sticky;
        top: 100px;
        height: fit-content;
    }
    
    .order-summary h2 {
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 1.5rem;
    }
    
    .cart-items {
        max-height: 300px;
        overflow-y: auto;
        margin-bottom: 1.5rem;
    }
    
    .cart-item {
        display: flex;
        gap: 1rem;
        padding: 1rem 0;
        border-bottom: 1px solid var(--border);
    }
    
    .cart-item:last-child {
        border-bottom: none;
    }
    
    .cart-item-image {
        width: 60px;
        height: 60px;
        border-radius: 8px;
        background: var(--light);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }
    
    .cart-item-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .cart-item-image i {
        color: var(--gray);
    }
    
    .cart-item-details {
        flex: 1;
    }
    
    .cart-item-name {
        font-weight: 600;
        margin-bottom: 0.25rem;
    }
    
    .cart-item-qty {
        font-size: 0.875rem;
        color: var(--gray);
    }
    
    .cart-item-price {
        font-weight: 600;
        color: var(--primary);
    }
    
    .coupon-form {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 1.5rem;
    }
    
    .coupon-form input {
        flex: 1;
        padding: 0.75rem 1rem;
        border: 2px solid var(--border);
        border-radius: 8px;
    }
    
    .coupon-form button {
        padding: 0.75rem 1.5rem;
        background: var(--dark);
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
    }
    
    .applied-coupon {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: rgba(16, 185, 129, 0.1);
        padding: 0.75rem 1rem;
        border-radius: 8px;
        margin-bottom: 1rem;
    }
    
    .applied-coupon span {
        color: var(--success);
        font-weight: 600;
    }
    
    .applied-coupon button {
        background: none;
        border: none;
        color: var(--gray);
        cursor: pointer;
    }
    
    .summary-row {
        display: flex;
        justify-content: space-between;
        padding: 0.75rem 0;
        border-bottom: 1px solid var(--border);
    }
    
    .summary-row.total {
        font-size: 1.25rem;
        font-weight: 700;
        border-bottom: none;
        padding-top: 1rem;
    }
    
    .summary-row.discount {
        color: var(--success);
    }
    
    .terms-checkbox {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        margin: 1.5rem 0;
    }
    
    .terms-checkbox input {
        margin-top: 0.25rem;
    }
    
    .terms-checkbox label {
        font-size: 0.875rem;
        color: var(--gray);
    }
    
    .terms-checkbox a {
        color: var(--primary);
    }
    
    .checkout-btn {
        width: 100%;
        padding: 1rem;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: white;
        border: none;
        border-radius: 50px;
        font-size: 1rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .checkout-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 24px rgba(99, 102, 241, 0.3);
    }
    
    .checkout-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    
    .alert {
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
    }
    
    .alert-success {
        background: rgba(16, 185, 129, 0.1);
        color: var(--success);
        border: 1px solid var(--success);
    }
    
    .alert-error {
        background: rgba(239, 68, 68, 0.1);
        color: var(--danger);
        border: 1px solid var(--danger);
    }
    
    @media (max-width: 1024px) {
        .checkout-container {
            grid-template-columns: 1fr;
        }
        
        .order-summary {
            position: static;
        }
    }
    
    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="container">
    <h1 style="font-size: 2rem; font-weight: 800; margin-bottom: 0.5rem;">Finalizacja zamówienia</h1>
    <p style="color: var(--gray); margin-bottom: 2rem;">Wypełnij dane dostawy i wybierz metodę płatności</p>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    <form action="{{ route('checkout.process') }}" method="POST" id="checkout-form">
        @csrf

        <div class="checkout-container">
            <div class="checkout-form">
                <!-- Contact Information -->
                <div class="form-section">
                    <h2><i class="fas fa-user"></i> Dane kontaktowe</h2>

                    <div class="form-row">
                        <div class="form-group @error('first_name') error @enderror">
                            <label for="first_name">Imię *</label>
                            <input type="text" id="first_name" name="first_name" 
                                   value="{{ old('first_name', $defaultAddress->first_name ?? ($user->name ?? '')) }}" required>
                            @error('first_name')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group @error('last_name') error @enderror">
                            <label for="last_name">Nazwisko *</label>
                            <input type="text" id="last_name" name="last_name" 
                                   value="{{ old('last_name', $defaultAddress->last_name ?? '') }}" required>
                            @error('last_name')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group @error('email') error @enderror">
                            <label for="email">Email *</label>
                            <input type="email" id="email" name="email" 
                                   value="{{ old('email', $user->email ?? '') }}" required>
                            @error('email')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group @error('phone') error @enderror">
                            <label for="phone">Telefon *</label>
                            <input type="tel" id="phone" name="phone" 
                                   value="{{ old('phone', $defaultAddress->phone ?? ($user->phone ?? '')) }}" required>
                            @error('phone')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Shipping Address -->
                <div class="form-section">
                    <h2><i class="fas fa-truck"></i> Adres dostawy</h2>

                    @if($addresses->count() > 0)
                    <div class="form-group">
                        <label>Zapisane adresy</label>
                        <select id="saved-address" onchange="fillAddress(this)">
                            <option value="">-- Wprowadź nowy adres --</option>
                            @foreach($addresses as $address)
                            <option value="{{ $address->id }}" 
                                    data-first-name="{{ $address->first_name }}"
                                    data-last-name="{{ $address->last_name }}"
                                    data-street="{{ $address->street_address }}"
                                    data-apartment="{{ $address->apartment }}"
                                    data-city="{{ $address->city }}"
                                    data-postal="{{ $address->postal_code }}"
                                    data-country="{{ $address->country }}"
                                    data-phone="{{ $address->phone }}"
                                    {{ $address->is_default ? 'selected' : '' }}>
                                {{ $address->first_name }} {{ $address->last_name }} - {{ $address->street_address }}, {{ $address->city }}
                                {{ $address->is_default ? '(domyślny)' : '' }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <div class="form-group @error('street_address') error @enderror">
                        <label for="street_address">Ulica i numer *</label>
                        <input type="text" id="street_address" name="street_address" 
                               value="{{ old('street_address', $defaultAddress->street_address ?? '') }}" required>
                        @error('street_address')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="apartment">Mieszkanie / Piętro (opcjonalne)</label>
                        <input type="text" id="apartment" name="apartment" 
                               value="{{ old('apartment', $defaultAddress->apartment ?? '') }}">
                    </div>

                    <div class="form-row">
                        <div class="form-group @error('city') error @enderror">
                            <label for="city">Miasto *</label>
                            <input type="text" id="city" name="city" 
                                   value="{{ old('city', $defaultAddress->city ?? '') }}" required>
                            @error('city')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group @error('postal_code') error @enderror">
                            <label for="postal_code">Kod pocztowy *</label>
                            <input type="text" id="postal_code" name="postal_code" 
                                   value="{{ old('postal_code', $defaultAddress->postal_code ?? '') }}" 
                                   pattern="[0-9]{2}-[0-9]{3}" placeholder="00-000" required>
                            @error('postal_code')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group @error('country') error @enderror">
                        <label for="country">Kraj *</label>
                        <select id="country" name="country" required>
                            <option value="Polska" {{ old('country', $defaultAddress->country ?? 'Polska') == 'Polska' ? 'selected' : '' }}>Polska</option>
                        </select>
                        @error('country')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Shipping Method -->
                <div class="form-section">
                    <h2><i class="fas fa-shipping-fast"></i> Metoda dostawy</h2>

                    <div class="shipping-options">
                        @foreach($shippingMethods as $key => $method)
                        <label class="option-card {{ old('shipping_method', 'standard') == $key ? 'selected' : '' }}">
                            <input type="radio" name="shipping_method" value="{{ $key }}" 
                                   {{ old('shipping_method', 'standard') == $key ? 'checked' : '' }}
                                   onchange="updateShipping({{ $method['price'] }}); this.closest('.shipping-options').querySelectorAll('.option-card').forEach(c => c.classList.remove('selected')); this.closest('.option-card').classList.add('selected');">
                            <div class="option-info">
                                <div class="option-name">{{ $method['name'] }}</div>
                            </div>
                            <div class="option-price">
                                @if($method['price'] > 0)
                                    {{ number_format($method['price'], 2) }} zł
                                @else
                                    Bezpłatnie
                                @endif
                            </div>
                        </label>
                        @endforeach
                    </div>
                    @error('shipping_method')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Payment Method -->
                <div class="form-section">
                    <h2><i class="fas fa-credit-card"></i> Metoda płatności</h2>

                    <div class="payment-options">
                        @foreach($paymentMethods as $key => $name)
                        <label class="option-card {{ old('payment_method', 'cash_on_delivery') == $key ? 'selected' : '' }}">
                            <input type="radio" name="payment_method" value="{{ $key }}" 
                                   {{ old('payment_method', 'cash_on_delivery') == $key ? 'checked' : '' }}
                                   onchange="this.closest('.payment-options').querySelectorAll('.option-card').forEach(c => c.classList.remove('selected')); this.closest('.option-card').classList.add('selected');">
                            <div class="option-info">
                                <div class="option-name">{{ $name }}</div>
                            </div>
                            @if($key == 'payu')
                                <i class="fab fa-cc-visa" style="font-size: 1.5rem; color: var(--gray);"></i>
                            @elseif($key == 'bank_transfer')
                                <i class="fas fa-university" style="font-size: 1.5rem; color: var(--gray);"></i>
                            @else
                                <i class="fas fa-money-bill-wave" style="font-size: 1.5rem; color: var(--gray);"></i>
                            @endif
                        </label>
                        @endforeach
                    </div>
                    @error('payment_method')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Customer Notes -->
                <div class="form-section">
                    <h2><i class="fas fa-comment"></i> Uwagi do zamówienia</h2>
                    <div class="form-group">
                        <textarea name="customer_notes" rows="3" placeholder="Dodatkowe uwagi (opcjonalne)">{{ old('customer_notes') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="order-summary">
                <h2>Podsumowanie zamówienia</h2>

                <div class="cart-items">
                    @foreach($cart['items'] as $key => $item)
                    <div class="cart-item">
                        <div class="cart-item-image">
                            @if($item['image'])
                                <img src="{{ asset('storage/' . $item['image']) }}" alt="{{ $item['name'] }}">
                            @else
                                <i class="fas fa-compact-disc"></i>
                            @endif
                        </div>
                        <div class="cart-item-details">
                            <div class="cart-item-name">{{ $item['name'] }}</div>
                            <div class="cart-item-qty">Ilość: {{ $item['quantity'] }}</div>
                        </div>
                        <div class="cart-item-price">{{ number_format($item['price'] * $item['quantity'], 2) }} zł</div>
                    </div>
                    @endforeach
                </div>

                <!-- Coupon -->
                @if($appliedCoupon)
                <div class="applied-coupon">
                    <div>
                        <span><i class="fas fa-tag"></i> {{ $appliedCoupon }}</span>
                        @if($discount > 0)
                        <div style="font-size: 0.875rem; color: var(--success); margin-top: 0.25rem;">
                            Rabat: -{{ number_format($discount, 2) }} zł
                        </div>
                        @else
                        <div style="font-size: 0.875rem; color: var(--warning); margin-top: 0.25rem;">
                            <i class="fas fa-exclamation-triangle"></i> Kupon zastosowany, ale rabat = 0 zł
                        </div>
                        @endif
                    </div>
                    <button type="button" id="remove-coupon-btn" title="Usuń kupon" style="background: none; border: none; color: var(--gray); cursor: pointer; padding: 0.25rem 0.5rem;">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                @else
                <div style="margin-bottom: 1rem;">
                    <form id="coupon-form" class="coupon-form" onsubmit="return false;">
                        @csrf
                        <input type="text" id="coupon_code" name="coupon_code" placeholder="Kod rabatowy" value="{{ old('coupon_code') }}" required style="text-transform: uppercase;">
                        <button type="button" id="apply-coupon-btn">Zastosuj</button>
                    </form>
                    <div id="coupon-message" style="margin-top: 0.5rem;"></div>
                    @if(session('error'))
                    <div style="color: var(--danger); font-size: 0.875rem; margin-top: 0.5rem; padding: 0.5rem; background: rgba(239, 68, 68, 0.1); border-radius: 6px;">
                        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                    </div>
                    @endif
                    @if(session('success'))
                    <div style="color: var(--success); font-size: 0.875rem; margin-top: 0.5rem; padding: 0.5rem; background: rgba(16, 185, 129, 0.1); border-radius: 6px;">
                        <i class="fas fa-check-circle"></i> {{ session('success') }}
                    </div>
                    @endif
                </div>
                @endif

                <div class="summary-row">
                    <span>Produkty</span>
                    <span>{{ number_format($cart['total'], 2) }} zł</span>
                </div>

                <div class="summary-row">
                    <span>Dostawa</span>
                    <span id="shipping-cost">{{ number_format($shippingMethods['standard']['price'], 2) }} zł</span>
                </div>

                @if($discount > 0)
                <div class="summary-row discount">
                    <span>Rabat</span>
                    <span>-{{ number_format($discount, 2) }} zł</span>
                </div>
                @endif

                <div class="summary-row total">
                    <span>Do zapłaty</span>
                    <span id="total-amount">{{ number_format($cart['total'] + $shippingMethods['standard']['price'] - $discount, 2) }} zł</span>
                </div>

                <div class="terms-checkbox">
                    <input type="checkbox" id="terms_accepted" name="terms_accepted" value="1" required>
                    <label for="terms_accepted">
                        Akceptuję <a href="#" target="_blank">regulamin</a> i <a href="#" target="_blank">politykę prywatności</a> *
                    </label>
                </div>
                @error('terms_accepted')
                    <div class="error-message">{{ $message }}</div>
                @enderror

                <button type="submit" class="checkout-btn">
                    <i class="fas fa-lock"></i> Złóż zamówienie
                </button>

                <p style="text-align: center; font-size: 0.875rem; color: var(--gray); margin-top: 1rem;">
                    <i class="fas fa-shield-alt"></i> Bezpieczna płatność
                </p>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    const cartTotal = {{ $cart['total'] }};
    let discount = {{ $discount }};
    let currentShipping = {{ $shippingMethods['standard']['price'] }};
    
    // Debug: Log initial values
    console.log('Cart Total:', cartTotal);
    console.log('Initial Discount:', discount);
    console.log('Applied Coupon:', '{{ $appliedCoupon ?? "none" }}');
    
    function updateShipping(price) {
        currentShipping = price;
        document.getElementById('shipping-cost').textContent = price.toFixed(2) + ' zł';
        updateTotal();
    }
    
    function updateTotal() {
        const total = cartTotal + currentShipping - discount;
        document.getElementById('total-amount').textContent = total.toFixed(2) + ' zł';
    }
    
    function fillAddress(select) {
        const option = select.options[select.selectedIndex];
        if (!option.value) return;
        
        document.getElementById('first_name').value = option.dataset.firstName || '';
        document.getElementById('last_name').value = option.dataset.lastName || '';
        document.getElementById('street_address').value = option.dataset.street || '';
        document.getElementById('apartment').value = option.dataset.apartment || '';
        document.getElementById('city').value = option.dataset.city || '';
        document.getElementById('postal_code').value = option.dataset.postal || '';
        if (option.dataset.phone) {
            document.getElementById('phone').value = option.dataset.phone;
        }
    }
    
    // Coupon form handling with AJAX
    document.addEventListener('DOMContentLoaded', function() {
        const savedAddress = document.getElementById('saved-address');
        if (savedAddress && savedAddress.value) {
            fillAddress(savedAddress);
        }
        
        // Update total on load to ensure correct display
        updateTotal();
        
        // Apply coupon button
        const applyCouponBtn = document.getElementById('apply-coupon-btn');
        if (applyCouponBtn) {
            applyCouponBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const couponCode = document.getElementById('coupon_code').value.trim().toUpperCase();
                const messageDiv = document.getElementById('coupon-message');
                
                if (!couponCode) {
                    showCouponMessage('Wprowadź kod kuponu', 'error');
                    return;
                }
                
                // Disable button during request
                applyCouponBtn.disabled = true;
                applyCouponBtn.textContent = 'Sprawdzam...';
                
                // Get CSRF token
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') 
                    || document.querySelector('input[name="_token"]')?.value;
                
                // Send AJAX request
                fetch('{{ route("checkout.coupon") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        coupon_code: couponCode
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => Promise.reject(err));
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        showCouponMessage(data.message || 'Kupon został zastosowany.', 'success');
                        // Reload page after short delay to show updated discount
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    } else {
                        showCouponMessage(data.message || 'Nieprawidłowy kod kuponu.', 'error');
                        applyCouponBtn.disabled = false;
                        applyCouponBtn.textContent = 'Zastosuj';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    const errorMessage = error.message || error.error || 'Wystąpił błąd. Spróbuj ponownie.';
                    showCouponMessage(errorMessage, 'error');
                    applyCouponBtn.disabled = false;
                    applyCouponBtn.textContent = 'Zastosuj';
                });
            });
        }
        
        // Remove coupon button
        const removeCouponBtn = document.getElementById('remove-coupon-btn');
        if (removeCouponBtn) {
            removeCouponBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                // Get CSRF token
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') 
                    || document.querySelector('input[name="_token"]')?.value;
                
                // Send AJAX request
                fetch('{{ route("checkout.coupon.remove") }}', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => Promise.reject(err));
                    }
                    return response.json();
                })
                .then(data => {
                    // Reload page to show updated state
                    window.location.reload();
                })
                .catch(error => {
                    console.error('Error:', error);
                    // Still reload to ensure state is correct
                    window.location.reload();
                });
            });
        }
        
        // Allow Enter key to submit coupon form
        const couponCodeInput = document.getElementById('coupon_code');
        if (couponCodeInput) {
            couponCodeInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    e.stopPropagation();
                    if (applyCouponBtn) {
                        applyCouponBtn.click();
                    }
                }
            });
        }
    });
    
    function showCouponMessage(message, type) {
        const messageDiv = document.getElementById('coupon-message');
        if (!messageDiv) return;
        
        const color = type === 'error' ? 'var(--danger)' : 'var(--success)';
        const bgColor = type === 'error' ? 'rgba(239, 68, 68, 0.1)' : 'rgba(16, 185, 129, 0.1)';
        const icon = type === 'error' ? 'fa-exclamation-circle' : 'fa-check-circle';
        
        messageDiv.innerHTML = `
            <div style="color: ${color}; font-size: 0.875rem; padding: 0.5rem; background: ${bgColor}; border-radius: 6px;">
                <i class="fas ${icon}"></i> ${message}
            </div>
        `;
    }
</script>
@endpush
@endsection