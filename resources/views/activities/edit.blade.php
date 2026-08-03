@extends('layouts.app')
@section('title', 'Edit Tiket: '.$activity->ticket_number)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card card-modern shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-pencil me-2"></i>Edit Tiket {{ $activity->ticket_number }}</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('activities.update', $activity) }}">
                    @csrf @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-medium">Judul <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $activity->title) }}" required>
                            @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-medium">Deskripsi <span class="text-danger">*</span></label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3" required>{{ old('description', $activity->description) }}</textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium">Kategori <span class="text-danger">*</span></label>
                            <select name="category" id="categorySelect" class="form-select" required>
                                <option value="hardware" {{ old('category', $activity->category)==='hardware' ? 'selected' : '' }}>Hardware</option>
                                <option value="software" {{ old('category', $activity->category)==='software' ? 'selected' : '' }}>Software</option>
                                <option value="network" {{ old('category', $activity->category)==='network' ? 'selected' : '' }}>Jaringan</option>
                                <option value="other" {{ old('category', $activity->category)==='other' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                        </div>
                        <div class="col-md-4" id="subCategoryGroup">
                            <label class="form-label fw-medium">Sub Kategori</label>
                            <select name="sub_category" id="subCategorySelect" class="form-select">
                                <option value="">-- Pilih Sub Kategori --</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium">Department</label>
                            <select name="department" class="form-select">
                                @foreach(['IT','Finance','HR','Marketing','Operations','Sales','Engineering','Lainnya'] as $d)
                                    <option value="{{ $d }}" {{ old('department', $activity->department)===$d ? 'selected' : '' }}>{{ $d }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4" id="deviceTypeGroup" style="display:none">
                            <label class="form-label fw-medium">Jenis Perangkat</label>
                            <input type="text" name="device_type" class="form-control" value="{{ old('device_type', $activity->device_type) }}">
                        </div>
                        <div class="col-md-4" id="barcodeGroup" style="display:none">
                            <label class="form-label fw-medium">No Barcode</label>
                            <input type="text" name="barcode_number" class="form-control" value="{{ old('barcode_number', $activity->barcode_number) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium">Prioritas <span class="text-danger">*</span></label>
                            <select name="priority" class="form-select" required>
                                <option value="low" {{ old('priority', $activity->priority)==='low' ? 'selected' : '' }}>Rendah</option>
                                <option value="medium" {{ old('priority', $activity->priority)==='medium' ? 'selected' : '' }}>Sedang</option>
                                <option value="high" {{ old('priority', $activity->priority)==='high' ? 'selected' : '' }}>Tinggi</option>
                                <option value="urgent" {{ old('priority', $activity->priority)==='urgent' ? 'selected' : '' }}>Mendesak</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium">Ditugaskan ke</label>
                            <select name="assigned_to" class="form-select">
                                <option value="">-- Belum Ditugaskan --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('assigned_to', $activity->assigned_to)==$user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium">Tim</label>
                            <select name="team_id" class="form-select">
                                <option value="">-- Pilih Tim --</option>
                                @foreach($teams as $team)
                                    <option value="{{ $team->id }}" {{ old('team_id', $activity->team_id)==$team->id ? 'selected' : '' }}>{{ $team->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Nama Pelapor</label>
                            <input type="text" name="reporter_name" class="form-control" value="{{ old('reporter_name', $activity->reporter_name) }}" readonly>
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary btn-modern"><i class="bi bi-check-lg me-1"></i>Update Tiket</button>
                        <a href="{{ route('activities.show', $activity) }}" class="btn btn-outline-secondary btn-modern">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const subCategories = {
    hardware: ['Laptop','Desktop','Printer','Scanner','Monitor','Keyboard','Mouse','UPS','Server','Lainnya'],
    software: ['Operating System','Aplikasi','Driver','Antivirus','Database','Lainnya'],
    network: ['Router','Switch','Access Point','Kabel LAN','Server','Lainnya'],
};
const deviceFields = ['hardware','network'];

function updateSubCategory() {
    const cat = document.getElementById('categorySelect').value;
    const subSel = document.getElementById('subCategorySelect');
    const subGrp = document.getElementById('subCategoryGroup');
    subSel.innerHTML = '<option value="">-- Pilih Sub Kategori --</option>';
    if (subCategories[cat]) {
        subGrp.style.display = '';
        subCategories[cat].forEach(s => {
            const opt = document.createElement('option');
            opt.value = s; opt.text = s;
            if ('{{ old("sub_category", $activity->sub_category ?? "") }}' === s) opt.selected = true;
            subSel.appendChild(opt);
        });
    } else {
        subGrp.style.display = 'none';
    }
    toggleDeviceFields();
}

function toggleDeviceFields() {
    const cat = document.getElementById('categorySelect').value;
    const sub = document.getElementById('subCategorySelect').value;
    const show = deviceFields.includes(cat) && sub !== '';
    document.getElementById('deviceTypeGroup').style.display = show ? '' : 'none';
    document.getElementById('barcodeGroup').style.display = show ? '' : 'none';
}

document.getElementById('categorySelect').addEventListener('change', updateSubCategory);
document.getElementById('subCategorySelect').addEventListener('change', toggleDeviceFields);
updateSubCategory();
</script>
@endpush
