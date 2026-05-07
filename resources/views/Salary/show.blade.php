@extends('layout.dashboard')
@section('header', 'Detail Slip Gaji Karyawan')

@section('content')

    <style>
        /* ================= STYLE DEFAULT ================= */
        body {
            background: #f4f6f9;
        }

        .card-slip {
            border-radius: 14px;
            border: none;
            background: #fff;
        }

        .company-header {
            background: linear-gradient(90deg, #bc5e6b 0%, #a34a57 100%);
            color: #fff;
            border-radius: 12px;
            padding: 18px;
        }

        .logo-pt {
            width: 55px;
            height: 55px;
            object-fit: contain;
            background: #fff;
            border-radius: 8px;
            padding: 5px;
        }

        .detail-box {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 10px;
            padding: 12px 15px;
            font-weight: 500;
        }

        .label-title {
            font-size: 13px;
            color: #6c757d;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .total-box {
            background: linear-gradient(90deg, #bc5e6b 0%, #a34a57 100%);
            color: #fff;
            border-radius: 10px;
            padding: 14px 16px;
        }

        #printOnlyHeader {
            display: none;
        }

        /* ================= PRINT ================= */
        @media print {

            @page {
                size: A4 portrait;
                margin: 0;
            }

            html,
            body {
                width: 210mm;
                height: 297mm;
                margin: 0;
                padding: 0;
                background: white !important;
            }

            /* sembunyikan semua parent langsung */
            body>* {
                visibility: hidden;
            }

            /* ✅ hanya print area */
            #printOnlyHeader,
            #printOnlyHeader *,
            .card-slip,
            .card-slip * {
                visibility: visible;
            }

            /*  HILANGKAN HEADER WEB (INI KUNCI DOUBLE HEADER FIX) */
            .company-header {
                display: none !important;
            }

            /* posisi layout */
            #printOnlyHeader {
                display: block !important;
                visibility: visible !important;
                position: absolute;
                top: 10mm;
                left: 10mm;
                right: 10mm;
                padding-bottom: 8px;
                border-bottom: 2px solid #000;
            }

            .card-slip {
                position: absolute;
                top: 42mm;
                left: 10mm;
                right: 10mm;
                width: calc(210mm - 20mm);
                border: none !important;
                box-shadow: none !important;
            }

            /* UI hide */
            nav,
            aside,
            header,
            footer,
            .sidebar,
            .navbar,
            button,
            .btn,
            .no-print,
            .fa {
                display: none !important;
            }

            .row>* {
                width: 50% !important;
            }

            * {
                font-size: 13px !important;
            }

            .detail-box {
                background: transparent !important;
                border: 1px solid #ddd !important;
            }

            .total-box {
                background: #f2f2f2 !important;
                border: 1px solid #000 !important;
                color: #000 !important;
            }

            .total-nominal {
                font-weight: 800;

                font-size: 18px;
                letter-spacing: 0.5px;
            }

            .text-potongan {
                color: red !important;
            }

            .text-potongan strong {
                color: red !important;
            }


        }
    </style>

    <div class="container py-4">

        {{-- ================= KOP SURAT PRINT ================= --}}
        <div id="printOnlyHeader">
            <div class="d-flex justify-content-between align-items-center">

                <div class="d-flex align-items-center">
                    <img src="{{ asset('build/assets/img/logo.png') }}" style="width:70px; margin-right:15px;">
                    <div>
                        <h4 style="margin:0; font-weight:bold;">
                            PT. MEGAJAYA SARANA NUSANTARA
                        </h4>
                        <small>Gedung Sarana Square Lt.3A Jl. Tebet Barat, Jakarta Selatan</small><br>
                        <small>Email: megajayasarananusantara@gmail.com | Telp: 0811227337</small>
                    </div>
                </div>

                <div class="text-end">
                    <h5 style="margin:0;">SLIP GAJI</h5>
                    <small class="currentTimeDisplay"></small>
                </div>

            </div>
        </div>

        {{-- ================= CARD SLIP ================= --}}
        <div class="card card-slip shadow-sm">

            <div class="card-body p-4">

                {{-- HEADER (WEB ONLY) --}}
                <div class="company-header d-flex justify-content-between align-items-center mb-4">

                    <div class="d-flex align-items-center gap-3">
                        <img src="{{ asset('build/assets/img/logo.png') }}" class="logo-pt">

                        <div>
                            <div class="fw-bold">PT. Megajaya Sarana Nusantara</div>
                            <div style="font-size: 11px;">Gedung Sarana Square Lt.3A Jl. Tebet Barat</div>
                            <div style="font-size: 11px;">megajayasarananusantara@gmail.com</div>
                        </div>
                    </div>

                    <div class="text-end">
                        <div class="fw-bold">SLIP GAJI</div>
                        <small class="currentTimeDisplay"></small>
                    </div>

                </div>

                <h5 class="text-center mb-4 fw-bold">SLIP GAJI KARYAWAN</h5>

                {{-- DETAIL KARYAWAN --}}
                {{-- ================= INFO SLIP GAJI ================= --}}
                <div class="row g-3 mb-4">

                    <div class="col-md-3">
                        <div class="label-title">Nama Karyawan</div>
                        <div class="detail-box">{{ ucwords($salary->employee->fullname) }}</div>
                    </div>

                    <div class="col-md-3">
                        <div class="label-title">Divisi</div>
                        <div class="detail-box">{{ ucwords($salary->employee->division->name) }}</div>
                    </div>

                    <div class="col-md-3">
                        <div class="label-title">Periode Gaji</div>
                        <div class="detail-box">{{ \Carbon\Carbon::parse($salary->date)->translatedFormat('F Y') }}</div>
                    </div>

                    <div class="col-md-3">
                        <div class="label-title">Status</div>
                        <div class="detail-box">{{ ucwords($salary->employee->status) }}</div>
                    </div>

                </div>

                {{-- GAJI --}}
                <div class="row g-4">

                    <div class="col-md-6">
                        <h6 class="fw-bold border-bottom pb-2">PENERIMAAN</h6>

                        <div class="d-flex justify-content-between">
                            <span>Gaji Pokok</span>
                            <strong>
                                Rp. {{ number_format($salary->employee->position->base_salary, 0, ',', '.') }}
                            </strong>
                        </div>

                        <div class="d-flex justify-content-between text-potongan">
                            <span>BPJS Kesehatan</span>
                            <strong>
                                - Rp {{ number_format($salary->bpjs_kesehatan_cuts, 0, ',', '.') }}
                            </strong>
                        </div>

                        <div class="d-flex justify-content-between text-potongan">
                            <span>BPJS Ketenagakerjaan</span>
                            <strong>
                                - Rp {{ number_format($salary->bpjs_ketenagakerjaan_cuts, 0, ',', '.') }}
                            </strong>
                        </div>

                    </div>

                    <div class="col-md-6">
                        <h6 class="fw-bold border-bottom pb-2">POTONGAN</h6>

                        <div class="d-flex justify-content-between text-potongan">
                            <span>Potongan Absen</span>
                            <strong>- Rp. {{ number_format($salary->absent_cuts, 0, ',', '.') }}</strong>
                        </div>

                        <div class="d-flex justify-content-between text-potongan">
                            <span>Potongan Telat</span>
                            <strong>- Rp. {{ number_format($salary->late_cuts, 0, ',', '.') }}</strong>
                        </div>

                        <div class="d-flex justify-content-between text-potongan">
                            <span>PPh 21</span>
                            <strong>- Rp {{ number_format($salary->pph_cuts, 0, ',', '.') }}</strong>
                        </div>

                        <div class="d-flex justify-content-between text-potongan">
                            <span>Potongan Cuti</span>
                            <strong>- Rp {{ number_format($salary->leave_cuts, 0, ',', '.') }}</strong>
                        </div>
                    </div>

                </div>

                {{-- TOTAL --}}
                <div class="total-box mt-4 d-flex justify-content-between">
                    <strong>Total Diterima (Take Home Pay)</strong>

                    <h5 class="mb-0 total-nominal">
                        {{ number_format($totalSalary, 0, ',', '.') }}
                    </h5>
                </div>

                {{-- TTD --}}
                <div class="row mt-5 text-center">

                    <div class="col-6">
                        <p>Diterima Oleh,</p>
                        <br><br>
                        <strong>( {{ $salary->employee->fullname }} )</strong>
                    </div>

                    <div class="col-6">
                        <p>HRD</p>
                        <br><br>
                        <strong>( HRD Manager )</strong>
                    </div>

                </div>

                {{-- BUTTON --}}
                <div class="d-flex justify-content-end gap-2 mt-4 no-print">

                    <a href="{{ route('salary.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fa fa-arrow-left me-2"></i> Kembali
                    </a>

                    <button onclick="window.print()" class="btn btn-danger btn-sm">
                        <i class="fa fa-print me-2"></i> Print
                    </button>

                </div>

            </div>
        </div>

    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const now = new Date();
            const formattedDate = now.toLocaleString('id-ID', {
                day: '2-digit',
                month: 'long',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });

            document.querySelectorAll(".currentTimeDisplay").forEach(el => {
                el.innerText = formattedDate;
            });
        });
    </script>

@endsection
