@extends('layouts.app')
@section('title', 'Manajemen Sparepart')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div></div>
    <a href="{{ route('spareparts.create') }}" class="btn btn-sm btn-success btn-modern"><i class="bi bi-plus-lg me-1"></i>Tambah</a>
</div>

<div class="card card-modern shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-modern datatables mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Part</th>
                        <th>Stok</th>
                        <th>Harga Satuan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($spareparts as $sp)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td class="fw-medium">{{ $sp->name }}</td>
                        <td>
                            @if($sp->stock <= 0)
                                <span class="badge bg-danger badge-status">Habis</span>
                            @elseif($sp->stock <= 5)
                                <span class="badge bg-warning text-dark badge-status">Sisa {{ $sp->stock }}</span>
                            @else
                                <span class="badge bg-success badge-status">{{ $sp->stock }}</span>
                            @endif
                        </td>
                        <td class="fw-medium">Rp {{ number_format($sp->price, 0, ',', '.') }}</td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('spareparts.edit', $sp) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                <form method="POST" action="{{ route('spareparts.destroy', $sp) }}" onsubmit="return confirm('Hapus sparepart ini?')">
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
