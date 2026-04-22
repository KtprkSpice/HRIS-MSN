@extends('layout.dashboard');

@section('header', 'Laporan')

@section('content')


    <style>
        body {
            background: #f5f6fa;
        }

        .header-card {
            background: linear-gradient(135deg, #b83e48, #eb8697);
            color: white;
            border-radius: 18px;
        }

        .header-card h4 {
            letter-spacing: 0.5px;
        }

        .header-card .badge {
            border-radius: 20px;
            font-size: 12px;
            padding: 6px 12px;
        }

        .logo-pt {
            width: 42px;
            height: 42px;
            object-fit: contain;
            background: white;
            border-radius: 10px;
            padding: 5px;
        }

        .stat-card {
            border-radius: 18px;
            transition: 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-total {
            background: linear-gradient(135deg, #eef2ff, #e0e7ff);
            border-left: 5px solid #6366f1;
        }

        .stat-gaji {
            background: linear-gradient(135deg, #ecfdf5, #d1fae5);
            border-left: 5px solid #10b981;
        }

        .stat-absen {
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            border-left: 5px solid #f59e0b;
        }

        .stat-karyawan {
            background: linear-gradient(135deg, #f9f1f3, #e2e8f0);
            border-left: 5px solid #ef4444;
        }

        .stat-icon {
            font-size: 24px;
        }

        table thead {
            background: linear-gradient(100deg, #b83e48, #eb8697);
            color: white;
        }

        table tbody tr:hover {
            background: #f8f9fa;
        }

        .status-active {
            background: rgba(16, 185, 129, 0.15);
            color: #059669;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
        }

        .status-inactive {
            background: rgba(239, 68, 68, 0.15);
            color: #dc2626;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
        }

        .name-box {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .name-icon {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
            font-size: 14px;
        }

        .last-update {
            text-align: right;
            opacity: 0.9;
        }

        /* ================= PRINT STYLE ================= */
        .print-header {
            display: none;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        /* kiri */
        .print-header .header-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .print-header img {
            width: 70px;
            height: 70px;
            object-fit: contain;
        }

        .print-header h2 {
            margin: 0;
            font-size: 18px;
            font-weight: 700;
        }

        .print-header p {
            margin: 0;
            font-size: 12px;
            color: #333;
        }

        /* kanan */
        .print-header .header-right {
            text-align: right;
        }

        .print-header .title {
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .print-header .date {
            font-size: 12px;
            margin-top: 5px;
            color: #555;
        }

        /* ================= PRINT MODE ================= */
        @media print {

            body * {
                visibility: hidden;
            }

            #printArea,
            #printArea * {
                visibility: visible;
            }

            .print-header,
            .print-header * {
                visibility: visible;
            }

            #printArea {
                position: absolute;
                left: 0;
                top: 120px;
                width: 100%;
            }

            .print-header {
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                display: block;
            }

            .no-print {
                display: none !important;
            }

            table {
                font-size: 12px;
                width: 20%;
            }

            .card,
            .shadow-sm {
                box-shadow: none !important;
            }
        }

        @media print {

            body * {
                visibility: hidden;
            }

            #printArea,
            #printArea * {
                visibility: visible;
            }

            .print-header,
            .print-header * {
                visibility: visible;
            }

            .print-header {
                display: flex;
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
            }

            #printArea {
                position: absolute;
                top: 110px;
                left: 0;
                width: 100%;
            }

            table {
                width: 100%;
                font-size: 12px;
                border-collapse: collapse;
            }

            th,
            td {
                padding: 6px;
                border: 1px solid #ddd;
            }
        }

        .dt-buttons {
            display: none !important;
        }
    </style>
    <!-- HEADER -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 header-card">
        <div class="card-body p-4 d-flex justify-content-between align-items-center flex-wrap gap-3">

            <div>
                <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
                    <img src="{{ asset('build/assets/img/logo.png') }}" class="logo-pt" alt="Logo PT">
                    Dashboard Report
                </h4>

                <div class="small text-light opacity-75">
                    PT. Megajaya Sarana Nusantara
                </div>

                <div class="mt-2">
                    <span class="badge bg-light text-dark me-2">
                        <i class="fa-solid fa-calendar me-1"></i>
                        Periode: <span id="periode"></span>
                    </span>

                    <span class="badge bg-light text-dark">
                        <i class="fa-solid fa-shield-halved me-1"></i>
                        Owner Dashboard
                    </span>
                </div>
            </div>

            <div class="last-update">
                <div class="small opacity-75">Last Update</div>
                <div class="fw-semibold" id="lastUpdate"></div>
            </div>

        </div>
    </div>

    <!-- STAT -->
    <div class="row g-4 mb-4">

        <div class="col-md-3">
            <div class="card stat-card stat-total shadow-sm p-3">
                <div class="d-flex justify-content-between">
                    <div>
                        <small>Total Cuti</small>
                        <h4>{{ $leaveTotal }}</h4>
                    </div>
                    <div class="stat-icon text-primary">
                        <i class="fa-solid fa-calendar"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stat-card stat-gaji shadow-sm p-3">
                <div class="d-flex justify-content-between">
                    <div>
                        <small>Total Gaji</small>
                        <h4>Rp.{{ number_format($salaries, 0, ',', '.') }}</h4>
                    </div>
                    <div class="stat-icon text-success">
                        <i class="fa-solid fa-money-bill"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stat-card stat-absen shadow-sm p-3">
                <div class="d-flex justify-content-between">
                    <div>
                        <small>Total Absen</small>
                        <h4>{{ $absentTotal }}</h4>
                    </div>
                    <div class="stat-icon text-warning">
                        <i class="fa-solid fa-user-clock"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stat-card stat-karyawan shadow-sm p-3">
                <div class="d-flex justify-content-between">
                    <div>
                        <small>Karyawan Aktif</small>
                        <h4>{{ $activeEmployees }}</h4>
                    </div>
                    <div class="stat-icon text-danger">
                        <i class="fa-solid fa-users"></i>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!-- PRINT HEADER (KHUSUS PRINT SAJA) -->
    <div class="print-header">
        <div class="header-left">
            <img src="{{ asset('build/assets/img/logo.png') }}" alt="Logo">
            <div>
                <h2>PT. Megajaya Sarana Nusantara</h2>
                <p>Gedung Sarana Square Lt.3A Jl. Tebet Barat</p>
                <p>Email: megajayasarananusantara@gmail.com | Telp: 0811227337</p>
            </div>
        </div>

        <div class="header-right">
            <div class="title">LAPORAN DATA KARYAWAN</div>
            <div class="date" id="printDate"></div>
        </div>
    </div>

    <!-- TABLE (PRINT AREA) -->
    <div id="printArea" class="card shadow-sm border-0 rounded-4">
        <div class="card-body table-responsive">

            <table class="table align-middle" id="table">

                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Divisi</th>
                        <th>Gender</th>
                        <th>No Tlp</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Cuti</th>
                        <th>Gaji</th>
                        <th>Absen</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($employees as $employee)
                        <tr>
                            <td>
                                <div class="name-box">
                                    <div class="name-icon"><i class="fa-solid fa-user"></i></div>
                                    {{ ucwords($employee->fullname) }}
                                </div>
                            </td>
                            <td>{{ ucwords($employee->division->name) }}</td>
                            <td>{{ ucwords($employee->gender) }}</td>
                            <td>{{ $employee->phone }}</td>
                            <td>{{ $employee->email }}</td>
                            <td><span @class([
                                'badge rounded-pill px-3 py-2 fw-medium status-badge' => true,
                                'status-active' => $employee->status == 'active',
                                'status-inactive' => $employee->status == 'inactive',
                            ])>
                                    {{ ucwords($employee->status) }}
                                </span></td>
                            <td>{{ $employee->leave_total ?: '-' }}</td>
                            <td>{{ number_format($employee->salary_total, 0, ',', '.') }}</td>
                            <td>{{ $employee->absent_total ?: '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>

            </table>

        </div>
    </div>


    <!-- PRINT BUTTON (BOTTOM ONLY) -->
    <div class="d-flex justify-content-end mt-5 mb-2 no-print gap-3">

        <button id="btnExcel" class="btn btn-success">
            <i class="fa fa-file-excel me-1"></i> Export Excel
        </button>

        <button onclick="window.print()" class="btn btn-danger">
            <i class="fa fa-print me-1"></i> Print
        </button>

    </div>

    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('DataTables/datatables.min.js') }}"></script>

    <script>
        let table;

        $(document).ready(function() {
            // Inisialisasi DataTable
            table = $('#table').DataTable({
                dom: 'Bfrtip',
                buttons: [{
                    extend: 'excelHtml5',
                    text: '<i class="fa fa-file-excel"></i> Export Excel',
                    title: 'Laporan Karyawan PT Megajaya Sarana Nusantara',
                    className: 'btn btn-success'
                }],
                pageLength: 10,
                ordering: true,
                responsive: true
            });

            // Tombol export manual
            $('#btnExcel').on('click', function(e) {
                e.preventDefault();
                table.button('.buttons-excel').trigger();
            });
        });

        // Set tanggal
        document.addEventListener("DOMContentLoaded", function() {
            const bulan = [
                "Januari", "Februari", "Maret", "April", "Mei", "Juni",
                "Juli", "Agustus", "September", "Oktober", "November", "Desember"
            ];

            const now = new Date();

            const periodeElement = document.getElementById("periode");
            if (periodeElement) {
                periodeElement.innerText = bulan[now.getMonth()] + " " + now.getFullYear();
            }

            const lastUpdateElement = document.getElementById("lastUpdate");
            if (lastUpdateElement) {
                lastUpdateElement.innerText = now.toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'long',
                    year: 'numeric'
                }) + " " + now.toLocaleTimeString('id-ID', {
                    hour: '2-digit',
                    minute: '2-digit'
                });
            }

            const printDateElement = document.getElementById("printDate");
            if (printDateElement) {
                printDateElement.innerText = "Dicetak pada: " + now.toLocaleString('id-ID');
            }
        });

        // Handler untuk print - FIX
        window.onbeforeprint = function() {
            // Simpan state DataTable
            if ($.fn.DataTable.isDataTable('#table')) {
                var tablePrint = $('#table').DataTable();
                tablePrint.destroy();
            }
        };

        window.onafterprint = function() {
            // Re-inisialisasi DataTable setelah print selesai
            if (!$.fn.DataTable.isDataTable('#table')) {
                table = $('#table').DataTable({
                    dom: 'Bfrtip',
                    buttons: [{
                        extend: 'excelHtml5',
                        text: '<i class="fa fa-file-excel"></i> Export Excel',
                        title: 'Laporan Karyawan PT Megajaya Sarana Nusantara',
                        className: 'btn btn-success'
                    }],
                    pageLength: 10,
                    ordering: true,
                    responsive: true
                });
            }
        };
    </script>
@endsection
