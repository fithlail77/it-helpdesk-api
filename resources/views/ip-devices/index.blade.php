@extends('layouts.app')
@section('title', 'Manajemen IP Device')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div></div>
    <a href="{{ route('ip-devices.create') }}" class="btn btn-sm btn-success btn-modern"><i class="bi bi-plus-lg me-1"></i>Tambah</a>
</div>

<div class="card card-modern shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-modern datatables mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Perangkat</th>
                        <th>Merk</th>
                        <th>Spesifikasi</th>
                        <th>IP Address</th>
                        <th>Lokasi</th>
                        <th>Keterangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($devices as $d)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td class="fw-medium">{{ $d->name }}</td>
                        <td>{{ $d->brand }}</td>
                        <td><small class="text-muted">{{ $d->specifications ?: '-' }}</small></td>
                        <td><code class="bg-light px-2 py-1 rounded" style="font-size:0.82rem">{{ $d->ip_address }}</code></td>
                        <td>{{ $d->location }}</td>
                        <td><small class="text-muted">{{ $d->description ?: '-' }}</small></td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('ip-devices.edit', $d) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                <form method="POST" action="{{ route('ip-devices.destroy', $d) }}" onsubmit="return confirm('Hapus IP device ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
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
