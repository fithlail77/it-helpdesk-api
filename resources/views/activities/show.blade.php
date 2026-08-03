@extends('layouts.app')
@section('title', 'Detail Tiket: '.$activity->ticket_number)

@section('content')
<div class="row g-3">
    <div class="col-lg-8">
        <div class="card card-modern shadow-sm">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-0 fw-semibold">{{ $activity->title }}</h6>
                    <small class="text-muted">{{ $activity->ticket_number }} • Dibuat {{ $activity->created_at->format('d/m/Y H:i') }}</small>
                </div>
                <div class="d-flex gap-2">
                    @php
                        $sc = match($activity->status) { 'completed' => 'success', 'in_progress' => 'info', 'pending' => 'warning', default => 'danger' };
                        $sl = match($activity->status) { 'completed' => 'Selesai', 'in_progress' => 'Diproses', 'pending' => 'Tertunda', default => 'Dibatalkan' };
                    @endphp
                    <span class="badge bg-{{ $sc }} badge-status fs-6">{{ $sl }}</span>
                    <a href="{{ route('activities.edit', $activity) }}" class="btn btn-sm btn-outline-primary btn-modern"><i class="bi bi-pencil me-1"></i>Edit</a>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="text-muted" style="font-size:0.8rem">Kategori</label>
                        <div class="fw-medium">{{ ucfirst($activity->category) }}{{ $activity->sub_category ? ' › '.$activity->sub_category : '' }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted" style="font-size:0.8rem">Prioritas</label>
                        <div>
                            @php
                                $pc = match($activity->priority) { 'urgent' => 'danger', 'high' => 'warning', 'medium' => 'primary', default => 'secondary' };
                                $pl = match($activity->priority) { 'urgent' => 'Mendesak', 'high' => 'Tinggi', 'medium' => 'Sedang', default => 'Rendah' };
                            @endphp
                            <span class="badge bg-{{ $pc }} badge-status">{{ $pl }}</span>
                        </div>
                    </div>
                    @if($activity->deviceType)
                    <div class="col-md-4">
                        <label class="text-muted" style="font-size:0.8rem">Jenis Perangkat</label>
                        <div class="fw-medium">{{ $activity->device_type }}</div>
                    </div>
                    @endif
                    @if($activity->barcodeNumber)
                    <div class="col-md-4">
                        <label class="text-muted" style="font-size:0.8rem">No Barcode</label>
                        <div class="fw-medium font-monospace">{{ $activity->barcode_number }}</div>
                    </div>
                    @endif
                    @if($activity->department)
                    <div class="col-md-4">
                        <label class="text-muted" style="font-size:0.8rem">Department</label>
                        <div class="fw-medium">{{ $activity->department }}</div>
                    </div>
                    @endif
                </div>

                <div class="mb-4">
                    <label class="text-muted" style="font-size:0.8rem">Deskripsi</label>
                    <div class="p-3 bg-light rounded" style="white-space:pre-wrap">{{ $activity->description }}</div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="text-muted" style="font-size:0.8rem">Pelapor</label>
                        <div class="fw-medium">{{ $activity->reporter_name }}</div>
                        @if($activity->reporter_phone) <small class="text-muted">{{ $activity->reporter_phone }}</small> @endif
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted" style="font-size:0.8rem">Ditugaskan ke</label>
                        <div class="fw-medium">{{ $activity->assignee?->name ?? '-' }}</div>
                        @if($activity->team) <small class="text-muted">Tim: {{ $activity->team->name }}</small> @endif
                    </div>
                </div>

                @if($activity->completedAt)
                <div class="mt-3 p-3 bg-success bg-opacity-10 rounded">
                    <i class="bi bi-check-circle text-success me-1"></i>
                    <span class="fw-medium">Selesai pada: {{ $activity->completedAt->format('d/m/Y H:i') }}</span>
                </div>
                @endif
            </div>
        </div>

        @if($activity->logs && $activity->logs->count())
        <div class="card card-modern shadow-sm mt-3">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-clock-history me-2"></i>Riwayat Perubahan</h6>
            </div>
            <div class="card-body p-0">
                @foreach($activity->logs->sortByDesc('created_at') as $log)
                <div class="d-flex gap-3 px-3 py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center flex-shrink-0" style="width:32px;height:32px">
                        <i class="bi bi-person text-muted" style="font-size:0.8rem"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div style="font-size:0.875rem">
                            <strong>{{ $log->user?->name ?? 'System' }}</strong> mengubah status ke
                            @php $lsc = match($log->status) { 'completed' => 'success', 'in_progress' => 'info', 'pending' => 'warning', default => 'secondary' }; @endphp
                            <span class="badge bg-{{ $lsc }} badge-status">{{ $log->status }}</span>
                        </div>
                        @if($log->note) <div style="font-size:0.82rem" class="text-muted">{{ $log->note }}</div> @endif
                        @if($log->repair_data)
                        <div class="mt-1 p-2 bg-primary bg-opacity-10 rounded" style="font-size:0.82rem">
                            <i class="bi bi-tools text-primary me-1"></i>
                            <strong>Detail Perbaikan:</strong> {{ $log->repair_data['description'] ?? '-' }}
                            @if(($log->repair_data['stock_part_used'] ?? false) ?? false)
                            <br><span class="text-warning"><i class="bi bi-box-seam me-1"></i>Stock Part: {{ $log->repair_data['stock_part_name'] ?? '-' }} x{{ $log->repair_data['stock_part_quantity'] ?? 1 }}</span>
                            @endif
                        </div>
                        @endif
                        @if($log->sparepart)
                        <div class="mt-1 p-2 bg-success bg-opacity-10 rounded" style="font-size:0.82rem">
                            <i class="bi bi-box-seam text-success me-1"></i>
                            <strong>Sparepart:</strong> {{ $log->sparepart->name }} x{{ $log->sparepart_quantity }}
                            @if($log->sparepart_price)
                            <span class="text-muted"> @ Rp {{ number_format($log->sparepart_price, 0, ',', '.') }} = <strong>Rp {{ number_format($log->sparepart_price * $log->sparepart_quantity, 0, ',', '.') }}</strong></span>
                            @endif
                        </div>
                        @endif
                        <small class="text-muted">{{ $log->created_at->format('d/m/Y H:i') }}</small>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <div class="col-lg-4">
        <div class="card card-modern shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-arrow-repeat me-2"></i>Ubah Status</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('activities.status', $activity) }}">
                    @csrf @method('PATCH')
                    <div class="mb-3">
                        <label class="form-label fw-medium">Status</label>
                        <select name="status" id="statusSelect" class="form-select">
                            <option value="pending" {{ $activity->status==='pending' ? 'selected' : '' }}>Tertunda</option>
                            <option value="in_progress" {{ $activity->status==='in_progress' ? 'selected' : '' }}>Diproses</option>
                            <option value="completed" {{ $activity->status==='completed' ? 'selected' : '' }}>Selesai</option>
                            <option value="cancelled" {{ $activity->status==='cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                    </div>

                    <div id="repairFields" style="display:none">
                        <hr>
                        <h6 class="fw-semibold" style="font-size:0.9rem"><i class="bi bi-tools me-1"></i>Data Perbaikan</h6>
                        <div class="mb-2">
                            <label class="form-label" style="font-size:0.82rem">Deskripsi Perbaikan <span class="text-danger">*</span></label>
                            <textarea name="repair_description" class="form-control form-control-sm" rows="2" placeholder="Jelaskan perbaikan yang dilakukan..."></textarea>
                        </div>

                        <div class="mt-2">
                            <label class="form-label fw-medium" style="font-size:0.82rem">Sparepart (Pilih dari stok)</label>
                            <div id="sparepartRows">
                                <div class="sparepart-row d-flex gap-2 mb-2 align-items-end">
                                    <div class="flex-grow-1">
                                        <select name="spareparts[0][id]" class="form-select form-select-sm sparepart-dropdown">
                                            <option value="">-- Pilih sparepart --</option>
                                            @foreach($spareparts as $sp)
                                            <option value="{{ $sp->id }}" data-price="{{ $sp->price }}" data-stock="{{ $sp->stock }}">{{ $sp->name }} (Stok: {{ $sp->stock }} - Rp {{ number_format($sp->price, 0, ',', '.') }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div style="width:70px">
                                        <input type="number" name="spareparts[0][quantity]" class="form-control form-control-sm sparepart-qty" min="1" value="1" placeholder="Qty">
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-danger btn-remove-sparepart" style="display:none"><i class="bi bi-x-lg"></i></button>
                                </div>
                            </div>
                            <button type="button" id="addSparepartBtn" class="btn btn-sm btn-outline-success btn-modern mt-1"><i class="bi bi-plus-lg me-1"></i>Tambah Part</button>
                            <div id="sparepartTotalRow" class="mt-2" style="display:none">
                                <div class="p-2 bg-success bg-opacity-10 rounded" style="font-size:0.82rem">
                                    <strong class="text-success">Total Biaya Sparepart: <span id="grandTotal">Rp 0</span></strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-modern w-100 mt-2"><i class="bi bi-check-lg me-1"></i>Update Status</button>
                </form>
            </div>
        </div>

        @if($activity->latitude && $activity->longitude)
        <div class="card card-modern shadow-sm mt-3">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-geo-alt me-2"></i>Lokasi</h6>
            </div>
            <div class="card-body p-0">
                <div class="ratio ratio-16x9">
                    <iframe src="https://www.openstreetmap.org/export/embed.html?bbox={{ $activity->longitude - 0.01 }}%2C{{ $activity->latitude - 0.01 }}%2C{{ $activity->longitude + 0.01 }}%2C{{ $activity->latitude + 0.01 }}&layer=mapnik&marker={{ $activity->latitude }}%2C{{ $activity->longitude }}" style="border:0" loading="lazy"></iframe>
                </div>
                <div class="px-3 py-2">
                    <small class="text-muted font-monospace">{{ $activity->latitude }}, {{ $activity->longitude }}</small>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('statusSelect').addEventListener('change', function() {
    document.getElementById('repairFields').style.display = this.value === 'completed' ? '' : 'none';
});

function formatRupiah(num) {
    return 'Rp ' + num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

let sparepartIndex = 1;

function updateGrandTotal() {
    let total = 0;
    document.querySelectorAll('.sparepart-row').forEach(function(row) {
        const dropdown = row.querySelector('.sparepart-dropdown');
        const qty = row.querySelector('.sparepart-qty');
        if (dropdown && dropdown.value) {
            const price = parseFloat(dropdown.options[dropdown.selectedIndex].dataset.price) || 0;
            const q = parseInt(qty.value) || 1;
            total += price * q;
        }
    });
    const totalRow = document.getElementById('sparepartTotalRow');
    const grandTotal = document.getElementById('grandTotal');
    if (total > 0) {
        grandTotal.textContent = formatRupiah(total);
        totalRow.style.display = '';
    } else {
        totalRow.style.display = 'none';
    }
}

document.getElementById('addSparepartBtn').addEventListener('click', function() {
    const container = document.getElementById('sparepartRows');
    const firstRow = container.querySelector('.sparepart-row');
    const newRow = firstRow.cloneNode(true);

    newRow.querySelector('.sparepart-dropdown').name = 'spareparts[' + sparepartIndex + '][id]';
    newRow.querySelector('.sparepart-dropdown').value = '';
    newRow.querySelector('.sparepart-qty').name = 'spareparts[' + sparepartIndex + '][quantity]';
    newRow.querySelector('.sparepart-qty').value = '1';
    newRow.querySelector('.btn-remove-sparepart').style.display = '';

    container.appendChild(newRow);
    bindRowEvents(newRow);
    sparepartIndex++;
});

function bindRowEvents(row) {
    row.querySelector('.sparepart-dropdown').addEventListener('change', function() {
        updateGrandTotal();
        const container = document.getElementById('sparepartRows');
        if (container.children.length === 1) {
            row.querySelector('.btn-remove-sparepart').style.display = 'none';
        }
    });
    row.querySelector('.sparepart-qty').addEventListener('input', updateGrandTotal);
    row.querySelector('.btn-remove-sparepart').addEventListener('click', function() {
        row.remove();
        updateGrandTotal();
        const container = document.getElementById('sparepartRows');
        if (container.children.length === 1) {
            container.querySelector('.btn-remove-sparepart').style.display = 'none';
        }
    });
}

document.querySelectorAll('.sparepart-row').forEach(function(row) {
    row.querySelector('.sparepart-dropdown').addEventListener('change', updateGrandTotal);
    row.querySelector('.sparepart-qty').addEventListener('input', updateGrandTotal);
});
</script>
@endpush
