@extends('layouts.app')

@section('title','Checkout')

@section('content')
<h1>Finalizacja zamówienia</h1>

<form method="POST" action="{{ route('checkout.place') }}">
    @csrf
    <div>
        <label>Imię i nazwisko</label>
        <input type="text" name="name" value="{{ old('name', auth()->user()->name ?? '') }}" required>
    </div>
    <div>
        <label>Email</label>
        <input type="email" name="email" value="{{ old('email', auth()->user()->email ?? '') }}" required>
    </div>
    <div>
        <label>Adres</label>
        <textarea name="address" required>{{ old('address') }}</textarea>
    </div>
    <button type="submit">Złóż zamówienie</button>
</form>
@endsection