<!doctype html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>@yield('title', 'Sklep')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <header>
        <nav>
            <a href="{{ route('home') }}">Home</a> |
            <a href="{{ route('cart.index') }}">Koszyk ({{ session('cart_count', 0) }})</a> |
            @auth
                <a href="{{ route('account.dashboard') }}">Konto</a> |
                <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit">Wyloguj</button>
                </form>
            @else
                <a href="{{ route('login') }}">Zaloguj</a> |
                <a href="{{ route('register') }}">Rejestracja</a>
            @endauth
        </nav>
    </header>

    <main>
        @if(session('success'))
            <div style="color:green;">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div style="color:red;">{{ session('error') }}</div>
        @endif

        @yield('content')
    </main>

    <footer>
        <p>&copy; {{ date('Y') }} Sklep</p>
    </footer>
</body>
</html>