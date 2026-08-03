@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-clipboard-data"></i></div>
                <div><div class="stat-value text-primary">{{ $summary['all_total'] }}</div><div class="stat-label">Total Semua Tiket</div></div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-warning bg-opacity-10 text-warning"><i class="bi bi-hourglass-split"></i></div>
                <div><div class="stat-value text-warning">{{ $summary['all_pending'] }}</div><div class="stat-label">Tertunda</div></div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-info bg-opacity-10 text-info"><i class="bi bi-arrow-repeat"></i></div>
                <div><div class="stat-value text-info">{{ $summary['all_in_progress'] }}</div><div class="stat-label">Diproses</div></div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-success bg-opacity-10 text-success"><i class="bi bi-check-circle"></i></div>
                <div><div class="stat-value text-success">{{ $summary['all_completed'] }}</div><div class="stat-label">Selesai</div></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-8">
        <div class="card card-modern shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-bar-chart me-2"></i>Aktivitas 7 Hari Terakhir</h6>
            </div>
            <div class="card-body">
                <canvas id="weeklyChart" height="260"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card card-modern shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-people me-2"></i>Statistik Tim</h6>
            </div>
            <div class="card-body p-0">
                @forelse($teamStats as $team)
                <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
                    <div>
                        <div class="fw-medium" style="font-size:0.9rem">{{ $team->name }}</div>
                        <small class="text-muted">{{ $team->completed_activities }}/{{ $team->total_activities }} selesai</small>
                    </div>
                    <div class="text-end">
                        <span class="fw-bold" style="font-size:0.95rem;color:{{ $team->completion_ratio >= 70 ? '#22c55e' : ($team->completion_ratio >= 40 ? '#f59e0b' : '#ef4444') }}">{{ $team->completion_ratio }}%</span>
                        <div class="progress mt-1" style="width:80px;height:5px">
                            <div class="progress-bar" style="width:{{ $team->completion_ratio }}%;background:{{ $team->completion_ratio >= 70 ? '#22c55e' : ($team->completion_ratio >= 40 ? '#f59e0b' : '#ef4444') }}"></div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="p-3 text-center text-muted">Belum ada data tim</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<div class="card card-modern shadow-sm">
    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-semibold"><i class="bi bi-clock-history me-2"></i>Tiket Terbaru</h6>
        <a href="{{ route('activities.index') }}" class="btn btn-sm btn-outline-primary btn-modern">Lihat Semua</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-modern mb-0">
                <thead>
                    <tr>
                        <th>Tiket</th>
                        <th>Judul</th>
                        <th>Kategori</th>
                        <th>Status</th>
                        <th>Prioritas</th>
                        <th>Dibuat</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentActivities as $act)
                    <tr>
                        <td><a href="{{ route('activities.show', $act) }}" class="text-primary fw-medium text-decoration-none">{{ $act->ticket_number }}</a></td>
                        <td>{{ Str::limit($act->title, 40) }}</td>
                        <td><span class="badge bg-primary bg-opacity-10 text-primary badge-status">{{ ucfirst($act->category) }}</span></td>
                        <td>
                            @php
                                $sc = match($act->status) { 'completed' => 'success', 'in_progress' => 'info', 'pending' => 'warning', default => 'danger' };
                                $sl = match($act->status) { 'completed' => 'Selesai', 'in_progress' => 'Diproses', 'pending' => 'Tertunda', default => 'Dibatalkan' };
                            @endphp
                            <span class="badge bg-{{ $sc }} bg-opacity-10 text-{{ $sc }} badge-status">{{ $sl }}</span>
                        </td>
                        <td>
                            @php
                                $pc = match($act->priority) { 'urgent' => 'danger', 'high' => 'warning', 'medium' => 'primary', default => 'secondary' };
                                $pl = match($act->priority) { 'urgent' => 'Mendesak', 'high' => 'Tinggi', 'medium' => 'Sedang', default => 'Rendah' };
                            @endphp
                            <span class="badge bg-{{ $pc }} bg-opacity-10 text-{{ $pc }} badge-status">{{ $pl }}</span>
                        </td>
                        <td class="text-muted" style="font-size:0.82rem">{{ $act->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">Belum ada tiket</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
const weekly = @json($weekly);
const ctx = document.getElementById('weeklyChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: weekly.map(d => { const dt = new Date(d.date + 'T00:00:00'); return dt.toLocaleDateString('id-ID', {weekday:'short', day:'numeric', month:'short'}); }),
        datasets: [
            { label: 'Selesai', data: weekly.map(d => d.completed), backgroundColor: '#22c55e', borderRadius: 4 },
            { label: 'Diproses', data: weekly.map(d => d.in_progress), backgroundColor: '#8b5cf6', borderRadius: 4 },
            { label: 'Tertunda', data: weekly.map(d => d.pending), backgroundColor: '#f59e0b', borderRadius: 4 },
        ]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, padding: 15, font: { size: 12 } } } },
        scales: { x: { grid: { display: false } }, y: { beginAtZero: true, ticks: { stepSize: 1 } } }
    }
});
</script>
@endpush
