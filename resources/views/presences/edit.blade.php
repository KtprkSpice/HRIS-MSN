@extends('layout.dashboard')
@section('header', 'Edit Data Kehadiran')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<style>
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
    .btn-back:hover {
        background-color: #e2e6ea;
        color: #495057;
    }
</style>

<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card shadow border-0">
            
            <!-- HEADER -->
            <div class="card-header text-white" style="background: linear-gradient(90deg, #bc5e6b 0%, #a34a57 100%); border: none; padding: 1.2rem;">
                <h5 class="mb-0 fw-bold">
                    <i class="fa-solid fa-pen-to-square me-2"></i> Edit Data Kehadiran
                </h5>
            </div>

            <!-- BODY -->
            <div class="card-body p-4">

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0 small">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- FORM (TIDAK DIUBAH LOGIKA) -->
                <form action="{{ route('presence.update', $presence->id) }}" method="post">
                    @csrf
                    @method('PUT')

                    <div class="row">

                        <!-- NAMA -->
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold text-secondary">
                                <i class="fa-solid fa-user me-2" style="color:#bc5e6b;"></i> Nama Karyawan
                            </label>
                            <select name="employee_id" class="form-control select2-js @error('employee_id') is-invalid @enderror">
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ $employee->id == $presence->employee_id ? 'selected' : '' }}>
                                        {{ ucwords($employee->fullname) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('employee_id')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- TANGGAL -->
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold text-secondary">
                                <i class="fa-solid fa-calendar-day me-2" style="color:#bc5e6b;"></i> Tanggal
                            </label>
                            <input type="date" 
                                class="form-control shadow-sm @error('date') is-invalid @enderror"
                                name="date"
                                value="{{ old('date', $presence->date) }}"
                                style="height:45px; border-radius:8px;" required>
                            @error('date')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- CHECK IN -->
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold text-secondary">
                                <i class="fa-solid fa-right-to-bracket me-2" style="color:#bc5e6b;"></i> Jam Masuk
                            </label>
                            <input type="datetime-local"
                                class="form-control shadow-sm @error('check_in') is-invalid @enderror"
                                name="check_in"
                                value="{{ old('check_in', $presence->check_in) }}"
                                style="height:45px; border-radius:8px;" required>
                            @error('check_in')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- CHECK OUT -->
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold text-secondary">
                                <i class="fa-solid fa-right-from-bracket me-2" style="color:#bc5e6b;"></i> Jam Keluar
                            </label>
                            <input type="datetime-local"
                                class="form-control shadow-sm @error('check_out') is-invalid @enderror"
                                name="check_out"
                                value="{{ old('check_out', $presence->check_out) }}"
                                style="height:45px; border-radius:8px;" required>
                            @error('check_out')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                    </div>

                    <hr class="my-4 opacity-50">

                    <!-- BUTTON -->
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-lg text-white shadow"
                            style="background: linear-gradient(90deg, #bc5e6b 0%, #a34a57 100%); border:none; border-radius:10px;">
                            <i class="fa-solid fa-save me-2"></i> Update Data
                        </button>

                        <a href="{{ route('presence.index') }}" class="btn btn-lg btn-back shadow-sm">
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
        $('.select2-js').select2({
            width: '100%'
        });
    });
</script>
@endsection