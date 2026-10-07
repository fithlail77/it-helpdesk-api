@extends('layouts.app')
@section('title', 'Tambah Cost Overhead')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card card-modern shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-plus-circle me-2 text-primary"></i>Tambah Data Cost Overhead</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('cost-overheads.store') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium" style="font-size:0.875rem">GL Account <span class="text-danger">*</span></label>
                            <input type="number" name="gl_account" class="form-control form-control-sm @error('gl_account') is-invalid @enderror"
                                   value="{{ old('gl_account') }}" required>
                            @error('gl_account')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium" style="font-size:0.875rem">GL Name <span class="text-danger">*</span></label>
                            <input type="text" name="gl_name" class="form-control form-control-sm @error('gl_name') is-invalid @enderror"
                                   value="{{ old('gl_name') }}" required>
                            @error('gl_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium" style="font-size:0.875rem">Posting Date <span class="text-danger">*</span></label>
                            <input type="date" name="posting_date" class="form-control form-control-sm @error('posting_date') is-invalid @enderror"
                                   value="{{ old('posting_date') }}" required>
                            @error('posting_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium" style="font-size:0.875rem">Amount <span class="text-danger">*</span></label>
                            <input type="number" name="amount" class="form-control form-control-sm @error('amount') is-invalid @enderror"
                                   value="{{ old('amount') }}" required>
                            @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium" style="font-size:0.875rem">Text</label>
                            <textarea name="text" rows="2" class="form-control form-control-sm @error('text') is-invalid @enderror">{{ old('text') }}</textarea>
                            @error('text')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium" style="font-size:0.875rem">Document Header</label>
                            <textarea name="document_header" rows="2" class="form-control form-control-sm @error('document_header') is-invalid @enderror">{{ old('document_header') }}</textarea>
                            @error('document_header')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium" style="font-size:0.875rem">Profit Center</label>
                            <input type="text" name="profit_center" class="form-control form-control-sm @error('profit_center') is-invalid @enderror"
                                   value="{{ old('profit_center') }}">
                            @error('profit_center')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium" style="font-size:0.875rem">Company Code</label>
                            <input type="text" name="company_code" class="form-control form-control-sm @error('company_code') is-invalid @enderror"
                                   value="{{ old('company_code') }}" maxlength="10">
                            @error('company_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium" style="font-size:0.875rem">Departemen</label>
                            <input type="text" name="departemen" class="form-control form-control-sm @error('departemen') is-invalid @enderror"
                                   value="{{ old('departemen') }}">
                            @error('departemen')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium" style="font-size:0.875rem">User</label>
                            <input type="text" name="user" class="form-control form-control-sm @error('user') is-invalid @enderror"
                                   value="{{ old('user') }}">
                            @error('user')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium" style="font-size:0.875rem">Activity</label>
                            <input type="text" name="activity" class="form-control form-control-sm @error('activity') is-invalid @enderror"
                                   value="{{ old('activity') }}">
                            @error('activity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-sm btn-primary btn-modern">
                            <i class="bi bi-save me-1"></i>Simpan
                        </button>
                        <a href="{{ route('cost-overheads.index') }}" class="btn btn-sm btn-outline-secondary btn-modern">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
