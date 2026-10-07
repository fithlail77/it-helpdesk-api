@extends('layouts.app')
@section('title', 'Cost Overhead')

@section('content')

<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <form method="GET" action="{{ route('cost-overheads.index') }}" class="d-flex flex-wrap gap-2 align-items-center">
        <div class="input-group input-group-sm" style="width:auto">
            <span class="input-group-text bg-white border-end-0 text-muted" style="font-size:0.8rem">Dari</span>
            <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}" style="max-width:150px">
        </div>
        <div class="input-group input-group-sm" style="width:auto">
            <span class="input-group-text bg-white border-end-0 text-muted" style="font-size:0.8rem">Sampai</span>
            <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}" style="max-width:150px">
        </div>
        <button type="submit" class="btn btn-sm btn-primary btn-modern"><i class="bi bi-funnel me-1"></i>Filter</button>
        @if(request('date_from') || request('date_to'))
            <a href="{{ route('cost-overheads.index') }}" class="btn btn-sm btn-outline-secondary btn-modern">Reset</a>
        @endif
    </form>

    <div class="d-flex gap-2">
        <a href="{{ route('cost-overheads.export', request()->only('date_from','date_to')) }}"
           class="btn btn-sm btn-outline-success btn-modern">
            <i class="bi bi-file-earmark-excel me-1"></i>Export
        </a>
        <button type="button" class="btn btn-sm btn-outline-secondary btn-modern" data-bs-toggle="modal" data-bs-target="#modalUpload">
            <i class="bi bi-upload me-1"></i>Upload Excel
        </button>
        <a href="{{ route('cost-overheads.create') }}" class="btn btn-sm btn-success btn-modern">
            <i class="bi bi-plus-lg me-1"></i>Tambah
        </a>
    </div>
</div>

<div class="card card-modern shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-modern mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>GL Account</th>
                        <th>GL Name</th>
                        <th>Posting Date</th>
                        <th>Amount</th>
                        <th>Text</th>
                        <th>Document Header</th>
                        <th>Profit Center</th>
                        <th>Company Code</th>
                        <th>Departemen</th>
                        <th>User</th>
                        <th>Activity</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($costOverheads as $row)
                    <tr>
                        <td>{{ $costOverheads->firstItem() + $loop->index }}</td>
                        <td>{{ $row->gl_account }}</td>
                        <td>{{ $row->gl_name }}</td>
                        <td>{{ $row->posting_date?->format('d/m/Y') }}</td>
                        <td class="fw-medium">{{ number_format($row->amount, 0, ',', '.') }}</td>
                        <td>{{ $row->text }}</td>
                        <td>{{ $row->document_header }}</td>
                        <td>{{ $row->profit_center }}</td>
                        <td>{{ $row->company_code }}</td>
                        <td>{{ $row->departemen }}</td>
                        <td>{{ $row->user }}</td>
                        <td>{{ $row->activity }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="12" class="text-center text-muted py-4">Tidak ada data.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($costOverheads->hasPages())
        <div class="d-flex justify-content-between align-items-center px-3 py-2 border-top" style="font-size:0.82rem">
            <span class="text-muted">
                Menampilkan {{ $costOverheads->firstItem() }}–{{ $costOverheads->lastItem() }} dari {{ $costOverheads->total() }} data
            </span>
            {{ $costOverheads->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>

<div class="modal fade" id="modalUpload" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('cost-overheads.import') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-upload me-2"></i>Upload File Excel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-medium" style="font-size:0.875rem">File Excel (.xlsx / .xls)</label>
                        <input type="file" name="file" class="form-control form-control-sm" accept=".xlsx,.xls" required>
                        <div class="form-text">
                            Urutan kolom: A=GL Account, B=GL Name, C=Posting Date (dd/mm/yyyy), D=Amount, E=Text, F=Document Header, G=Profit Center, H=Company Code, I=Departemen, J=User, K=Activity. Baris 1 = header.
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary btn-modern"><i class="bi bi-upload me-1"></i>Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
