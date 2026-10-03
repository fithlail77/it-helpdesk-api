@extends('layouts.app')
@section('title', 'Semua Tiket')

@section('content')
<div class="card card-modern shadow-sm mb-3">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('activities.index') }}" id="filterForm">
            <div class="row g-2 align-items-end">
                <div class="col-sm-6 col-md-3">
                    <label class="form-label mb-1" style="font-size:0.8rem;font-weight:600;color:#64748b">DARI TANGGAL</label>
                    <input type="date" name="date_from" class="form-control form-control-sm" value="{{ $dateFrom }}">
                </div>
                <div class="col-sm-6 col-md-3">
                    <label class="form-label mb-1" style="font-size:0.8rem;font-weight:600;color:#64748b">SAMPAI TANGGAL</label>
                    <input type="date" name="date_to" class="form-control form-control-sm" value="{{ $dateTo }}">
                </div>
                <div class="col-sm-6 col-md-2">
                    <label class="form-label mb-1" style="font-size:0.8rem;font-weight:600;color:#64748b">STATUS</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">Semua</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Tertunda</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>Diproses</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </div>
                <div class="col-sm-6 col-md-2">
                    <label class="form-label mb-1" style="font-size:0.8rem;font-weight:600;color:#64748b">KATEGORI</label>
                    <select name="category" class="form-select form-select-sm">
                        <option value="">Semua</option>
                        <option value="hardware" {{ request('category') == 'hardware' ? 'selected' : '' }}>Hardware</option>
                        <option value="software" {{ request('category') == 'software' ? 'selected' : '' }}>Software</option>
                        <option value="network" {{ request('category') == 'network' ? 'selected' : '' }}>Network</option>
                        <option value="other" {{ request('category') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div class="col-sm-12 col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-fill">
                        <i class="bi bi-funnel me-1"></i>Filter
                    </button>
                    <a href="{{ route('activities.index') }}" class="btn btn-outline-secondary btn-sm" title="Reset">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card card-modern shadow-sm">
    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h6 class="mb-0 fw-semibold">
            <i class="bi bi-clipboard-check me-2"></i>Daftar Tiket
            <span class="text-muted fw-normal" style="font-size:0.8rem">
                ({{ \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') }} – {{ \Carbon\Carbon::parse($dateTo)->format('d/m/Y') }})
            </span>
        </h6>
        <div class="d-flex gap-2">
            <a href="{{ route('activities.export', request()->query()) }}" class="btn btn-success btn-modern">
                <i class="bi bi-file-earmark-excel me-1"></i>Export XLSX
            </a>
            <a href="{{ route('activities.create') }}" class="btn btn-primary btn-modern">
                <i class="bi bi-plus-lg me-1"></i>Buat Tiket
            </a>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-modern datatables mb-0">
                <thead>
                    <tr>
                        <th>Tiket</th>
                        <th>Judul</th>
                        <th>Kategori</th>
                        <th>Sub Kategori</th>
                        <th>Status</th>
                        <th>Prioritas</th>
                        <th>Dibuat</th>
                        <th class="text-center" style="width:100px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($activities as $act)
                    <tr>
                        <td><a href="{{ route('activities.show', $act) }}" class="text-primary fw-medium text-decoration-none">{{ $act->ticket_number }}</a></td>
                        <td>
                            <div>{{ Str::limit($act->title, 35) }}</div>
                            <small class="text-muted">{{ $act->reporter_name }}{{ $act->department ? ' • '.$act->department : '' }}</small>
                        </td>
                        <td><span class="badge bg-primary bg-opacity-10 text-primary badge-status">{{ ucfirst($act->category) }}</span></td>
                        <td>{{ $act->sub_category ?? '-' }}</td>
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
                        <td class="text-muted" style="font-size:0.82rem" data-order="{{ $act->created_at->format('Y-m-d H:i') }}">{{ $act->created_at->format('d/m/Y H:i') }}</td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('activities.show', $act) }}" class="btn btn-outline-info" title="Detail"><i class="bi bi-eye"></i></a>
                                <a href="{{ route('activities.edit', $act) }}" class="btn btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                                <form method="POST" action="{{ route('activities.destroy', $act) }}" class="d-inline" onsubmit="return confirm('Yakin hapus tiket ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Hapus"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('.datatables').DataTable({
        order: [[6, 'desc']],
        pageLength: 25,
        lengthChange: false,
        language: {
            search: "Cari:",
            info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
            infoEmpty: "Tidak ada data",
            infoFiltered: "(disaring dari _MAX_ total data)",
            zeroRecords: "Tidak ada data yang cocok",
            paginate: { previous: "&laquo;", next: "&raquo;" }
        }
    });
});
</script>
@endpush
