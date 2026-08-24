@extends('layout.dashboard')
@section('header', 'Edit Gaji Manual')

@section('content')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        .select2-container--default .select2-selection--single {
            height: 45px !important;
            padding: 8px !important;
            border: 1px solid #dee2e6 !important;
            border-radius: 8px !important;
            box-shadow: 0 .125rem .25rem rgba(0, 0, 0, .075) !important;
        }

        .input-group-text {
            background-color: #f8f9fa;
            color: #bc5e6b;
            font-weight: bold;
            border-radius: 8px 0 0 8px !important;
        }

        .money-input {
            border-radius: 0 8px 8px 8px !important;
        }

        /* 🔥 LANDSCAPE STYLE */
        .salary-wrapper {
            max-width: 1200px;
            margin: auto;
        }

        .card {
            border-radius: 16px;
        }

        .card-header {
            border-radius: 16px 16px 0 0;
        }
    </style>

    <div class="container-fluid salary-wrapper">
        <div class="row justify-content-center">

            <!-- 🔥 DIPERLEBAR UNTUK LANDSCAPE -->
            <div class="col-lg-10 col-xl-9">

                <div class="card shadow border-0">

                    <div class="card-header text-white" style="background: linear-gradient(90deg, #bc5e6b 0%, #a34a57 100%);">
                        <h5 class="mb-0 fw-bold">
                            <i class="fa-solid fa-plus-circle me-2"></i>
                            Form Input Gaji Karyawan
                        </h5>
                    </div>

                    <div class="card-body p-5">

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('salary.update', $salary->id) }}" method="post">
                            @csrf
                            @method('PUT')

                            <!-- KARYAWAN -->
                            <div class="mb-4">
                                <label class="form-label fw-bold text-secondary">
                                    <i class="fa-solid fa-user me-2" style="color:#bc5e6b;"></i>
                                    Nama Karyawan
                                </label>

                                <select name="employee_id" id="employeeSelect" class="form-control select2-js">
                                    <option value="">-- Cari Nama Karyawan --</option>

                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}"
                                            data-salary="{{ $employee->position->base_salary ?? 0 }}"
                                            {{ old('employee_id', $salary->employee->id == $employee->id ? 'selected' : '') }}>
                                            {{ ucwords($employee->fullname) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="row">

                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-bold text-secondary">
                                        <i class="fa-solid fa-money-bill-wave me-2" style="color:#bc5e6b;"></i>
                                        Gaji Pokok
                                    </label>
                                    <div class="input-group shadow-sm">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" name="net_salary" id="netSalary"
                                            class="form-control money-input"
                                            value="{{ number_format(old('net_salary', $salary->net_salary), 0, ',', '.') }}"
                                            readonly>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-bold text-secondary">
                                        <i class="fa-solid fa-hand-holding-dollar me-2" style="color:#bc5e6b;"></i>
                                        Potongan BPJS Kesehatan
                                    </label>
                                    <div class="input-group shadow-sm">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" name="bpjs_kesehatan_cuts" class="form-control money-input"
                                            value="{{ number_format(old('bpjs_kesehatan_cuts', $salary->bpjs_kesehatan_cuts), 0, ',', '.') }}">
                                    </div>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-bold text-secondary">
                                        <i class="fa-solid fa-hand-holding-dollar me-2" style="color:#bc5e6b;"></i>
                                        Potongan BPJS Ketenagakerjaan
                                    </label>
                                    <div class="input-group shadow-sm">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" name="bpjs_ketenagakerjaan_cuts"
                                            class="form-control money-input"
                                            value="{{ number_format(old('bpjs_ketenagakerjaan_cuts', $salary->bpjs_ketenagakerjaan_cuts), 0, ',', '.') }}">
                                    </div>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-bold text-secondary">
                                        <i class="fa-solid fa-clock me-2" style="color:#bc5e6b;"></i>
                                        Potongan Telat
                                    </label>
                                    <div class="input-group shadow-sm">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" name="late_cuts" class="form-control money-input"
                                            value="{{ number_format(old('late_cuts', $salary->late_cuts), 0, ',', '.') }}" readonly>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-bold text-secondary">
                                        <i class="fa-solid fa-user-xmark me-2" style="color:#bc5e6b;"></i>
                                        Potongan Absen
                                    </label>
                                    <div class="input-group shadow-sm">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" name="absent_cuts" class="form-control money-input"
                                            value="{{ number_format(old('absent_cuts', $salary->absent_cuts), 0, ',', '.') }}" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold text-secondary">
                                    <i class="fa-solid fa-calendar-check me-2" style="color:#bc5e6b;"></i>
                                    Tanggal Pencairan
                                </label>

                                <input type="date" name="date" class="form-control"
                                    value="{{ old('date', $salary->date) }}">
                            </div>

                            <button type="submit" class="btn btn-lg text-white w-100"
                                style="background: linear-gradient(90deg,#bc5e6b,#a34a57); border-radius:12px;">
                                <i class="fa-solid fa-floppy-disk me-2"></i>
                                Update Data Gaji
                            </button>

                            <div class="d-grid gap-2 mt-3">
                                <a href="{{ route('salary.index') }}" class="btn btn-light btn-lg shadow-sm"
                                    style="border-radius:12px; border:1px solid #dee2e6;">
                                    <i class="fa-solid fa-arrow-left me-2"></i>
                                    Kembali
                                </a>
                            </div>

                        </form>
                    </div>

                </div>

            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {

            // INIT SELECT2
            $('.select2-js').select2({
                width: '100%'
            });

            // FORMAT INPUT UANG
            $('.money-input').on('keyup', function() {

                let value = $(this).val().replace(/[^0-9]/g, '');

                if (value !== '') {
                    $(this).val(
                        new Intl.NumberFormat('id-ID').format(value)
                    );
                } else {
                    $(this).val('');
                }

            });

            // AUTO AMBIL GAJI POKOK DARI POSITION
            $('#employeeSelect').on('change', function() {

                // ambil option yang dipilih
                let selected = $(this).find(':selected');

                // ambil data-salary
                let salary = selected.data('salary');

                // cek jika ada salary
                if (salary && salary != 0) {

                    // format rupiah
                    let formatted = new Intl.NumberFormat('id-ID').format(salary);

                    // isi ke input gaji
                    $('#netSalary').val(formatted);

                } else {

                    // kosongkan jika tidak ada
                    $('#netSalary').val('');
                }

            });

        });
    </script>
@endsection
