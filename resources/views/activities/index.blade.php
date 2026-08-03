@extends('layouts.app')
@section('title', 'Semua Tiket')

@section('content')
<div class="card card-modern shadow-sm">
    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h6 class="mb-0 fw-semibold"><i class="bi bi-clipboard-check me-2"></i>Daftar Tiket</h6>
        <a href="{{ route('activities.create') }}" class="btn btn-primary btn-modern"><i class="bi bi-plus-lg me-1"></i>Buat Tiket</a>
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
                        <td class="text-muted" style="font-size:0.82rem">{{ $act->created_at->format('d/m/Y H:i') }}</td>
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
