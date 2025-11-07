<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Rap Shop') - Twój sklep z rapem</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --secondary: #ec4899;
            --dark: #0f172a;
            --dark-light: #1e293b;
            --gray: #64748b;
            --light: #f8fafc;
            --white: #ffffff;
            --border: #e2e8f0;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--light);
            color: var(--dark);
            line-height: 1.6;
        }

        /* Top Bar */
        .top-bar {
            background: var(--dark);
            color: white;
            padding: 0.5rem 0;
            font-size: 0.875rem;
        }

        .top-bar-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .top-bar-links {
            display: flex;
            gap: 1.5rem;
        }

        .top-bar-links a {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: color 0.2s;
        }

        .top-bar-links a:hover {
            color: white;
        }

        /* Navbar */
        .navbar {
            background: white;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 1rem 2rem;
            display: flex;
            align-items: center;
            gap: 2rem;
        }

        .logo {
            font-size: 1.75rem;
            font-weight: 900;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-search {
            flex: 1;
            max-width: 600px;
            position: relative;
        }

        .nav-search input {
            width: 100%;
            padding: 0.875rem 1.25rem 0.875rem 3rem;
            border: 2px solid var(--border);
            border-radius: 50px;
            font-size: 0.9375rem;
            transition: all 0.2s;
        }

        .nav-search input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(99,102,241,0.1);
        }

        .nav-search i {
            position: absolute;
            left: 1.25rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray);
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .nav-btn {
            position: relative;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            border: none;
            background: var(--light);
            color: var(--dark);
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.125rem;
        }

        .nav-btn:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-2px);
        }

        .nav-btn .badge {
            position: absolute;
            top: -4px;
            right: -4px;
            background: var(--danger);
            color: white;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            font-size: 0.75rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .user-menu-btn {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            background: var(--light);
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            font-weight: 600;
        }

        .user-menu-btn:hover {
            background: var(--primary);
            color: white;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 0.875rem;
        }

        /* Categories Nav */
        .categories-nav {
            background: white;
            border-bottom: 1px solid var(--border);
        }

        .categories-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            gap: 2rem;
            overflow-x: auto;
        }

        .categories-content::-webkit-scrollbar {
            display: none;
        }

        .cat-link {
            padding: 1rem 0;
            color: var(--gray);
            text-decoration: none;
            font-weight: 500;
            white-space: nowrap;
            position: relative;
            transition: color 0.2s;
        }

        .cat-link:hover,
        .cat-link.active {
            color: var(--primary);
        }

        .cat-link.active::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: var(--primary);
        }

        /* Main Content */
        .main-content {
            min-height: calc(100vh - 400px);
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }

        /* Footer */
        .footer {
            background: var(--dark);
            color: white;
            margin-top: 4rem;
        }

        .footer-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 3rem 2rem;
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 3rem;
        }

        .footer-section h3 {
            font-size: 1.25rem;
            margin-bottom: 1.5rem;
            font-weight: 700;
        }

        .footer-section p {
            color: rgba(255,255,255,0.7);
            line-height: 1.8;
            margin-bottom: 1rem;
        }

        .footer-links {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: 0.75rem;
        }

        .footer-links a {
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            transition: color 0.2s;
        }

        .footer-links a:hover {
            color: white;
        }

        .social-links {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }

        .social-btn {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            transition: all 0.2s;
        }

        .social-btn:hover {
            background: var(--primary);
            transform: translateY(-2px);
        }

        .newsletter-form {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
        }

        .newsletter-form input {
            flex: 1;
            padding: 0.875rem 1.25rem;
            border: 2px solid rgba(255,255,255,0.2);
            border-radius: 50px;
            background: rgba(255,255,255,0.1);
            color: white;
            font-size: 0.9375rem;
        }

        .newsletter-form input::placeholder {
            color: rgba(255,255,255,0.5);
        }

        .newsletter-form input:focus {
            outline: none;
            border-color: var(--primary);
        }

        .newsletter-form button {
            padding: 0.875rem 2rem;
            border-radius: 50px;
            border: none;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .newsletter-form button:hover {
            transform: translateY(-2px);
        }

        .footer-bottom {
            border-top: 1px solid rgba(255,255,255,0.1);
            padding: 1.5rem 2rem;
            text-align: center;
            color: rgba(255,255,255,0.6);
            font-size: 0.875rem;
        }

        .payment-methods {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 1rem;
        }

        .payment-methods i {
            font-size: 2rem;
            color: rgba(255,255,255,0.4);
        }

        /* Mobile Menu */
        .mobile-menu-btn {
            display: none;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            border: none;
            background: var(--light);
            color: var(--dark);
            cursor: pointer;
            font-size: 1.25rem;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .footer-content {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 768px) {
            .top-bar {
                display: none;
            }

            .mobile-menu-btn {
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .navbar-content {
                padding: 1rem;
            }

            .nav-search {
                display: none;
            }

            .categories-nav {
                display: none;
            }

            .footer-content {
                grid-template-columns: 1fr;
                gap: 2rem;
            }

            .container {
                padding: 1rem;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Top Bar -->
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

    <!-- Navbar -->
    <nav class="navbar">
        <div class="navbar-content">
            <button class="mobile-menu-btn" onclick="toggleMobileMenu()">
                <i class="fas fa-bars"></i>
            </button>

            <a href="{{ route('home') }}" class="logo">
                <i class="fas fa-compact-disc"></i>
                RAP SHOP
            </a>

            <div class="nav-search">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Szukaj płyt, mercha, artystów...">
            </div>

            <div class="nav-actions">
                @auth
                    <button class="nav-btn" title="Lista życzeń">
                        <i class="fas fa-heart"></i>
                        <span class="badge">{{ auth()->user()->wishlist->count() ?? 0 }}</span>
                    </button>
                @endauth

                <button class="nav-btn" onclick="toggleCart()" title="Koszyk">
                    <i class="fas fa-shopping-bag"></i>
                    <span class="badge" id="cart-count">0</span>
                </button>

                @auth
                    <a href="{{ route('account.dashboard') }}" class="user-menu-btn">
                        <div class="user-avatar">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <span>{{ auth()->user()->name }}</span>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="user-menu-btn">
                        <i class="fas fa-user"></i>
                        <span>Zaloguj</span>
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Categories Navigation -->
    <div class="categories-nav">
        <div class="categories-content">
            <a href="{{ route('home') }}" class="cat-link {{ request()->routeIs('home') ? 'active' : '' }}">
                <i class="fas fa-fire"></i> Nowości
            </a>
            <a href="#" class="cat-link">
                <i class="fas fa-compact-disc"></i> Płyty CD
            </a>
            <a href="#" class="cat-link">
                <i class="fas fa-record-vinyl"></i> Winyle
            </a>
            <a href="#" class="cat-link">
                <i class="fas fa-cassette-tape"></i> Kasety
            </a>
            <a href="#" class="cat-link">
                <i class="fas fa-tshirt"></i> Koszulki
            </a>
            <a href="#" class="cat-link">
                <i class="fas fa-hat-cowboy"></i> Czapki
            </a>
            <a href="#" class="cat-link">
                <i class="fas fa-tags"></i> Promocje
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <main class="main-content">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
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

            <div class="footer-section">
                <h3>Sklep</h3>
                <ul class="footer-links">
                    <li><a href="#">Nowości</a></li>
                    <li><a href="#">Bestsellery</a></li>
                    <li><a href="#">Promocje</a></li>
                    <li><a href="#">Limitowane edycje</a></li>
                    <li><a href="#">Pre-ordery</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h3>Informacje</h3>
                <ul class="footer-links">
                    <li><a href="#">O nas</a></li>
                    <li><a href="#">Regulamin</a></li>
                    <li><a href="#">Polityka prywatności</a></li>
                    <li><a href="#">Dostawa i płatność</a></li>
                    <li><a href="#">Zwroty i reklamacje</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h3>Newsletter</h3>
                <p>Zapisz się i otrzymuj info o nowościach i promocjach!</p>
                <form class="newsletter-form">
                    <input type="email" placeholder="Twój email">
                    <button type="submit">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </form>
            </div>
        </div>

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

    <script>
        function toggleCart() {
            // Implementacja koszyka
            console.log('Toggle cart');
        }

        function toggleMobileMenu() {
            // Implementacja mobile menu
            console.log('Toggle mobile menu');
        }
    </script>
    @stack('scripts')
</body>
</html>

<!-- 
Zapisz jako: resources/views/layouts/shop.blade.php
-->