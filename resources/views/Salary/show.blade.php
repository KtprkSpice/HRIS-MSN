@extends('layout.dashboard')
@section('header', 'Detail Gaji Karyawan')

@section('content')

    <style>
        .detail-box {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 12px 15px;
            border: 1px solid #dee2e6;
            font-weight: 500;
        }

        .label-title {
            font-size: 14px;
            color: #6c757d;
            font-weight: 600;
            margin-bottom: 5px;
        }
    </style>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow border-0">
                <div class="card-header text-white"
                    style="background: linear-gradient(90deg, #bc5e6b 0%, #a34a57 100%); padding: 1.2rem;">
                    <h5 class="mb-0 fw-bold">
                        <i class="fa-solid fa-file-invoice-dollar me-2"></i> Detail Gaji
                    </h5>
                </div>

                <div class="card-body p-4">

                    {{-- Nama Karyawan --}}
                    <div class="mb-4">
                        <div class="label-title">
                            <i class="fa-solid fa-user me-2" style="color: #bc5e6b;"></i> Nama Karyawan
                        </div>
                        <div class="detail-box">
                            {{ ucwords($salary->employee->fullname ?? '-') }}
                        </div>
                    </div>

                    {{-- Gaji Pokok --}}
                    <div class="mb-4">
                        <div class="label-title">
                            <i class="fa-solid fa-money-bill-wave me-2" style="color: #bc5e6b;"></i> Gaji Pokok
                        </div>
                        <div class="detail-box">
                            Rp {{ number_format($salary->net_salary, 0, ',', '.') }}
                        </div>
                    </div>

                    {{-- Potongan --}}
                    <div class="mb-4">
                        <div class="label-title">
                            <i class="fa-solid fa-scissors me-2" style="color: #bc5e6b;"></i> Potongan
                        </div>
                        <div class="detail-box">
                            Rp {{ number_format($salary->cuts, 0, ',', '.') }}
                        </div>
                    </div>

                    {{-- Bonus --}}
                    <div class="mb-4">
                        <div class="label-title">
                            <i class="fa-solid fa-gift me-2" style="color: #bc5e6b;"></i> Bonus
                        </div>
                        <div class="detail-box">
                            Rp {{ number_format($salary->bonus, 0, ',', '.') }}
                        </div>
                    </div>

                    {{-- Tanggal --}}
                    <div class="mb-4">
                        <div class="label-title">
                            <i class="fa-solid fa-calendar-day me-2" style="color: #bc5e6b;"></i> Tanggal Gaji
                        </div>
                        <div class="detail-box">
                            {{ \Carbon\Carbon::parse($salary->date)->format('d M Y') }}
                        </div>
                    </div>

                    <hr class="my-4 opacity-50">

                    {{-- Button --}}
                    <div class="d-grid">
                        <a href="{{ route('salary.index') }}" class="btn btn-lg shadow-sm"
                            style="border-radius: 10px; background:#f8f9fa; border:1px solid #dee2e6;">
                            <i class="fa-solid fa-arrow-left me-2"></i> Kembali
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>

@endsection
