@extends('layout.dashboard')
@section('header', 'Tambah Gaji Manual')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Styling khusus agar menyatu dengan tema modern */
    .select2-container--default .select2-selection--single {
        height: 45px !important;
        padding: 8px !important;
        border: 1px solid #dee2e6 !important;
        border-radius: 8px !important;
        box-shadow: 0 .125rem .25rem rgba(0,0,0,.075)!important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 42px !important;
    }
    .select2-dropdown {
        border: 1px solid #bc5e6b !important;
        border-radius: 8px !important;
    }
    .btn-back {
        border-radius: 10px;
        background-color: #f8f9fa;
        color: #6c757d;
        border: 1px solid #dee2e6;
        transition: all 0.3s;
    }
    .btn-back:hover { background-color: #e2e6ea; }
    
    .input-group-text {
        background-color: #f8f9fa;
        color: #bc5e6b;
        font-weight: bold;
        border-radius: 8px 0 0 8px !important;
    }
    .money-input { border-radius: 0 8px 8px 0 !important; }
</style>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow border-0">
            <div class="card-header text-white" style="background: linear-gradient(90deg, #bc5e6b 0%, #a34a57 100%); border: none; padding: 1.2rem;">
                <h5 class="mb-0 fw-bold"><i class="fa-solid fa-plus-circle me-2"></i> Form Input Gaji Baru</h5>
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

                <form action="{{ route('salary.store') }}" method="post">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="employee_id" class="form-label fw-bold text-secondary">
                            <i class="fa-solid fa-user me-2" style="color: #bc5e6b;"></i> Nama Karyawan
                        </label>
                        <select name="employee_id" id="employee_id" class="form-control select2-js @error('employee_id') is-invalid @enderror">
                            <option value="">-- Cari Nama Karyawan --</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                    {{ ucwords($employee->fullname) }}
                                </option>
                            @endforeach
                        </select>
                        @error('employee_id')
                            <span class="invalid-feedback d-block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="net_salary" class="form-label fw-bold text-secondary">
                            <i class="fa-solid fa-money-bill-wave me-2" style="color: #bc5e6b;"></i> Jumlah Gaji Pokok
                        </label>
                        <div class="input-group shadow-sm">
                            <span class="input-group-text">Rp</span>
                            <input type="text" class="form-control money-input @error('net_salary') is-invalid @enderror" 
                                name="net_salary" id="net_salary" required placeholder="0" 
                                value="{{ old('net_salary') }}" style="height: 45px;">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label for="cuts" class="form-label fw-bold text-secondary">
                                <i class="fa-solid fa-scissors me-2" style="color: #bc5e6b;"></i> Potongan
                            </label>
                            <div class="input-group shadow-sm">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control money-input @error('cuts') is-invalid @enderror" 
                                    name="cuts" id="cuts" required placeholder="0" 
                                    value="{{ old('cuts') }}" style="height: 45px;">
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label for="bonus" class="form-label fw-bold text-secondary">
                                <i class="fa-solid fa-gift me-2" style="color: #bc5e6b;"></i> Bonus
                            </label>
                            <div class="input-group shadow-sm">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control money-input @error('bonus') is-invalid @enderror" 
                                    name="bonus" id="bonus" required placeholder="0" 
                                    value="{{ old('bonus') }}" style="height: 45px;">
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="date" class="form-label fw-bold text-secondary">
                            <i class="fa-solid fa-calendar-check me-2" style="color: #bc5e6b;"></i> Tanggal Pencairan Gaji
                        </label>
                        <input type="date" class="form-control shadow-sm @error('date') is-invalid @enderror" 
                            name="date" id="date" required value="{{ old('date') }}" style="height: 45px; border-radius: 8px;">
                    </div>

                    <hr class="my-4 opacity-50">

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-lg text-white shadow" style="background: linear-gradient(90deg, #bc5e6b 0%, #a34a57 100%); border: none; border-radius: 10px;">
                            <i class="fa-solid fa-paper-plane me-2"></i> Simpan Data Gaji
                        </button>
                        <a href="{{ route('salary.index') }}" class="btn btn-lg btn-back shadow-sm">
                            <i class="fa-solid fa-arrow-left me-2"></i> Batal & Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        // Jalankan Select2
        $('.select2-js').select2({ width: '100%' });

        // Auto-format Rupiah saat mengetik
        $('.money-input').on('keyup', function(){
            let val = $(this).val().replace(/[^0-9]/g, '');
            if(val != "") {
                $(this).val(new Intl.NumberFormat('id-ID').format(val));
            }
        });
    });
</script>
@endsection