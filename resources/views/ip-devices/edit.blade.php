@extends('layouts.app')
@section('title', 'Edit IP Device')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card card-modern shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0 fw-semibold">Form Edit IP Device</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('ip-devices.update', $device) }}">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <label class="form-label fw-medium">Nama Perangkat <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $device->name) }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Merk <span class="text-danger">*</span></label>
                        <input type="text" name="brand" class="form-control @error('brand') is-invalid @enderror" value="{{ old('brand', $device->brand) }}" required>
                        @error('brand') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Spesifikasi</label>
                        <textarea name="specifications" class="form-control @error('specifications') is-invalid @enderror" rows="2">{{ old('specifications', $device->specifications) }}</textarea>
                        @error('specifications') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">IP Address <span class="text-danger">*</span></label>
                        <input type="text" name="ip_address" class="form-control @error('ip_address') is-invalid @enderror" value="{{ old('ip_address', $device->ip_address) }}" required>
                        @error('ip_address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Lokasi <span class="text-danger">*</span></label>
                        <input type="text" name="location" class="form-control @error('location') is-invalid @enderror" value="{{ old('location', $device->location) }}" required>
                        @error('location') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Keterangan</label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="2">{{ old('description', $device->description) }}</textarea>
                        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-modern"><i class="bi bi-check-lg me-1"></i>Update</button>
                        <a href="{{ route('ip-devices.index') }}" class="btn btn-outline-secondary btn-modern">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
