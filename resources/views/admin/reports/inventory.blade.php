{{-- resources/views/admin/reports/inventory.blade.php --}}
@extends('layouts.admin')

@section('title', 'Raport magazynowy')

@push('styles')
<style>
	.inventory-summary {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
		gap: 1rem;
		margin-bottom: 2rem;
	}

	.summary-box {
		background: white;
		padding: 1.5rem;
		border-radius: 12px;
		border: 1px solid var(--border);
		text-align: center;
	}

	.summary-box-icon {
		width: 64px;
		height: 64px;
		border-radius: 16px;
		margin: 0 auto 1rem;
		display: flex;
		align-items: center;
		justify-content: center;
		font-size: 2rem;
	}

	.summary-box h3 {
		font-size: 2rem;
		font-weight: 700;
		margin-bottom: 0.5rem;
	}

	.summary-box p {
		color: var(--gray);
		font-size: 0.875rem;
	}

	.inventory-table {
		background: white;
		border-radius: 16px;
		border: 1px solid var(--border);
		overflow: hidden;
	}

	.table-header-row {
		background: var(--light-gray);
		padding: 1rem 1.5rem;
		display: grid;
		grid-template-columns: 2fr 1fr 1fr 1fr auto;
		gap: 1rem;
		font-weight: 600;
		font-size: 0.875rem;
		color: var(--gray);
		text-transform: uppercase;
		letter-spacing: 0.05em;
	}

	.table-row {
		padding: 1rem 1.5rem;
		display: grid;
		grid-template-columns: 2fr 1fr 1fr 1fr auto;
		gap: 1rem;
		align-items: center;
		border-bottom: 1px solid var(--light-gray);
		transition: all 0.2s;
	}

	.table-row:hover {
		background: var(--light-gray);
	}

	.table-row:last-child {
		border-bottom: none;
	}

	.value-badge {
		display: inline-block;
		padding: 0.5rem 1rem;
		border-radius: 8px;
		font-weight: 700;
		font-size: 1.125rem;
	}

	.value-badge.high {
		background: #d1fae5;
		color: #065f46;
	}

	.value-badge.medium {
		background: #fef3c7;
		color: #92400e;
	}

	.value-badge.low {
		background: #fee2e2;
		color: #991b1b;
	}
</style>
@endpush

@section('content')
<div class="page-header">
	<div class="breadcrumb">
		<a href="{{ route('admin.dashboard') }}">Admin</a>
		<span>/</span>
		<a href="#">Raporty</a>
		<span>/</span>
		<span>Magazyn</span>
	</div>
	<div style="display: flex; justify-content: space-between; align-items: center;">
		<div>
			<h1 class="page-title">Raport magazynowy</h1>
			<p class="page-subtitle">Wartość i stan zapasów</p>
		</div>
		<div style="display: flex; gap: 0.5rem;">
			<a href="{{ route('admin.stock.export') }}" class="btn btn-primary">
				<i class="fas fa-download"></i> Eksportuj CSV
			</a>
			<button onclick="window.print()" class="btn btn-secondary">
				<i class="fas fa-print"></i> Drukuj
			</button>
		</div>
	</div>
</div>

<!-- Summary -->
<div class="inventory-summary">
	<div class="summary-box">
		<div class="summary-box-icon" style="background: #dbeafe; color: #1e40af;">
			<i class="fas fa-boxes"></i>
		</div>
		<h3>{{ \App\Models\Product::count() }}</h3>
		<p>Produktów</p>
	</div>

	<div class="summary-box">
		<div class="summary-box-icon" style="background: #d1fae5; color: #065f46;">
			<i class="fas fa-warehouse"></i>
		</div>
		<h3>{{ $totalUnits }}</h3>
		<p>Jednostek na stanie</p>
	</div>

	<div class="summary-box">
		<div class="summary-box-icon" style="background: #fef3c7; color: #92400e;">
			<i class="fas fa-exclamation-triangle"></i>
		</div>
		<h3>{{ $lowStockProducts->count() }}</h3>
		<p>Niski stan</p>
	</div>

	<div class="summary-box">
		<div class="summary-box-icon" style="background: #fee2e2; color: #991b1b;">
			<i class="fas fa-times-circle"></i>
		</div>
		<h3>{{ $outOfStock->count() }}</h3>
		<p>Brak w magazynie</p>
	</div>

	<div class="summary-box">
		<div class="summary-box-icon" style="background: linear-gradient(135deg, #6366f1, #ec4899); color: white;">
			<i class="fas fa-money-bill-wave"></i>
		</div>
		<h3>{{ number_format($totalValue, 0) }} zł</h3>
		<p>Wartość zapasów</p>
	</div>
</div>

<!-- Low Stock Products -->
@if($lowStockProducts->count() > 0)
<div class="chart-card" style="margin-bottom: 1.5rem;">
	<h3 class="section-title" style="margin-bottom: 1.5rem;">
		<i class="fas fa-exclamation-triangle" style="color: var(--warning);"></i> 
		Produkty z niskim stanem ({{ $lowStockProducts->count() }})
	</h3>

	<div class="inventory-table">
		<div class="table-header-row">
			<div>Produkt</div>
			<div>Kategoria</div>
			<div>Stan obecny</div>
			<div>Próg</div>
			<div>Wartość</div>
		</div>

		@foreach($lowStockProducts as $product)
		<div class="table-row">
			<div>
				<div style="font-weight: 600;">{{ $product->name }}</div>
				<div style="font-size: 0.875rem; color: var(--gray);">SKU: {{ $product->sku }}</div>
			</div>
			<div>
				<span class="badge badge-info">{{ $product->category->name }}</span>
			</div>
			<div>
				<span class="value-badge {{ $product->stock_quantity == 0 ? 'low' : 'medium' }}">
					{{ $product->stock_quantity }}
				</span>
			</div>
			<div>
				<span style="color: var(--gray);">{{ $product->low_stock_threshold }}</span>
			</div>
			<div style="font-weight: 700;">
				{{ number_format($product->stock_quantity * $product->price, 2) }} zł
			</div>
		</div>
		@endforeach
	</div>
</div>
@endif

<!-- Out of Stock -->
@if($outOfStock->count() > 0)
<div class="chart-card">
	<h3 class="section-title" style="margin-bottom: 1.5rem;">
		<i class="fas fa-times-circle" style="color: var(--danger);"></i> 
		Brak w magazynie ({{ $outOfStock->count() }})
	</h3>

	<div class="inventory-table">
		<div class="table-header-row">
			<div>Produkt</div>
			<div>Kategoria</div>
			<div>Ostatnia sprzedaż</div>
			<div>Cena</div>
			<div>Akcje</div>
		</div>

		@foreach($outOfStock as $product)
		<div class="table-row">
			<div>
				<div style="font-weight: 600;">{{ $product->name }}</div>
				<div style="font-size: 0.875rem; color: var(--gray);">SKU: {{ $product->sku }}</div>
			</div>
			<div>
				<span class="badge badge-info">{{ $product->category->name }}</span>
			</div>
			<div style="color: var(--gray); font-size: 0.875rem;">
				@php
					$lastOutMovement = \App\Models\StockMovement::where('product_id', $product->id)
						->where('type', 'out')
						->whereNull('product_variant_id')
						->latest()
						->first();
				@endphp
				@if($lastOutMovement)
					{{ $lastOutMovement->created_at->format('d.m.Y') }}
				@else
					-
				@endif
			</div>
			<div style="font-weight: 700;">
				{{ number_format($product->price, 2) }} zł
			</div>
			<div>
				<a href="{{ route('admin.products.stock', $product) }}" class="btn btn-primary" style="padding: 0.5rem 1rem;">
					<i class="fas fa-plus"></i> Uzupełnij
				</a>
			</div>
		</div>
		@endforeach
	</div>
</div>
@endif

<!-- All Products by Category -->
<div class="chart-card" style="margin-top: 1.5rem;">
	<h3 class="section-title" style="margin-bottom: 1.5rem;">
		<i class="fas fa-list"></i> Stan według kategorii
	</h3>

	@foreach(\App\Models\Category::withCount('products')->with('products')->get() as $category)
	<div style="margin-bottom: 1.5rem;">
		<div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
			<h4 style="font-weight: 600;">{{ $category->name }}</h4>
			<span style="color: var(--gray);">{{ $category->products->count() }} produktów</span>
		</div>
        
		<div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem;">
			<div style="padding: 1rem; background: var(--light-gray); border-radius: 8px; text-align: center;">
				<div style="font-size: 1.5rem; font-weight: 700; color: var(--success);">
					{{ $category->products->sum('stock_quantity') }}
				</div>
				<div style="font-size: 0.75rem; color: var(--gray);">Jednostek</div>
			</div>
			<div style="padding: 1rem; background: var(--light-gray); border-radius: 8px; text-align: center;">
				<div style="font-size: 1.5rem; font-weight: 700; color: var(--primary);">
					{{ number_format($category->products->sum(function($p) { return $p->stock_quantity * $p->price; }), 0) }} zł
				</div>
				<div style="font-size: 0.75rem; color: var(--gray);">Wartość</div>
			</div>
			<div style="padding: 1rem; background: var(--light-gray); border-radius: 8px; text-align: center;">
				<div style="font-size: 1.5rem; font-weight: 700; color: var(--warning);">
					{{ $category->products->filter(fn($p) => $p->isLowStock())->count() }}
				</div>
				<div style="font-size: 0.75rem; color: var(--gray);">Niski stan</div>
			</div>
		</div>
	</div>
	@endforeach
</div>

@endsection
