@extends('layout.dashboard')
@section('header', 'Slip Gaji')
@section('content')

    <style>
        /* Styling Header Tabel Gradasi Merah Profesional */
        #salaryTable thead th {
            background: linear-gradient(180deg, #bc5e6b 0%, #a34a57 100%);
            color: white;
            border: none;
            white-space: nowrap;
            padding: 15px;
        }

        .btn-action {
            border-radius: 6px;
            padding: 6px 10px;
            border: none;
            transition: all 0.2s;
        }

        /* Warna tombol aksi yang soft */
        .btn-view {
            background-color: #e3f2fd;
            color: #1976d2;
        }

        .btn-view:hover {
            background-color: #bbdefb;
        }

        .btn-edit {
            background-color: #fff3e0;
            color: #f57c00;
        }

        .btn-edit:hover {
            background-color: #ffe0b2;
        }

        .btn-delete {
            background-color: #ffebee;
            color: #d32f2f;
        }

        .btn-delete:hover {
            background-color: #ffcdd2;
        }

        /* Badge Divisi */
        .badge-divisi {
            background-color: #f8f9fa;
            color: #6c757d;
            border: 1px solid #dee2e6;
            font-weight: 600;
            padding: 5px 10px;
        }
    </style>

    @if (in_array($userRole, ['hr', 'owner']))
        <div class="card shadow-sm border-0 rounded-4 mb-4">

            <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-3">

                <h5 class="mb-0 fw-semibold d-flex align-items-center">
                    <i class="fa-solid fa-plus-circle me-2"></i>
                    Manajemen Slip Gaji
                </h5>

                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('salary.create') }}" class="btn btn-primary rounded-pill px-4">
                        <i class="fa-solid fa-plus me-2"></i> Tambah Slip
                    </a>

                    <a href="{{ route('salary.generate') }}" class="btn btn-outline-success rounded-pill px-4">
                        <i class="fa-solid fa-arrows-rotate me-2"></i> Generate
                    </a>
                </div>

            </div>

            @if (session('success'))
                <div class="card-body pt-0">
                    <div class="alert alert-success alert-dismissible fade show rounded-3 mt-2" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="card-body pt-0">
                    <div class="alert alert-danger alert-dismissible fade show rounded-3 mt-2" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                </div>
            @endif

        </div>
    @endif

    <div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="card-body p-4">

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">

            <h6 class="fw-semibold mb-0">
                <i class="fa-solid fa-filter me-2"></i>
                Filter Slip Gaji
            </h6>

            <div class="d-flex align-items-center gap-2 flex-wrap">

                {{-- BULAN --}}
                <select id="filterMonth"
                    class="form-select form-select-sm rounded-pill"
                    style="width: 160px;">

                    <option value="">Semua Bulan</option>
                    <option value="01">Januari</option>
                    <option value="02">Februari</option>
                    <option value="03">Maret</option>
                    <option value="04">April</option>
                    <option value="05">Mei</option>
                    <option value="06">Juni</option>
                    <option value="07">Juli</option>
                    <option value="08">Agustus</option>
                    <option value="09">September</option>
                    <option value="10">Oktober</option>
                    <option value="11">November</option>
                    <option value="12">Desember</option>

                </select>


                {{-- TAHUN --}}
                <select id="filterYear"
                    class="form-select form-select-sm rounded-pill"
                    style="width: 130px;">

                    <option value="">Semua Tahun</option>

                    @for ($year = date('Y'); $year >= 2023; $year--)
                        <option value="{{ $year }}">
                            {{ $year }}
                        </option>
                    @endfor

                </select>


                {{-- RESET --}}
                <button type="button"
                    id="resetSalaryFilter"
                    class="btn btn-sm btn-outline-secondary rounded-pill px-3">

                    <i class="fa-solid fa-rotate-left me-1"></i>
                    Reset

                </button>

            </div>

        </div>

    </div>
</div>

    <div class="card shadow border-0">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-semibold mb-0">
                    <i class="fa-solid fa-file-invoice-dollar me-2" style="color: #bc5e6b;"></i>
                    Daftar Slip Gaji
                </h5>
            </div>

            <div class="table-responsive">
                <table id="salaryTable" class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Karyawan</th>
                            <th>Divisi</th>
                            <th>Gaji Pokok</th>
                            <th>Potongan</th>
                            <th>Total Diterima</th>

                            @if (in_array($userRole, ['hr', 'owner', 'employee']))
                                <th class="text-center">Aksi</th>
                            @endif
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($salaries as $salary)
                            <tr
                            data-month="{{ \Carbon\Carbon::parse($salary->date)->format('m') }}"
                            data-year="{{ \Carbon\Carbon::parse($salary->date)->format('Y') }}">
                                <td>{{ $loop->iteration }}</td>
                                <td class="fw-bold text-dark">
                                    {{ ucwords($salary->employee->fullname) }}
                                </td>

                                <td>
                                    <span class="badge badge-divisi">
                                        {{ strtoupper($salary->employee->division->name) }}
                                    </span>
                                </td>

                                <td>Rp {{ number_format($salary->net_salary, 0, ',', '.') }}</td>
                                <td class="text-danger">
                                    Rp {{ number_format($salary->cuts, 0, ',', '.') }}
                                </td>

                                <td class="fw-bold text-success">
                                    Rp {{ number_format($salary->total, 0, ',', '.') }}
                                </td>

                                @if (in_array($userRole, ['hr', 'owner', 'employee']))
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">

                                            {{-- ICON MATA (EMPLOYEE + HR + OWNER) --}}
                                            <a href="{{ route('salary.show', $salary->id) }}"
                                                class="btn btn-action btn-view btn-sm" title="Lihat">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>

                                            {{-- EDIT + DELETE HANYA HR / OWNER --}}
                                            @if (in_array($userRole, ['hr', 'owner']))
                                                <a href="{{ route('salary.edit', $salary->id) }}"
                                                    class="btn btn-action btn-edit btn-sm" title="Edit">
                                                    <i class="fa-solid fa-pen"></i>
                                                </a>

                                                <form action="{{ route('salary.destroy', $salary->id) }}" method="POST"
                                                    class="d-inline" id="deleteForm{{ $salary->id }}">
                                                    @csrf
                                                    @method('DELETE')

                                                    <button class="btn btn-action btn-delete btn-sm" type="button"
                                                        onclick="confirmDelete({{ $salary->id }})" title="Hapus">
                                                        <i class="fa-solid fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif

                                        </div>
                                    </td>
                                @endif

                            </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('DataTables/datatables.min.js') }}"></script>

    <script>
    $(document).ready(function() {

        // =====================================================
        // DATATABLE
        // =====================================================

        const table = $('#salaryTable').DataTable({

            pageLength: 10,

            ordering: true,

            responsive: true,

            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",

                paginate: {
                    previous: "Sebelumnya",
                    next: "Berikutnya",
                },
            },

            @if (in_array($userRole, ['hr', 'owner']))
                columnDefs: [{
                    targets: 6,
                    orderable: false,
                    searchable: false
                }]
            @else
                columnDefs: [{
                    targets: 6,
                    orderable: false,
                    searchable: false
                }]
            @endif
        });


        // =====================================================
        // FILTER BULAN + TAHUN
        // =====================================================

        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {

            // Hanya berlaku untuk salaryTable
            if (settings.nTable.id !== 'salaryTable') {
                return true;
            }

            const row = table.row(dataIndex).node();

            if (!row) {
                return true;
            }

            const rowMonth = row.dataset.month;
            const rowYear = row.dataset.year;

            const selectedMonth = $('#filterMonth').val();
            const selectedYear = $('#filterYear').val();


            // Filter bulan
            const monthMatch =
                selectedMonth === '' ||
                rowMonth === selectedMonth;


            // Filter tahun
            const yearMatch =
                selectedYear === '' ||
                rowYear === selectedYear;


            return monthMatch && yearMatch;

        });


        // =====================================================
        // CHANGE FILTER
        // =====================================================

        $('#filterMonth, #filterYear').on('change', function() {

            table.draw();

        });


        // =====================================================
        // RESET FILTER
        // =====================================================

        $('#resetSalaryFilter').on('click', function() {

            $('#filterMonth').val('');

            $('#filterYear').val('');

            table.draw();

        });

    });
</script>

@endsection
