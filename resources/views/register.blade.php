@extends('layouts.app')

@section('title','Rejestracja')

@section('content')
<h1>Rejestracja</h1>
<form method="POST" action="{{ route('register') }}">
    @csrf
    <div>
        <label>Imię</label>
        <input type="text" name="name" value="{{ old('name') }}" required>
    </div>
    <div>
        <label>Email</label>
        <input type="email" name="email" value="{{ old('email') }}" required>
    </div>
    <div>
        <label>Hasło</label>
        <input type="password" name="password" required>
    </div>
    <div>
        <label>Powtórz hasło</label>
        <input type="password" name="password_confirmation" required>
    </div>
    <button type="submit">Zarejestruj</button>
</form>
@endsection