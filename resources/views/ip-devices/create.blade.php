@extends('layouts.app')
@section('title', 'Tambah IP Device')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card card-modern shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0 fw-semibold">Form Tambah IP Device</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('ip-devices.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-medium">Nama Perangkat <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="contoh: Server Utama" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Merk <span class="text-danger">*</span></label>
                        <input type="text" name="brand" class="form-control @error('brand') is-invalid @enderror" value="{{ old('brand') }}" placeholder="contoh: Dell, HP, Cisco" required>
                        @error('brand') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Spesifikasi</label>
                        <textarea name="specifications" class="form-control @error('specifications') is-invalid @enderror" rows="2" placeholder="contoh: RAM 16GB, CPU Intel i7, SSD 512GB">{{ old('specifications') }}</textarea>
                        @error('specifications') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">IP Address <span class="text-danger">*</span></label>
                        <input type="text" name="ip_address" class="form-control @error('ip_address') is-invalid @enderror" value="{{ old('ip_address') }}" placeholder="contoh: 192.168.1.10" required>
                        @error('ip_address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Lokasi <span class="text-danger">*</span></label>
                        <input type="text" name="location" class="form-control @error('location') is-invalid @enderror" value="{{ old('location') }}" placeholder="contoh: Server Room Lt.3, Kantor Pusat" required>
                        @error('location') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-modern"><i class="bi bi-check-lg me-1"></i>Simpan</button>
                        <a href="{{ route('ip-devices.index') }}" class="btn btn-outline-secondary btn-modern">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
