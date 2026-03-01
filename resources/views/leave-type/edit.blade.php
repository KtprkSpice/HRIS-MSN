@extends('layout.dashboard')
@section('header', 'Konfigurasi Jenis Cuti')

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

    .btn-back:hover {
        background-color: #e2e6ea;
        color: #495057;
    }

    .input-group-text {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 8px 0 0 8px !important;
        color: #6c757d;
        font-weight: 600;
    }

    .form-check-input:checked {
        background-color: #bc5e6b;
        border-color: #bc5e6b;
    }
</style>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow border-0">
            <div class="card-header text-white" style="background: linear-gradient(90deg, #bc5e6b 0%, #a34a57 100%); border: none; padding: 1.2rem;">
                <h5 class="mb-0 fw-bold"><i class="fa-solid fa-pen-to-square me-2"></i> Form Detail Jenis Cuti</h5>
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

                <form action="{{ route('leave-type.update', $leaveType->id) }}" method="post">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-4">
                        <label for="name" class="form-label fw-bold text-secondary">
                            <i class="fa-solid fa-tag me-2" style="color: #bc5e6b;"></i> Nama Cuti
                        </label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                            value="{{ old('name', $leaveType->name) }}" placeholder="Misal: Cuti Tahunan" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold text-secondary d-block">
                                <i class="fa-solid fa-hand-holding-dollar me-2" style="color: #bc5e6b;"></i> Status Gaji
                            </label>
                            <div class="d-flex gap-4 pt-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="is_paid" id="paid_yes" value="1"
                                        {{ old('is_paid', $leaveType->is_paid) == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="paid_yes">Dibayar</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="is_paid" id="paid_no" value="0"
                                        {{ old('is_paid', $leaveType->is_paid) == '0' ? 'checked' : '' }}>
                                    <label class="form-check-label text-danger fw-bold" for="paid_no">Potong Gaji</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold text-secondary d-block">
                                <i class="fa-solid fa-file-circle-check me-2" style="color: #bc5e6b;"></i> Wajib Dokumen
                            </label>
                            <div class="d-flex gap-4 pt-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="document" id="document_yes" value="1"
                                        {{ old('document', $leaveType->document) == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="document_yes">Ya</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="document" id="document_no" value="0"
                                        {{ old('document', $leaveType->document) == '0' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="document_no">Tidak</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4" id="deduction-wrapper">
                        <label for="deduction" class="form-label fw-bold text-secondary">
                            <i class="fa-solid fa-money-bill-transfer me-2" style="color: #bc5e6b;"></i> Potongan per Hari
                        </label>
                        <div class="input-group shadow-sm">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control @error('deduction') is-invalid @enderror" name="deduction"
                                id="deduction" value="{{ old('deduction', $leaveType->deduction) }}" min="0">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="max_days" class="form-label fw-bold text-secondary">
                            <i class="fa-solid fa-calendar-check me-2" style="color: #bc5e6b;"></i> Maksimal Hari (Kuota)
                        </label>
                        <div class="input-group shadow-sm">
                            <input type="number" class="form-control @error('max_days') is-invalid @enderror" name="max_days"
                                id="max_days" value="{{ old('max_days', $leaveType->max_days) }}" min="1">
                            <span class="input-group-text bg-white text-muted small">Hari / Tahun</span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="description" class="form-label fw-bold text-secondary">
                            <i class="fa-solid fa-align-left me-2" style="color: #bc5e6b;"></i> Deskripsi / Keterangan
                        </label>
                        <textarea class="form-control shadow-sm pt-2" name="description" id="description" rows="3" 
                            style="height: auto !important; border-radius: 8px;" 
                            placeholder="Catatan tambahan mengenai jenis cuti ini...">{{ old('description', $leaveType->description) }}</textarea>
                    </div>

                    <hr class="my-4 opacity-50">

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-lg text-white shadow" style="background: linear-gradient(90deg, #bc5e6b 0%, #a34a57 100%); border: none; border-radius: 10px;">
                            <i class="fa-solid fa-save me-2"></i> Simpan Perubahan
                        </button>
                        <a href="{{ route('leave-type.index') }}" class="btn btn-lg btn-back shadow-sm">
                            <i class="fa-solid fa-arrow-left me-2"></i> Batal & Kembali
                        </a>
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
            wrapper.style.opacity = '0.5';
            wrapper.style.pointerEvents = 'none';
            input.value = 0;
        } else {
            wrapper.style.opacity = '1';
            wrapper.style.pointerEvents = 'auto';
        }
    }

    document.querySelectorAll('input[name="is_paid"]').forEach(el => {
        el.addEventListener('change', toggleDeduction);
    });

    document.addEventListener('DOMContentLoaded', toggleDeduction);
</script>
@endsection