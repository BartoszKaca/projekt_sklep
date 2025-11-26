@extends('layouts.app')

@section('title','Ulubione')

@section('content')
<h1>Ulubione</h1>
@if($wishlist->isEmpty())
    <p>Brak produktów w ulubionych.</p>
@else
    <ul>
        @foreach($wishlist as $w)
            <li>{{ $w->product->name ?? '—' }}</li>
        @endforeach
    </ul>
@endif
@endsection