{{-- resources/views/admin/reports/sales.blade.php --}}
@extends('layouts.admin')

@section('title', 'Raport sprzedaży')

@push('styles')
<style>
    .date-range-picker {
        background: white;
        padding: 1.5rem;
        border-radius: 12px;
        border: 1px solid var(--border);
        margin-bottom: 2rem;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        border-radius: 16px;
        border: 1px solid var(--border);
        padding: 1.5rem;
        position: relative;
        overflow: hidden;
        transition: all 0.3s;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: var(--primary);
    }

    .stat-card.primary::before { background: var(--primary); }
    .stat-card.success::before { background: var(--success); }
    .stat-card.warning::before { background: var(--warning); }
    .stat-card.danger::before { background: var(--danger); }

    .stat-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }

    .stat-label {
        font-size: 0.875rem;
        color: var(--gray);
        margin-bottom: 0.5rem;
        font-weight: 500;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 800;
        color: var(--dark);
    }

    .stat-icon {
        width: 56px;
        height: 56px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .stat-icon.primary {
        background: rgba(99, 102, 241, 0.1);
        color: var(--primary);
    }

    .stat-icon.success {
        background: rgba(16, 185, 129, 0.1);
        color: var(--success);
    }

    .stat-icon.warning {
        background: rgba(245, 158, 11, 0.1);
        color: var(--warning);
    }

    .chart-card {
        background: white;
        padding: 2rem;
        border-radius: 16px;
        border: 1px solid var(--border);
        margin-bottom: 1.5rem;
    }

    .chart-container {
        height: 400px;
        position: relative;
    }

    .top-products-list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .top-product-item {
        display: grid;
        grid-template-columns: auto auto 1fr auto auto;
        gap: 1rem;
        align-items: center;
        padding: 1rem;
        background: var(--light-gray);
        border-radius: 10px;
        transition: all 0.2s;
    }

    .top-product-item:hover {
        background: var(--border);
    }

    .product-rank {
        font-size: 1.5rem;
        font-weight: 800;
        width: 40px;
        text-align: center;
    }

    .product-rank.gold { color: #f59e0b; }
    .product-rank.silver { color: #94a3b8; }
    .product-rank.bronze { color: #c2410c; }

    .product-thumb {
        width: 50px;
        height: 50px;
        border-radius: 8px;
        background: white;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--gray);
    }

    .export-buttons {
        display: flex;
        gap: 0.5rem;
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
        <span>Sprzedaż</span>
    </div>
    <h1 class="page-title">Raport sprzedaży</h1>
    <p class="page-subtitle">Analiza przychodów i najlepiej sprzedających się produktów</p>
</div>

<!-- Date Range Picker -->
<form method="GET" class="date-range-picker">
    <div class="form-row">
        <div class="form-group">
            <label class="form-label">Data od</label>
            <input type="date" name="start_date" class="form-input" 
                   value="{{ request('start_date', now()->subMonth()->format('Y-m-d')) }}">
        </div>

        <div class="form-group">
            <label class="form-label">Data do</label>
            <input type="date" name="end_date" class="form-input" 
                   value="{{ request('end_date', now()->format('Y-m-d')) }}">
        </div>

        <div class="form-group">
            <label class="form-label">&nbsp;</label>
            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Generuj raport
                </button>
                <a href="{{ route('admin.reports.sales') }}" class="btn btn-secondary">
                    <i class="fas fa-redo"></i> Reset
                </a>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">&nbsp;</label>
            <div class="export-buttons">
                <button type="button" onclick="window.print()" class="btn btn-secondary">
                    <i class="fas fa-print"></i> Drukuj
                </button>
                <button type="button" class="btn btn-secondary">
                    <i class="fas fa-file-pdf"></i> PDF
                </button>
            </div>
        </div>
    </div>
</form>

<!-- Summary Stats -->
<div class="stats-grid">
    <div class="stat-card primary">
        <div class="stat-header">
            <div>
                <div class="stat-label">Całkowity przychód</div>
                <div class="stat-value">{{ number_format($stats['total_revenue'] ?? 0, 2) }} zł</div>
            </div>
            <div class="stat-icon primary">
                <i class="fas fa-money-bill-wave"></i>
            </div>
        </div>
    </div>

    <div class="stat-card success">
        <div class="stat-header">
            <div>
                <div class="stat-label">Liczba zamówień</div>
                <div class="stat-value">{{ $stats['total_orders'] ?? 0 }}</div>
            </div>
            <div class="stat-icon success">
                <i class="fas fa-shopping-cart"></i>
            </div>
        </div>
    </div>

    <div class="stat-card warning">
        <div class="stat-header">
            <div>
                <div class="stat-label">Średnia wartość zamówienia</div>
                <div class="stat-value">{{ number_format($stats['avg_order_value'] ?? 0, 2) }} zł</div>
            </div>
            <div class="stat-icon warning">
                <i class="fas fa-chart-line"></i>
            </div>
        </div>
    </div>
</div>

<!-- Sales Chart -->
<div class="chart-card">
    <h3 class="section-title" style="margin-bottom: 1.5rem;">
        <i class="fas fa-chart-area"></i> Sprzedaż w czasie
    </h3>
    <div class="chart-container">
        <canvas id="salesChart"></canvas>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
    <!-- Top Products -->
    <div class="chart-card">
        <h3 class="section-title" style="margin-bottom: 1.5rem;">
            <i class="fas fa-trophy"></i> Top 10 produktów
        </h3>

        <div class="top-products-list">
            @forelse($topProducts ?? [] as $index => $product)
            <div class="top-product-item">
                <div class="product-rank {{ $index == 0 ? 'gold' : ($index == 1 ? 'silver' : ($index == 2 ? 'bronze' : '')) }}">
                    #{{ $index + 1 }}
                </div>
                <div class="product-thumb">
                    <i class="fas fa-compact-disc"></i>
                </div>
                <div>
                    <div style="font-weight: 600;">{{ $product->product_name }}</div>
                    <div style="font-size: 0.875rem; color: var(--gray);">SKU: {{ $product->sku ?? 'N/A' }}</div>
                </div>
                <div style="text-align: right;">
                    <div style="font-weight: 700; font-size: 1.125rem;">{{ $product->total_sold }}</div>
                    <div style="font-size: 0.75rem; color: var(--gray);">sprzedanych</div>
                </div>
                <div style="text-align: right;">
                    <div style="font-weight: 700; font-size: 1.125rem; color: var(--success);">
                        {{ number_format($product->revenue, 2) }} zł
                    </div>
                    <div style="font-size: 0.75rem; color: var(--gray);">przychód</div>
                </div>
            </div>
            @empty
            <div style="text-align: center; padding: 3rem; color: var(--gray);">
                <i class="fas fa-chart-bar" style="font-size: 3rem; opacity: 0.3; margin-bottom: 1rem;"></i>
                <p>Brak danych sprzedażowych w wybranym okresie</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Revenue Breakdown -->
    <div class="chart-card">
        <h3 class="section-title" style="margin-bottom: 1.5rem;">
            <i class="fas fa-chart-pie"></i> Podział przychodów
        </h3>
        <div class="chart-container" style="height: 300px;">
            <canvas id="revenueChart"></canvas>
        </div>

        <div style="margin-top: 1.5rem; display: grid; gap: 0.75rem;">
            <div style="display: flex; justify-content: space-between; padding: 0.75rem; background: var(--light-gray); border-radius: 8px;">
                <span><i class="fas fa-circle" style="color: var(--primary);"></i> Płyty</span>
                <strong>{{ number_format(($stats['albums_revenue'] ?? 0), 2) }} zł</strong>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 0.75rem; background: var(--light-gray); border-radius: 8px;">
                <span><i class="fas fa-circle" style="color: var(--secondary);"></i> Merch</span>
                <strong>{{ number_format(($stats['merch_revenue'] ?? 0), 2) }} zł</strong>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // Sales Chart
    const salesData = {!! json_encode($dailySales ?? []) !!};
    
    // Parse data for chart
    const salesLabels = salesData.map(d => {
        const date = new Date(d.date);
        return date.toLocaleDateString('pl-PL', { month: 'short', day: 'numeric' });
    });
    const salesRevenue = salesData.map(d => parseFloat(d.revenue) || 0);
    const ordersCount = salesData.map(d => parseInt(d.orders) || 0);
    
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: salesLabels,
            datasets: [{
                label: 'Przychód (zł)',
                data: salesRevenue,
                borderColor: '#6366f1',
                backgroundColor: 'rgba(99, 102, 241, 0.1)',
                tension: 0.4,
                fill: true,
                yAxisID: 'y'
            }, {
                label: 'Zamówienia',
                data: ordersCount,
                borderColor: '#ec4899',
                backgroundColor: 'rgba(236, 72, 153, 0.1)',
                tension: 0.4,
                fill: true,
                yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Przychód (zł)'
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Liczba zamówień'
                    },
                    grid: {
                        drawOnChartArea: false
                    }
                }
            }
        }
    });

    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    new Chart(revenueCtx, {
        type: 'doughnut',
        data: {
            labels: ['Płyty', 'Merch'],
            datasets: [{
                data: [{{ $stats['albums_revenue'] ?? 0 }}, {{ $stats['merch_revenue'] ?? 0 }}],
                backgroundColor: ['#6366f1', '#ec4899'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
</script>
@endpush
@endsection
