@extends('layout.dashboard')
@section('header', 'Tambah Jenis Cuti')

@section('content')
<style>
    /* Styling agar input menyatu dengan tema modern */
    .form-control {
        height: 45px !important;
        border-radius: 8px !important;
        border: 1px solid #dee2e6 !important;
        box-shadow: 0 .125rem .25rem rgba(0,0,0,.075)!important;
    }
    
    .form-control:focus {
        border-color: #bc5e6b !important;
        box-shadow: 0 0 0 0.25rem rgba(188, 94, 107, 0.15) !important;
    }

    .btn-back {
        border-radius: 10px;
        background-color: #f8f9fa;
        color: #6c757d;
        border: 1px solid #dee2e6;
    }

    .form-check-input:checked {
        background-color: #bc5e6b;
        border-color: #bc5e6b;
    }

    .card-custom-header {
        background: linear-gradient(90deg, #bc5e6b 0%, #a34a57 100%);
        border: none;
        padding: 1.2rem;
        border-radius: 12px 12px 0 0 !important;
    }
</style>

<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card shadow border-0" style="border-radius: 12px;">
            <div class="card-header text-white card-custom-header">
                <h5 class="mb-0 fw-bold"><i class="fa-solid fa-plus-circle me-2"></i> Form Tambah Jenis Cuti</h5>
            </div>
            
            <div class="card-body p-4">
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0 small">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form class="row g-4" action="{{ route('leave-type.store') }}" method="post">
                    @csrf

                    <div class="col-md-6">
                        <label for="name" class="form-label fw-bold text-secondary">
                            <i class="fa-solid fa-tag me-2" style="color: #bc5e6b;"></i> Nama Cuti
                        </label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                            value="{{ old('name') }}" placeholder="Misal: Cuti Tahunan" required>
                        @error('name')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold text-secondary d-block">
                            <i class="fa-solid fa-hand-holding-dollar me-2" style="color: #bc5e6b;"></i> Dibayar
                        </label>
                        <div class="pt-2">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="is_paid" id="paid_yes" value="1"
                                    {{ old('is_paid', '1') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label" for="paid_yes">Ya</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="is_paid" id="paid_no" value="0"
                                    {{ old('is_paid') == '0' ? 'checked' : '' }}>
                                <label class="form-check-label text-danger" for="paid_no">Tidak</label>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold text-secondary d-block">
                            <i class="fa-solid fa-file-circle-check me-2" style="color: #bc5e6b;"></i> Wajib Dokumen
                        </label>
                        <div class="pt-2">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="document" id="document_yes" value="1"
                                    {{ old('document', '1') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label" for="document_yes">Ya</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="document" id="document_no" value="0"
                                    {{ old('document') == '0' ? 'checked' : '' }}>
                                <label class="form-check-label" for="document_no">Tidak</label>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6" id="deduction-wrapper">
                        <label for="deduction" class="form-label fw-bold text-secondary">
                            <i class="fa-solid fa-money-bill-transfer me-2" style="color: #bc5e6b;"></i> Potongan per Hari
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">Rp</span>
                            <input type="number" class="form-control @error('deduction') is-invalid @enderror" name="deduction"
                                id="deduction" value="{{ old('deduction', 0) }}" min="0">
                        </div>
                        @error('deduction')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="max_days" class="form-label fw-bold text-secondary">
                            <i class="fa-solid fa-calendar-check me-2" style="color: #bc5e6b;"></i> Maksimal Hari (Tahunan)
                        </label>
                        <input type="number" class="form-control @error('max_days') is-invalid @enderror" name="max_days"
                            id="max_days" value="{{ old('max_days') }}" placeholder="Contoh: 12" min="1">
                        @error('max_days')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="limit_type" class="form-label fw-bold text-secondary">
                            <i class="fa-solid fa-calendar me-2" style="color: #bc5e6b;"></i> Periode Limit Cuti
                        </label>
                        <select class="form-select form-control @error('limit_type') is-invalid @enderror" name="limit_type" id="limit_type" required>
                            <option value="yearly" {{ old('limit_type', 'yearly') === 'yearly' ? 'selected' : '' }}>Tahunan</option>
                            <option value="monthly" {{ old('limit_type') === 'monthly' ? 'selected' : '' }}>Bulanan</option>
                        </select>
                        @error('limit_type')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="limit_days" class="form-label fw-bold text-secondary">
                            <i class="fa-solid fa-calendar-days me-2" style="color: #bc5e6b;"></i> Limit Hari Cuti
                        </label>
                        <div class="input-group">
                            <input type="number" class="form-control @error('limit_days') is-invalid @enderror" name="limit_days"
                                id="limit_days" value="{{ old('limit_days') }}" placeholder="Contoh: 12" min="1" step="1" required>
                            <span class="input-group-text bg-light">Hari</span>
                        </div>
                        @error('limit_days')
                            <span class="invalid-feedback d-block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-12">
                        <label for="description" class="form-label fw-bold text-secondary">
                            <i class="fa-solid fa-align-left me-2" style="color: #bc5e6b;"></i> Deskripsi
                        </label>
                        <textarea class="form-control @error('description') is-invalid @enderror" name="description"
                            id="description" rows="2" placeholder="Keterangan tambahan...">{{ old('description') }}</textarea>
                        @error('description')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-12 mt-4">
                        <hr class="opacity-50">
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('leave-type.index') }}" class="btn btn-back px-4 shadow-sm">
                                <i class="fa-solid fa-arrow-left me-2"></i> Batal
                            </a>
                            <button type="submit" class="btn text-white px-5 shadow" style="background: linear-gradient(90deg, #bc5e6b 0%, #a34a57 100%); border: none; border-radius: 10px;">
                                <i class="fa-solid fa-save me-2"></i> Simpan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleDeduction() {
        const isPaid = document.querySelector('input[name="is_paid"]:checked').value;
        const wrapper = document.getElementById('deduction-wrapper');
        const input = document.getElementById('deduction');

        if (isPaid == 1) {
            wrapper.style.display = 'none';
            input.value = 0;
        } else {
            wrapper.style.display = 'block';
        }
    }

    document.querySelectorAll('input[name="is_paid"]').forEach(el => {
        el.addEventListener('change', toggleDeduction);
    });

    // Jalankan saat pertama kali load
    toggleDeduction();
</script>
@endsection
