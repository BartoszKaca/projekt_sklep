@extends('layouts.app')

@section('title', 'Reset hasła')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">Zresetuj hasło</div>

        <div class="card-body">
            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
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

                <div class="row mb-0">
                    <div>
                        <button type="submit" class="btn btn-primary">
                            Wyślij link do resetowania
                        </button>
                    </div>
                </div>
                
                <div style="margin-top: 1.5rem; text-align: center;">
                    <a href="{{ route('login') }}" class="btn btn-link">Powrót do logowania</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
