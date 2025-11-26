@extends('layouts.app')

@section('title','Moje konto')

@section('content')
<h1>Witaj, {{ auth()->user()->name }}</h1>
<ul>
    <li><a href="{{ route('account.orders') }}">Historia zamówień</a></li>
    <li><a href="{{ route('account.wishlist') }}">Ulubione</a></li>
</ul>
@endsection