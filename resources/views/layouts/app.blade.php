<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Rap Shop') }}</title>

    <!-- Czcionki -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Ikony -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Style CSS - te same co w shop.blade.php -->
    <link rel="stylesheet" href="{{ asset('css/shop.css') }}">
    <link rel="stylesheet" href="{{ asset('css/footer.css') }}">
    <link rel="stylesheet" href="{{ asset('css/products.css') }}">
    <link rel="stylesheet" href="{{ asset('css/utilities.css') }}">

    @stack('styles')
</head>

<body>
    <!-- Górny pasek z informacjami -->
    <div class="top-bar">
        <div class="top-bar-content">
            <div>
                <i class="fas fa-shipping-fast"></i> Darmowa dostawa od 100 zł
            </div>
            <div class="top-bar-links">
                <a href="#"><i class="fas fa-phone"></i> +48 123 456 789</a>
                <a href="#"><i class="fas fa-envelope"></i> kontakt@rapshop.pl</a>
            </div>
        </div>
    </div>

    <!-- Główna nawigacja -->
    <nav class="navbar">
        <div class="navbar-content">
            <!-- Przycisk menu mobilne -->
            <button class="mobile-menu-btn" onclick="toggleMobileMenu()">
                <i class="fas fa-bars"></i>
            </button>

            <!-- Logo -->
            <a href="{{ route('home') }}" class="logo">
                <i class="fas fa-compact-disc"></i>
                RAP SHOP
            </a>

            <!-- Spacer - wypycha przyciski do prawej -->
            <div class="nav-spacer"></div>

            <!-- Przyciski po prawej stronie -->
            <div class="nav-actions">
                <!-- Wishlist - tylko dla zalogowanych -->
                @auth
                <a href="{{ route('account.wishlist') }}" class="nav-btn" title="Lista życzeń" data-wishlist-btn>
                    <i class="fas fa-heart"></i>
                    <span class="badge">{{ auth()->user()->wishlist->count() ?? 0 }}</span>
                </a>
                @endauth

                <!-- Koszyk -->
                @php
                    $cartItems = session('cart.items', []);
                    $cartCount = 0;
                    foreach ($cartItems as $ci) { 
                        $cartCount += ($ci['quantity'] ?? 0); 
                    }
                @endphp

                <a href="{{ route('cart.index') }}" class="nav-btn" title="Koszyk">
                    <i class="fas fa-shopping-bag"></i>
                    <span class="badge" id="cart-count">{{ $cartCount }}</span>
                </a>

                <!-- Konto użytkownika -->
                @auth
                    @if(auth()->user()->hasVerifiedEmail())
                    <a href="{{ route('account.dashboard') }}" class="user-menu-btn">
                        <div class="user-avatar">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <span>{{ auth()->user()->name }}</span>
                    </a>
                    @else
                    <a href="{{ route('verify.email.form') }}" class="user-menu-btn" title="Zweryfikuj swój email">
                        <i class="fas fa-exclamation-circle" style="color: var(--warning);"></i>
                        <span>Weryfikacja</span>
                    </a>
                    @endif
                @else
                <a href="{{ route('login') }}" class="user-menu-btn">
                    <i class="fas fa-user"></i>
                    <span>Zaloguj</span>
                </a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Menu kategorii -->
    <div class="categories-nav">
        <div class="categories-content">
            @php
            $navCategories = \App\Models\Category::orderBy('name')->take(12)->get();
            @endphp

            <a href="{{ route('home') }}" class="cat-link {{ request()->routeIs('home') ? 'active' : '' }}">
                <i class="fas fa-fire"></i> Nowości
            </a>

            @foreach($navCategories as $navCat)
            <a href="{{ route('category.show', $navCat->slug) }}" class="cat-link {{ request()->is('kategoria/'.$navCat->slug) ? 'active' : '' }}">
                <i class="fas fa-folder"></i> {{ $navCat->name }}
            </a>
            @endforeach
        </div>
    </div>

    <!-- Główna treść strony -->
    <main class="main-content py-4">
        @yield('content')
    </main>

    <!-- Stopka -->
    <footer class="footer">
        <div class="footer-content">
            <!-- O sklepie -->
            <div class="footer-section">
                <h3>
                    <i class="fas fa-compact-disc"></i>
                    RAP SHOP
                </h3>
                <p>
                    Twój numer jeden w świecie polskiego hip-hopu.
                    Oferujemy największy wybór płyt, vinylu i merchu od
                    najpopularniejszych polskich raperów.
                </p>
                <div class="social-links">
                    <a href="#" class="social-btn" title="Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="social-btn" title="Instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="#" class="social-btn" title="YouTube">
                        <i class="fab fa-youtube"></i>
                    </a>
                    <a href="#" class="social-btn" title="TikTok">
                        <i class="fab fa-tiktok"></i>
                    </a>
                </div>
            </div>

            <!-- Sklep -->
            <div class="footer-section">
                <h3>Sklep</h3>
                <ul class="footer-links">
                    <li><a href="{{ route('home') }}">Nowości</a></li>
                    <li><a href="{{ route('products.index') }}">Wszystkie produkty</a></li>
                    <li><a href="{{ route('products.index') }}?sort=popular">Bestsellery</a></li>
                    <li><a href="{{ route('products.index') }}?filter=sale">Promocje</a></li>
                    <li><a href="{{ route('products.index') }}?filter=limited">Limitowane edycje</a></li>
                </ul>
            </div>

            <!-- Informacje -->
            <div class="footer-section">
                <h3>Informacje</h3>
                <ul class="footer-links">
                    <li><a href="{{ route('about') }}">O nas</a></li>
                    <li><a href="{{ route('terms') }}">Regulamin</a></li>
                    <li><a href="{{ route('privacy') }}">Polityka prywatności</a></li>
                    <li><a href="{{ route('shipping') }}">Dostawa i płatność</a></li>
                    <li><a href="{{ route('returns') }}">Zwroty i reklamacje</a></li>
                </ul>
            </div>

            <!-- Newsletter -->
            <div class="footer-section">
                <h3>Newsletter</h3>
                <p>Zapisz się i otrzymuj info o nowościach i promocjach!</p>
                <form class="newsletter-form" id="newsletter-form" onsubmit="subscribeNewsletter(event)">
                    <input type="email" name="email" placeholder="Twój email" required>
                    <button type="submit">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </form>
            </div>
        </div>

        <!-- Dół stopki -->
        <div class="footer-bottom">
            <div class="payment-methods">
                <i class="fab fa-cc-visa"></i>
                <i class="fab fa-cc-mastercard"></i>
                <i class="fab fa-cc-paypal"></i>
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <p style="margin-top: 1rem;">
                © {{ date('Y') }} Rap Shop. Wszystkie prawa zastrzeżone.
            </p>
        </div>
    </footer>

    <!-- JavaScript -->
    <script>
        // Menu mobilne
        function toggleMobileMenu() {
            console.log('Toggle mobile menu');
        }
        
        // Dodawanie/usuwanie z listy życzeń
        async function toggleWishlist(productId) {
            @guest
                alert('Musisz być zalogowany, aby dodać produkt do ulubionych');
                window.location.href = '{{ route('login') }}';
                return;
            @endguest

            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            try {
                const wishlistUrl = @json(route('account.wishlist.add')) || '/account/wishlist/add';
                
                const res = await fetch(wishlistUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ product_id: productId })
                });

                if (res.status === 404) {
                    console.error('Route not found:', wishlistUrl);
                    alert('Błąd: Nie znaleziono ścieżki. Proszę odświeżyć stronę.');
                    return;
                }

                let data;
                try {
                    data = await res.json();
                } catch (jsonError) {
                    console.error('JSON parse error:', jsonError);
                    alert('Błąd: Nieprawidłowa odpowiedź z serwera.');
                    return;
                }

                if (data.success) {
                    // Aktualizuj licznik wishlisty
                    const badge = document.querySelector('a[data-wishlist-btn] .badge');
                    if (badge && data.count) badge.textContent = data.count;
                    showNotification(data.message || 'Dodano do ulubionych', 'success');
                } else {
                    showNotification(data.message || 'Wystąpił błąd', 'error');
                }
            } catch (e) {
                console.error(e);
                showNotification('Wystąpił błąd', 'error');
            }
        }
        
        // Zapis do newslettera
        async function subscribeNewsletter(event) {
            event.preventDefault();
            const form = event.target;
            const email = form.email.value;
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            
            try {
                const res = await fetch('{{ route('newsletter.subscribe') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ email: email })
                });
                
                const data = await res.json();
                if (data.success) {
                    showNotification(data.message || 'Dziękujemy za zapisanie!', 'success');
                    form.reset();
                } else {
                    showNotification(data.message || 'Wystąpił błąd', 'error');
                }
            } catch (e) {
                console.error(e);
                showNotification('Wystąpił błąd podczas zapisywania do newslettera', 'error');
            }
        }
        
        // Wyświetlanie powiadomień
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `notification notification-${type}`;
            notification.innerHTML = `
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
                    <span>${message}</span>
                </div>
            `;
            
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#3b82f6'};
                color: white;
                padding: 1rem 1.5rem;
                border-radius: 12px;
                box-shadow: 0 10px 25px rgba(0,0,0,0.2);
                z-index: 10000;
                animation: slideIn 0.3s ease;
            `;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.style.animation = 'slideOut 0.3s ease';
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }

        // Animacje dla powiadomień
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideIn {
                from { transform: translateX(400px); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            @keyframes slideOut {
                from { transform: translateX(0); opacity: 1; }
                to { transform: translateX(400px); opacity: 0; }
            }
        `;
        document.head.appendChild(style);
    </script>
    
    @stack('scripts')
</body>
</html>
