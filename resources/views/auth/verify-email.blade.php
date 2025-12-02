@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endpush

@section('content')
<div class="auth-page">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card auth-card">
                    <div class="card-header">Weryfikacja adresu email</div>

                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success" role="alert">
                                {{ session('success') }}
                            </div>
                        @endif

                        <div class="alert alert-info" role="alert">
                            <strong>Sprawdź swoją skrzynkę email!</strong><br>
                            Wysłaliśmy link weryfikacyjny na adres: <strong>{{ auth()->user()->email }}</strong>
                        </div>

                        <p class="mb-4">Kliknij link w wiadomości email, aby zweryfikować swoje konto i uzyskać pełny dostęp.</p>

                        <div class="mb-4">
                            <p>Jeżeli nie otrzymałeś/aś linku weryfikacyjnego, możesz poprosić o jego ponowne wysłanie poniżej.</p>
                        </div>

                        <hr class="my-4">

                        <div class="text-center">
                            <p class="mb-2">Nie otrzymałeś kodu?</p>
                            <form method="POST" action="{{ route('verification.resend') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-link">
                                    Wyślij nowy kod
                                </button>
                            </form>
                        </div>

                        <div class="text-center mt-3">
                            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-link text-muted">
                                    Wyloguj się
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-format code input
document.getElementById('code').addEventListener('input', function(e) {
    this.value = this.value.replace(/[^0-9]/g, '');
});
</script>
@endsection
