@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1>Ruchy magazynowe - {{ $product->name }}</h1>
            
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Typ ruchu</th>
                        <th>Ilość</th>
                        <th>Uwagi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($movements as $movement)
                        <tr>
                            <td>{{ $movement->created_at->format('d.m.Y H:i') }}</td>
                            <td>
                                <span class="badge badge-{{ $movement->type === 'in' ? 'success' : 'danger' }}">
                                    {{ $movement->type === 'in' ? 'Przychód' : 'Rozchód' }}
                                </span>
                            </td>
                            <td>{{ $movement->quantity }}</td>
                            <td>{{ $movement->notes }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">Brak ruchów magazynowych</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection