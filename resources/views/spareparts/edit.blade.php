@extends('layouts.app')
@section('title', 'Edit Sparepart')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card card-modern shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0 fw-semibold">Form Edit Sparepart</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('spareparts.update', $sparepart) }}">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <label class="form-label fw-medium">Nama Part <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $sparepart->name) }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Stok <span class="text-danger">*</span></label>
                        <input type="number" name="stock" class="form-control @error('stock') is-invalid @enderror" value="{{ old('stock', $sparepart->stock) }}" min="0" required>
                        @error('stock') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Harga Satuan (Rp) <span class="text-danger">*</span></label>
                        <input type="number" name="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price', $sparepart->price) }}" min="0" required>
                        @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-modern"><i class="bi bi-check-lg me-1"></i>Update</button>
                        <a href="{{ route('spareparts.index') }}" class="btn btn-outline-secondary btn-modern">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
