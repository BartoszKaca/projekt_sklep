@extends('layouts.app')

@section('title','Moje zamówienia')

@section('content')
<h1>Moje zamówienia</h1>
@if($orders->isEmpty())
    <p>Brak zamówień.</p>
@else
    <ul>
        @foreach($orders as $order)
            <li><a href="{{ route('admin.orders.show', $order->id) }}">Zamówienie #{{ $order->id }}</a> — {{ $order->status }} — {{ $order->total }} zł</li>
        @endforeach
    </ul>
    {{ $orders->links() }}
@endif
@endsection