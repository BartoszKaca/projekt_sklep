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
                    <div class="card-header">
                        <h3>Weryfikacja adresu email</h3>
                    </div>

                    <div class="card-body">
                        @if (session('resent'))
                            <div class="alert alert-success" role="alert">
                                Nowy link weryfikacyjny został wysłany na Twój adres email.
                            </div>
                        @endif

                        @if (session('success'))
                            <div class="alert alert-success" role="alert">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger" role="alert">
                                {{ session('error') }}
                            </div>
                        @endif

                        @if (session('info'))
                            <div class="alert alert-info" role="alert">
                                {{ session('info') }}
                            </div>
                        @endif

                        <p>
                            Zanim przejdziesz dalej, sprawdź swoją skrzynkę email w poszukiwaniu linku weryfikacyjnego.
                        </p>
                        
                        <p>
                            Jeśli nie otrzymałeś/aś emaila, możesz poprosić o wysłanie nowego linku:
                        </p>

                        <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                            @csrf
                            <button type="submit" class="btn btn-primary">
                                Wyślij ponownie link weryfikacyjny
                            </button>
                        </form>

                        <div class="mt-3">
                            <a href="{{ route('home') }}" class="btn btn-link">
                                Wróć do strony głównej
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
