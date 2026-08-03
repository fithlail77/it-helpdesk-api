@extends('layouts.app')
@section('title', 'Edit User')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card card-modern shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-pencil me-2"></i>Edit User: {{ $user->name }}</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('users.update', $user) }}">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <label class="form-label fw-medium">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Password Baru <small class="text-muted">(kosongkan jika tidak diubah)</small></label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="form-control">
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Role <span class="text-danger">*</span></label>
                            <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                                <option value="technician" {{ old('role', $user->role)==='technician' ? 'selected' : '' }}>Teknisi</option>
                                <option value="admin" {{ old('role', $user->role)==='admin' ? 'selected' : '' }}>Admin</option>
                            </select>
                            @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Tim</label>
                            <select name="team_id" class="form-select">
                                <option value="">-- Pilih Tim --</option>
                                @foreach($teams as $team)
                                    <option value="{{ $team->id }}" {{ old('team_id', $user->team_id)==$team->id ? 'selected' : '' }}>{{ $team->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-medium">No. Telepon</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-modern"><i class="bi bi-check-lg me-1"></i>Update</button>
                        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary btn-modern">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
