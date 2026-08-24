@extends('layout.dashboard')

@section('header', 'Laporan')

@section('content')


    <style>
        body {
            background: #f5f6fa;
        }

        /* Checkbox Filter Style */
        .filter-container {
            background: #f8f9fa;
            border-radius: 16px;
            padding: 20px;
        }

        .filter-group {
            background: #fff;
            border-radius: 12px;
            padding: 15px;
            border: 1px solid #e9ecef;
        }

        .filter-title {
            display: block;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 10px;
            color: #343a40;
        }

        .checkbox-list {
            display: flex;
            flex-wrap: wrap;
            gap: 10px 20px;
        }

        .custom-checkbox {
            position: relative;
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            font-size: 14px;
            color: #495057;
        }

        .custom-checkbox input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
        }

        .checkmark {
            width: 18px;
            height: 18px;
            border: 2px solid #ced4da;
            border-radius: 5px;
            display: inline-block;
            position: relative;
            transition: all .2s ease;
        }

        .custom-checkbox input:checked+.checkmark {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }

        .custom-checkbox input:checked+.checkmark::after {
            content: '';
            position: absolute;
            left: 4px;
            top: 1px;
            width: 5px;
            height: 9px;
            border: solid white;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
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
            width: 42px;
            height: 42px;
            border-radius: 50%;
            overflow: hidden;
            /* INI KUNCI BIAR BULAT RAPI */
            flex-shrink: 0;
            /* BIAR GA KEPENCET DI TABLE */
            background: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
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
    @php
        $reportMonths = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember',
        ];
    @endphp

    <div class="card border-0 shadow-sm rounded-4 mb-4 header-card">
        <div class="card-body p-4 d-flex justify-content-between align-items-center flex-wrap gap-3">

            <div>
                <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
                    <img src="{{ asset('build/img/logo.png') }}" class="logo-pt" alt="Logo PT">
                    Dashboard Report
                </h4>

                <div class="small text-light opacity-75">
                    PT. Megajaya Sarana Nusantara
                </div>

                <div class="mt-2">
                    <span class="badge bg-light text-dark me-2">
                        <i class="fa-solid fa-calendar me-1"></i>
                        Periode: {{ $reportMonths[$selectedMonth] }} {{ $selectedYear }}
                    </span>

                    <span class="badge bg-light text-dark">
                        <i class="fa-solid fa-shield-halved me-1"></i>
                        Dashboard Manajemen
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
            <img src="{{ asset('build/img/logo.png') }}" alt="Logo">
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

    <div class="filter-container mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-semibold mb-0">
                <i class="fa-solid fa-filter me-2"></i>
                Filter Karyawan
            </h6>

            <button type="button" id="resetFilter" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                <i class="fa-solid fa-rotate-left me-1"></i>
                Reset
            </button>
        </div>

        <div class="row g-3">

            {{-- PERIODE --}}
            <div class="col-md-4">
                <div class="filter-group">
                    <label class="filter-title">Periode Laporan</label>

                    <form method="GET" action="{{ route('report.index') }}" class="d-flex gap-2">
                        <select name="month" class="form-select form-select-sm rounded-pill">
                            @foreach ($reportMonths as $monthValue => $monthName)
                                <option value="{{ $monthValue }}" @selected($selectedMonth === $monthValue)>
                                    {{ $monthName }}
                                </option>
                            @endforeach
                        </select>

                        <select name="year" class="form-select form-select-sm rounded-pill">
                            @for ($year = now()->year + 1; $year >= 2023; $year--)
                                <option value="{{ $year }}" @selected($selectedYear === (string) $year)>
                                    {{ $year }}
                                </option>
                            @endfor
                        </select>

                        <button class="btn btn-sm btn-primary rounded-pill px-3" type="submit">
                            Terapkan
                        </button>
                    </form>
                </div>
            </div>

            {{-- STATUS --}}
            <div class="col-md-4">
                <div class="filter-group">
                    <label class="filter-title">Status</label>

                    <div class="checkbox-list">

                        <label class="custom-checkbox">
                            <input type="checkbox" class="filter-checkbox single-filter" data-filter="status" name="status"
                                value="active">

                            <span class="checkmark"></span>
                            Aktif
                        </label>

                        <label class="custom-checkbox">
                            <input type="checkbox" class="filter-checkbox single-filter" data-filter="status" name="status"
                                value="inactive">

                            <span class="checkmark"></span>
                            Nonaktif
                        </label>

                    </div>
                </div>
            </div>

            {{-- GENDER --}}
            <div class="col-md-4">
                <div class="filter-group">
                    <label class="filter-title">Gender</label>

                    <div class="checkbox-list">

                        <label class="custom-checkbox">
                            <input type="checkbox" class="filter-checkbox single-filter" data-filter="gender" name="gender"
                                value="laki-laki">

                            <span class="checkmark"></span>
                            Laki-laki
                        </label>

                        <label class="custom-checkbox">
                            <input type="checkbox" class="filter-checkbox single-filter" data-filter="gender" name="gender"
                                value="perempuan">

                            <span class="checkmark"></span>
                            Perempuan
                        </label>

                    </div>
                </div>
            </div>

            {{-- DIVISI --}}
            <div class="col-md-4">
                <div class="filter-group">
                    <label class="filter-title">Divisi</label>

                    <div class="checkbox-list">
                        @foreach ($divisions as $division)
                            <label class="custom-checkbox">
                                <input type="checkbox" class="filter-checkbox" data-filter="division"
                                    value="{{ strtolower($division->name) }}">

                                <span class="checkmark"></span>
                                {{ ucwords($division->name) }}
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

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
                        <th>Penempatan</th>
                        <th>Status</th>
                        <th>Cuti</th>
                        <th>Gaji</th>
                        <th>Absen</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($employees as $employee)
                        <tr data-gender="{{ strtolower($employee->gender) }}"
                            data-status="{{ strtolower($employee->status) }}"
                            data-division="{{ strtolower($employee->division->name) }}">
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
                            <td>
                                @forelse ($employee->tasks as $task)
                                    <div>{{ $task->name ?? '-' }}</div>
                                @empty
                                    <div>-</div>
                                @endforelse
                            </td>
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

        <a href="{{ route('report.export', ['month' => $selectedMonth, 'year' => $selectedYear]) }}" class="btn btn-success" id="btnExcel">
            <i class="fa fa-file-excel me-1"></i> Export Excel
        </a>

        <button onclick="window.print()" class="btn btn-danger">
            <i class="fa fa-print me-1"></i> Print
        </button>

    </div>

    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('DataTables/datatables.min.js') }}"></script>

    <script>
        let table;

        $(document).ready(function() {
            // Inisialisasi DataTable tanpa buttons extension
            table = $('#table').DataTable({
                pageLength: 10,
                ordering: true,
                responsive: true,
                language: {
                    "sProcessing": "Memproses...",
                    "sLengthMenu": "Tampilkan _MENU_ entri",
                    "sZeroRecords": "Tidak ada entri yang cocok ditemukan",
                    "sInfo": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                    "sInfoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
                    "sInfoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
                    "sSearch": "Cari:",
                    "oPaginate": {
                        "sFirst": "Pertama",
                        "sPrevious": "Sebelumnya",
                        "sNext": "Berikutnya",
                        "sLast": "Terakhir"
                    }
                }
            });
        });


        // Set tanggal
        document.addEventListener("DOMContentLoaded", function() {
            const bulan = [
                "Januari", "Februari", "Maret", "April", "Mei", "Juni",
                "Juli", "Agustus", "September", "Oktober", "November", "Desember"
            ];

            const now = new Date();

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
                    pageLength: 10,
                    ordering: true,
                    responsive: true,
                    language: {
                        "sProcessing": "Memproses...",
                        "sLengthMenu": "Tampilkan _MENU_ entri",
                        "sZeroRecords": "Tidak ada entri yang cocok ditemukan",
                        "sInfo": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                        "sInfoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
                        "sInfoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
                        "sSearch": "Cari:",
                        "oPaginate": {
                            "sFirst": "Pertama",
                            "sPrevious": "Sebelumnya",
                            "sNext": "Berikutnya",
                            "sLast": "Terakhir"
                        }
                    }
                });
            }
        };

        document.querySelectorAll('.single-filter').forEach(checkbox => {

            checkbox.addEventListener('change', function() {

                if (this.checked) {

                    const filterType = this.dataset.filter;

                    document.querySelectorAll(
                        `.single-filter[data-filter="${filterType}"]`
                    ).forEach(otherCheckbox => {

                        if (otherCheckbox !== this) {
                            otherCheckbox.checked = false;
                        }

                    });

                }

                table.draw();

            });

        });


        // =========================
        // CUSTOM DATATABLE FILTER
        // =========================

        DataTable.ext.search.push(function(settings, data, dataIndex) {

            if (settings.nTable.id !== 'table') {
                return true;
            }

            const row = settings.aoData[dataIndex].nTr;

            if (!row) {
                return true;
            }

            const status = row.dataset.status;
            const gender = row.dataset.gender;
            const division = row.dataset.division;


            // Ambil filter
            const selectedStatus =
                document.querySelector(
                    '.single-filter[data-filter="status"]:checked'
                )?.value.toLowerCase() || null;


            const selectedGender =
                document.querySelector(
                    '.single-filter[data-filter="gender"]:checked'
                )?.value.toLowerCase() || null;


            const selectedDivisions =
                Array.from(
                    document.querySelectorAll(
                        '.filter-checkbox[data-filter="division"]:checked'
                    )
                ).map(checkbox =>
                    checkbox.value.toLowerCase()
                );


            // =========================
            // FILTER STATUS
            // =========================

            const statusMatch = !selectedStatus ||
                status === selectedStatus;


            // =========================
            // FILTER GENDER
            // =========================

            const genderMatch = !selectedGender ||
                gender === selectedGender;


            // =========================
            // FILTER DIVISI
            // =========================

            const divisionMatch =
                selectedDivisions.length === 0 ||
                selectedDivisions.includes(division);


            return (
                statusMatch &&
                genderMatch &&
                divisionMatch
            );

        });


        // =========================
        // DIVISION CHECKBOX
        // =========================

        document.querySelectorAll(
            '.filter-checkbox[data-filter="division"]'
        ).forEach(checkbox => {

            checkbox.addEventListener('change', function() {
                table.draw();
            });

        });


        // =========================
        // RESET
        // =========================
        const checkboxes = document.querySelectorAll('.filter-checkbox');
        const resetButton = document.getElementById('resetFilter');

        resetButton.addEventListener('click', function() {

            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
            });

            table.draw();

        });
    </script>
@endsection
