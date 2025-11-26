@extends('layouts.app')

@section('title', 'Logowanie')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">Logowanie</div>

        <div class="card-body">
            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="row mb-3">
                    <label for="email" class="col-form-label">Adres email</label>

                    <div>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="password" class="col-form-label">Hasło</label>

                    <div>
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                            <label class="form-check-label" for="remember">
                                Zapamiętaj mnie
                            </label>
                        </div>
                    </div>
                </div>

                <div class="row mb-0">
                    <div>
                        <button type="submit" class="btn btn-primary">
                            Zaloguj się
                        </button>

                        @if (Route::has('password.request'))
                            <a class="btn btn-link" href="{{ route('password.request') }}">
                                Zapomniałeś hasła?
                            </a>
                        @endif
                    </div>
                </div>
                
                <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid var(--border); text-align: center;">
                    <p style="color: var(--gray);">Nie masz jeszcze konta?</p>
                    <a href="{{ route('register') }}" class="btn btn-primary" style="margin-top: 0.5rem;">Zarejestruj się</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
