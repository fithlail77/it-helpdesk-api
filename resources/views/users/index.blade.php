@extends('layouts.app')
@section('title', 'Manajemen User')

@section('content')
<div class="card card-modern shadow-sm">
    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h6 class="mb-0 fw-semibold"><i class="bi bi-people me-2"></i>Daftar User</h6>
        <a href="{{ route('users.create') }}" class="btn btn-primary btn-modern"><i class="bi bi-plus-lg me-1"></i>Tambah User</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-modern datatables mb-0">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Tim</th>
                        <th>Status</th>
                        <th class="text-center" style="width:180px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center" style="width:32px;height:32px;font-size:0.8rem;font-weight:600">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <span class="fw-medium">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td class="text-muted">{{ $user->email }}</td>
                        <td><span class="badge {{ $user->role==='admin' ? 'bg-danger' : 'bg-info' }} bg-opacity-10 text-{{ $user->role==='admin' ? 'danger' : 'info' }} badge-status">{{ ucfirst($user->role) }}</span></td>
                        <td>{{ $user->team?->name ?? '-' }}</td>
                        <td>
                            @if($user->is_active)
                                <span class="badge bg-success bg-opacity-10 text-success badge-status"><i class="bi bi-check-circle me-1"></i>Aktif</span>
                            @else
                                <span class="badge bg-secondary bg-opacity-10 text-secondary badge-status"><i class="bi bi-x-circle me-1"></i>Nonaktif</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('users.edit', $user) }}" class="btn btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                                <form method="POST" action="{{ route('users.toggle-status', $user) }}" class="d-inline">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-outline-{{ $user->is_active ? 'warning' : 'success' }}" title="{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                        <i class="bi bi-{{ $user->is_active ? 'pause-circle' : 'play-circle' }}"></i>
                                    </button>
                                </form>
                                @if($user->id !== session('web_user.id'))
                                <form method="POST" action="{{ route('users.destroy', $user) }}" class="d-inline" onsubmit="return confirm('Yakin hapus user ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Hapus"><i class="bi bi-trash"></i></button>
                                </form>
                                @endif
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
        pageLength: 10,
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
