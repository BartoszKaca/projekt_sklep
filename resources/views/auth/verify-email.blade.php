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
                            Wysłaliśmy 6-cyfrowy kod weryfikacyjny na adres: <strong>{{ auth()->user()->email }}</strong>
                        </div>

                        <p class="mb-4">Wprowadź kod z wiadomości email, aby zweryfikować swoje konto i uzyskać pełny dostęp.</p>

                        <form method="POST" action="{{ route('verify.email') }}">
                            @csrf

                            <div class="row mb-3">
                                <label for="code" class="col-md-4 col-form-label text-md-end">Kod weryfikacyjny</label>

                                <div class="col-md-6">
                                    <input id="code" 
                                           type="text" 
                                           class="form-control @error('code') is-invalid @enderror" 
                                           name="code" 
                                           value="{{ old('code') }}" 
                                           required 
                                           autofocus
                                           maxlength="6"
                                           placeholder="000000"
                                           pattern="[0-9]{6}"
                                           inputmode="numeric">

                                    @error('code')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror

                                    <small class="form-text text-muted">
                                        Wpisz 6-cyfrowy kod z emaila
                                    </small>
                                </div>
                            </div>

                            <div class="row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        Zweryfikuj email
                                    </button>
                                </div>
                            </div>
                        </form>

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
