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
            border-radius: 0 8px 8px 0 !important;
        }

        .card {
            border-radius: 16px;
        }
    </style>

    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-9">

                <div class="card shadow border-0">

                    <div class="card-header text-white" style="background: linear-gradient(90deg, #bc5e6b 0%, #a34a57 100%);">
                        <h5 class="mb-0 fw-bold">
                            <i class="fa-solid fa-file-pen me-2"></i> Edit Gaji Karyawan
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

                                <select name="employee_id" class="form-control select2-js">
                                    <option value="">Choose...</option>
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}"
                                            {{ old('employee_id', $salary->employee_id) == $employee->id ? 'selected' : '' }}>
                                            {{ ucwords($employee->fullname) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="row">

                                <!-- GAJI POKOK -->
                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-bold text-secondary">
                                        <i class="fa-solid fa-money-bill-wave me-2" style="color:#bc5e6b;"></i>
                                        Gaji Pokok
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" name="net_salary" class="form-control money-input"
                                            value="{{ old('net_salary', number_format($salary->net_salary, 0, ',', '.')) }}">
                                    </div>
                                </div>

                                <!-- TUNJANGAN -->
                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-bold text-secondary">
                                        <i class="fa-solid fa-hand-holding-dollar me-2" style="color:#bc5e6b;"></i>
                                        Tunjangan
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" name="allowance" class="form-control money-input"
                                            value="{{ old('allowance', number_format($salary->allowance ?? 0, 0, ',', '.')) }}">
                                    </div>
                                </div>

                                <!-- LEMBUR -->
                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-bold text-secondary">
                                        <i class="fa-solid fa-clock me-2" style="color:#bc5e6b;"></i>
                                        Lembur
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" name="overtime" class="form-control money-input"
                                            value="{{ old('overtime', number_format($salary->overtime ?? 0, 0, ',', '.')) }}">
                                    </div>
                                </div>

                                <!-- POTONGAN ABSEN -->
                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-bold text-secondary">
                                        <i class="fa-solid fa-user-xmark me-2" style="color:#bc5e6b;"></i>
                                        Potongan Absen
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" name="absent_deduction" class="form-control money-input"
                                            value="{{ old('absent_deduction', number_format($salary->absent_deduction ?? 0, 0, ',', '.')) }}">
                                    </div>
                                </div>

                                <!-- BPJS -->
                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-bold text-secondary">
                                        <i class="fa-solid fa-shield-heart me-2" style="color:#bc5e6b;"></i>
                                        BPJS
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" name="bpjs" class="form-control money-input"
                                            value="{{ old('bpjs', number_format($salary->bpjs ?? 0, 0, ',', '.')) }}">
                                    </div>
                                </div>

                                <!-- PAJAK -->
                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-bold text-secondary">
                                        <i class="fa-solid fa-file-invoice-dollar me-2" style="color:#bc5e6b;"></i>
                                        Pajak
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" name="tax" class="form-control money-input"
                                            value="{{ old('tax', number_format($salary->tax ?? 0, 0, ',', '.')) }}">
                                    </div>
                                </div>

                                <!-- POTONGAN -->
                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-bold text-secondary">
                                        <i class="fa-solid fa-scissors me-2" style="color:#bc5e6b;"></i>
                                        Potongan
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" name="cuts" class="form-control money-input"
                                            value="{{ old('cuts', number_format($salary->cuts, 0, ',', '.')) }}">
                                    </div>
                                </div>

                                <!-- BONUS -->
                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-bold text-secondary">
                                        <i class="fa-solid fa-gift me-2" style="color:#bc5e6b;"></i>
                                        Bonus
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" name="bonus" class="form-control money-input"
                                            value="{{ old('bonus', number_format($salary->bonus, 0, ',', '.')) }}">
                                    </div>
                                </div>

                            </div>

                            <!-- TANGGAL -->
                            <div class="mb-4">
                                <label class="form-label fw-bold text-secondary">
                                    <i class="fa-solid fa-calendar-check me-2" style="color:#bc5e6b;"></i>
                                    Tanggal Gaji
                                </label>

                                <input type="date" name="date" class="form-control"
                                    value="{{ old('date', $salary->date) }}">
                            </div>

                            <button type="submit" class="btn btn-lg text-white w-100"
                                style="background: linear-gradient(90deg,#bc5e6b,#a34a57); border-radius:12px;">
                                <i class="fa-solid fa-floppy-disk me-2"></i>
                                Update Data Gaji
                            </button>

                            <a href="{{ route('salary.index') }}" class="btn btn-light w-100 mt-3"
                                style="border-radius:12px; border:1px solid #dee2e6;">
                                <i class="fa-solid fa-arrow-left me-2"></i>
                                Kembali
                            </a>

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
            $('.select2-js').select2({
                width: '100%'
            });

            $('.money-input').on('keyup', function() {
                let val = $(this).val().replace(/[^0-9]/g, '');
                if (val !== "") {
                    $(this).val(new Intl.NumberFormat('id-ID').format(val));
                }
            });
        });
    </script>
@endsection
