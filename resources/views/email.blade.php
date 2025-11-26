@extends('layouts.app')

@section('title','Reset hasła')

@section('content')
<h1>Reset hasła</h1>
@if (session('status'))
    <div>{{ session('status') }}</div>
@endif
<form method="POST" action="{{ route('password.email') }}">
    @csrf
    <div>
        <label>Email</label>
        <input type="email" name="email" value="{{ old('email') }}" required>
    </div>
    <button type="submit">Wyślij link resetujący</button>
</form>
@endsection