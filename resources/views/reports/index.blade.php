@extends('layouts.app')
@section('title', 'Dashboard Laporan')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js" rel="stylesheet">
<style>
    .chart-container { position: relative; height: 300px; }
    .ai-analysis { background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border: 1px solid #fcd34d; border-radius: 12px; padding: 1rem; }
    .ai-analysis .icon { width: 40px; height: 40px; background: #f59e0b; border-radius: 10px; display: flex; align-items: center; justify-content: center; }
    .stat-card { border: none; border-radius: 12px; transition: transform 0.2s; }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 15px rgba(0,0,0,0.08); }
    .stat-card .stat-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; }
    .stat-card .stat-value { font-size: 1.5rem; font-weight: 700; }
    .stat-card .stat-label { font-size: 0.75rem; color: #64748b; }
    .nav-pills .nav-link { border-radius: 8px; font-weight: 500; }
    .nav-pills .nav-link.active { background: #3b82f6; color: #fff; }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h1 class="h4 mb-0"><i class="bi bi-graph-up-arrow me-2 text-primary"></i>Dashboard Laporan</h1>
    <form method="GET" action="{{ route('reports.index') }}" class="d-flex flex-wrap gap-2 align-items-center">
        <input type="hidden" name="type" value="{{ $reportType }}">
        <div class="input-group input-group-sm" style="width:auto">
            <span class="input-group-text bg-white border-end-0 text-muted" style="font-size:0.8rem">Dari</span>
            <input type="date" name="date_from" class="form-control form-control-sm" value="{{ $dateFrom }}" style="max-width:140px">
        </div>
        <div class="input-group input-group-sm" style="width:auto">
            <span class="input-group-text bg-white border-end-0 text-muted" style="font-size:0.8rem">Sampai</span>
            <input type="date" name="date_to" class="form-control form-control-sm" value="{{ $dateTo }}" style="max-width:140px">
        </div>
        <button type="submit" class="btn btn-sm btn-primary btn-modern"><i class="bi bi-funnel me-1"></i>Filter</button>
    </form>
</div>

<ul class="nav nav-pills mb-4" role="tablist">
    <li class="nav-item">
        <a class="nav-link {{ $reportType === 'tickets' ? 'active' : '' }}" href="{{ route('reports.index', ['type' => 'tickets'] + request()->except('type')) }}">
            <i class="bi bi-ticket-perforated me-1"></i> Analisa Tiket Helpdesk
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $reportType === 'cost_overhead' ? 'active' : '' }}" href="{{ route('reports.index', ['type' => 'cost_overhead'] + request()->except('type')) }}">
            <i class="bi bi-cash-stack me-1"></i> Analisa Cost Overhead
        </a>
    </li>
</ul>

<div class="row g-3 mb-4">
    @if($reportType === 'tickets')
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card shadow-sm" style="background:#eff6ff; border-left:4px solid #3b82f6">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon" style="background:#dbeafe"><i class="bi bi-ticket-perforated text-primary"></i></div>
                        <div class="ms-3">
                            <div class="stat-value text-dark">{{ $data['summary']['total_tickets'] }}</div>
                            <div class="stat-label">Total Tiket</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card shadow-sm" style="background:#f0fdf4; border-left:4px solid #22c55e">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon" style="background:#dcfce7"><i class="bi bi-check-circle text-success"></i></div>
                        <div class="ms-3">
                            <div class="stat-value text-dark">{{ $data['summary']['completed'] }}</div>
                            <div class="stat-label">Selesai</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card shadow-sm" style="background:#fffbeb; border-left:4px solid #f59e0b">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon" style="background:#fef3c7"><i class="bi bi-clock-history text-warning"></i></div>
                        <div class="ms-3">
                            <div class="stat-value text-dark">{{ $data['summary']['avg_resolution_days'] }} hari</div>
                            <div class="stat-label">Avg Resolusi</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card shadow-sm" style="background:#f0f9ff; border-left:4px solid #06b6d4">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon" style="background:#e0f2fe"><i class="bi bi-currency-dollar text-info"></i></div>
                        <div class="ms-3">
                            <div class="stat-value text-dark">Rp {{ number_format($data['summary']['total_sparepart_cost'], 0, ',', '.') }}</div>
                            <div class="stat-label">Total Biaya Sparepart</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card shadow-sm" style="background:#eff6ff; border-left:4px solid #3b82f6">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon" style="background:#dbeafe"><i class="bi bi-journal-text text-primary"></i></div>
                        <div class="ms-3">
                            <div class="stat-value text-dark">{{ $data['summary']['total_records'] }}</div>
                            <div class="stat-label">Total Records</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card shadow-sm" style="background:#f0fdf4; border-left:4px solid #22c55e">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon" style="background:#dcfce7"><i class="bi bi-cash-stack text-success"></i></div>
                        <div class="ms-3">
                            <div class="stat-value text-dark">Rp {{ number_format($data['summary']['total_amount'], 0, ',', '.') }}</div>
                            <div class="stat-label">Total Amount</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card shadow-sm" style="background:#fffbeb; border-left:4px solid #f59e0b">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon" style="background:#fef3c7"><i class="bi bi-calculator text-warning"></i></div>
                        <div class="ms-3">
                            <div class="stat-value text-dark">Rp {{ number_format($data['summary']['avg_amount'], 0, ',', '.') }}</div>
                            <div class="stat-label">Avg per Record</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card shadow-sm" style="background:#f0f9ff; border-left:4px solid #06b6d4">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon" style="background:#e0f2fe"><i class="bi bi-graph-up text-info"></i></div>
                        <div class="ms-3">
                            <div class="stat-value text-dark">{{ count($data['monthly_trend']) }}</div>
                            <div class="stat-label">Bulan Aktif</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<div class="row g-3 mb-4">
    <div class="col-12">
        <div class="card card-modern shadow-sm">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-robot me-2 text-warning"></i>Analisis AI</h6>
                <span class="badge bg-warning text-dark" style="font-size:0.7rem">Dipersiapkan otomatis</span>
            </div>
            <div class="card-body">
                <div class="ai-analysis">
                    <div class="d-flex gap-3">
                        <div class="icon"><i class="bi bi-cpu text-white" style="font-size:1.2rem"></i></div>
                        <div class="flex-grow-1">
                            <h6 class="fw-semibold text-dark mb-2">Insight & Rekomendasi</h6>
                            <div class="text-dark" style="font-size:0.9rem; line-height:1.6; white-space:pre-line">{{ $aiAnalysis }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    @if($reportType === 'tickets')
        <div class="col-lg-6">
            <div class="card card-modern shadow-sm h-100">
                <div class="card-header bg-white border-bottom"><h6 class="mb-0 fw-semibold">Status Tiket</h6></div>
                <div class="card-body"><div class="chart-container"><canvas id="chartStatus"></canvas></div></div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card card-modern shadow-sm h-100">
                <div class="card-header bg-white border-bottom"><h6 class="mb-0 fw-semibold">Prioritas Tiket</h6></div>
                <div class="card-body"><div class="chart-container"><canvas id="chartPriority"></canvas></div></div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card card-modern shadow-sm h-100">
                <div class="card-header bg-white border-bottom"><h6 class="mb-0 fw-semibold">Kategori Tiket</h6></div>
                <div class="card-body"><div class="chart-container"><canvas id="chartCategory"></canvas></div></div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card card-modern shadow-sm h-100">
                <div class="card-header bg-white border-bottom"><h6 class="mb-0 fw-semibold">Tiket per Tim</h6></div>
                <div class="card-body"><div class="chart-container"><canvas id="chartTeam"></canvas></div></div>
            </div>
        </div>
    @else
        <div class="col-lg-6">
            <div class="card card-modern shadow-sm h-100">
                <div class="card-header bg-white border-bottom"><h6 class="mb-0 fw-semibold">Trend Bulanan</h6></div>
                <div class="card-body"><div class="chart-container"><canvas id="chartMonthly"></canvas></div></div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card card-modern shadow-sm h-100">
                <div class="card-header bg-white border-bottom"><h6 class="mb-0 fw-semibold">Top GL Account</h6></div>
                <div class="card-body"><div class="chart-container"><canvas id="chartGlAccount"></canvas></div></div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card card-modern shadow-sm h-100">
                <div class="card-header bg-white border-bottom"><h6 class="mb-0 fw-semibold">Profit Center</h6></div>
                <div class="card-body"><div class="chart-container"><canvas id="chartProfitCenter"></canvas></div></div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card card-modern shadow-sm h-100">
                <div class="card-header bg-white border-bottom"><h6 class="mb-0 fw-semibold">Departemen</h6></div>
                <div class="card-body"><div class="chart-container"><canvas id="chartDepartemen"></canvas></div></div>
            </div>
        </div>
    @endif
</div>

@if($reportType === 'tickets')
<div class="row g-3 mt-3">
    <div class="col-12">
        <div class="card card-modern shadow-sm">
            <div class="card-header bg-white border-bottom"><h6 class="mb-0 fw-semibold">Top 5 Issues</h6></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-modern mb-0">
                        <thead><tr><th>#</th><th>Issue</th><th>Jumlah</th></tr></thead>
                        <tbody>
                            @foreach($data['top_issues'] as $issue => $count)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $issue }}</td>
                                <td>{{ $count }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js"></script>
<script>
// Wait for Chart.js and plugin to load
function initCharts() {
    if (typeof Chart === 'undefined') {
        console.error('Chart.js not loaded');
        return;
    }
    // Register datalabels plugin
    if (typeof ChartDataLabels !== 'undefined') {
        Chart.register(ChartDataLabels);
    } else {
        console.warn('chartjs-plugin-datalabels not loaded');
    }

    const chartData = @json($chartData);
    console.log('Chart data:', chartData);

    const colors = ['#3b82f6','#22c55e','#f59e0b','#ef4444','#8b5cf6','#ec4899','#06b6d4','#f97316','#84cc16','#6366f1'];

    function safeCreate(fn, ctxId, data, label) {
        const ctx = document.getElementById(ctxId);
        if (!ctx) {
            console.error('Canvas not found:', ctxId);
            return;
        }
        if (!data || !data.labels || !data.labels.length) {
            console.warn('No data for chart:', ctxId, data);
            ctx.parentElement.innerHTML = '<div class="text-center text-muted py-4">Tidak ada data</div>';
            return;
        }
        try {
            return fn(ctx, data, label);
        } catch (e) {
            console.error('Chart error:', ctxId, e);
            ctx.parentElement.innerHTML = '<div class="text-center text-danger py-4">Error: ' + e.message + '</div>';
        }
    }

    function createDoughnut(ctx, data, label) {
        return new Chart(ctx, {
            type: 'doughnut',
            data: { labels: data.labels, datasets: [{ data: data.data, backgroundColor: data.colors || colors, borderWidth: 0, label }] },
            options: { 
                responsive: true, 
                maintainAspectRatio: false, 
                plugins: { 
                    legend: { position: 'bottom', labels: { font: { size: 11 }, padding: 15 } },
                    title: { display: true, text: label, font: { size: 14, weight: 'bold' }, padding: { bottom: 10 } },
                    datalabels: { display: true, color: '#1e293b', font: { size: 11, weight: 'bold' }, formatter: (value) => value }
                } 
            }, 
            cutout: '60%' 
        });
    }

    function createBar(ctx, data, label) {
        return new Chart(ctx, {
            type: 'bar',
            data: { labels: data.labels, datasets: [{ label, data: data.data, backgroundColor: '#3b82f6', borderRadius: 4, maxBarThickness: 40 }] },
            options: { 
                responsive: true, 
                maintainAspectRatio: false, 
                plugins: { 
                    legend: { display: false },
                    title: { display: true, text: label, font: { size: 14, weight: 'bold' }, padding: { bottom: 10 } },
                    datalabels: { display: true, color: '#1e293b', font: { size: 11, weight: 'bold' }, anchor: 'end', align: 'top', formatter: (value) => value.toLocaleString() }
                }, 
                scales: { y: { beginAtZero: true, ticks: { font: { size: 11 } } }, x: { ticks: { font: { size: 11 } } } } 
            }
        });
    }

    function createLine(ctx, data, label) {
        return new Chart(ctx, {
            type: 'line',
            data: { labels: data.labels, datasets: [{ label, data: data.data, borderColor: '#3b82f6', backgroundColor: 'rgba(59,130,246,0.1)', fill: true, tension: 0.3, pointRadius: 4, pointBackgroundColor: '#3b82f6' }] },
            options: { 
                responsive: true, 
                maintainAspectRatio: false, 
                plugins: { 
                    legend: { display: false },
                    title: { display: true, text: label, font: { size: 14, weight: 'bold' }, padding: { bottom: 10 } },
                    datalabels: { display: true, color: '#1e293b', font: { size: 11, weight: 'bold' }, formatter: (value) => value.toLocaleString() }
                }, 
                scales: { y: { beginAtZero: true, ticks: { font: { size: 11 }, callback: (value) => value.toLocaleString() } }, x: { ticks: { font: { size: 11 } } } } 
            }
        });
    }

    @if($reportType === 'tickets')
    safeCreate(createDoughnut, 'chartStatus', chartData.status, 'Status Tiket');
    safeCreate(createDoughnut, 'chartPriority', chartData.priority, 'Prioritas Tiket');
    safeCreate(createDoughnut, 'chartCategory', chartData.category, 'Kategori Tiket');
    safeCreate(createBar, 'chartTeam', chartData.team, 'Tiket per Tim');
    @else
    safeCreate(createLine, 'chartMonthly', chartData.monthly, 'Trend Bulanan');
    safeCreate(createBar, 'chartGlAccount', chartData.gl_account, 'Top GL Account');
    safeCreate(createBar, 'chartProfitCenter', chartData.profit_center, 'Profit Center');
    safeCreate(createBar, 'chartDepartemen', chartData.departemen, 'Departemen');
    @endif
}

// Execute when DOM ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initCharts);
} else {
    initCharts();
}
</script>
@endpush