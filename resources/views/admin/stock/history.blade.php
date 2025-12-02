@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1>Historia Ruchów Magazynowych</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Ruchy magazynowe</h3>
                </div>
                <div class="card-body">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Produkt</th>
                                <th>Typ ruchu</th>
                                <th>Ilość</th>
                                <th>Użytkownik</th>
                                <th>Uwagi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($history as $record)
                                <tr>
                                    <td>{{ $record->created_at->format('d.m.Y H:i') }}</td>
                                    <td>{{ $record->product->name ?? 'N/A' }}</td>
                                    <td><span class="badge badge-{{ $record->type === 'in' ? 'success' : 'danger' }}">{{ $record->type }}</span></td>
                                    <td>{{ $record->quantity }}</td>
                                    <td>{{ $record->user->name ?? 'N/A' }}</td>
                                    <td>{{ $record->notes }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">Brak historii ruchów</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection