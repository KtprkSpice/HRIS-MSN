@extends('layout.dashboard')
@section('header', 'Daftar Karyawan')
@section('content')

    <!-- ===================== -->
    <!-- STATISTIK MODERN -->

    <div class="row mb-4 g-4">

        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 stat-card stat-total">
                <div class="card-body d-flex justify-content-between align-items-center p-4">
                    <div>
                        <p class="stat-label mb-1">Total Karyawan</p>
                        <h2 class="fw-bold mb-0">{{ $countTotalEmployee }}</h2>
                    </div>
                    <div class="stat-icon">
                        <i class="fa-solid fa-users"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 stat-card stat-active">
                <div class="card-body d-flex justify-content-between align-items-center p-4">
                    <div>
                        <p class="stat-label mb-1">Karyawan Aktif</p>
                        <h2 class="fw-bold mb-0">
                            {{ $countActiveEmployee }}
                        </h2>
                    </div>
                    <div class="stat-icon">
                        <i class="fa-solid fa-user-check"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 stat-card stat-inactive">
                <div class="card-body d-flex justify-content-between align-items-center p-4">
                    <div>
                        <p class="stat-label mb-1">Karyawan Nonaktif</p>
                        <h2 class="fw-bold mb-0">
                            {{ $countNonActiveEmployee }}
                        </h2>
                    </div>
                    <div class="stat-icon">
                        <i class="fa-solid fa-user-slash"></i>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- ===================== -->
    <!-- CARD TAMBAH -->

    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-semibold">
                <i class="fa-solid fa-users me-2"></i> Manajemen Karyawan
            </h5>

            <a href="{{ route('employee.create') }}" class="btn btn-primary rounded-pill px-4">
                <i class="fa-solid fa-plus me-2"></i> Tambah
            </a>
        </div>
    </div>

    {{-- Filter --}}

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

        {{-- STATUS --}}
   <div class="col-md-4">
    <div class="filter-group">
        <label class="filter-title">Status</label>

        <div class="checkbox-list">

            <label class="custom-checkbox">
                <input type="checkbox"
                    class="filter-checkbox single-filter"
                    data-filter="status"
                    name="status"
                    value="active">

                <span class="checkmark"></span>
                Aktif
            </label>

            <label class="custom-checkbox">
                <input type="checkbox"
                    class="filter-checkbox single-filter"
                    data-filter="status"
                    name="status"
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
                <input type="checkbox"
                    class="filter-checkbox single-filter"
                    data-filter="gender"
                    name="gender"
                    value="laki-laki">

                <span class="checkmark"></span>
                Laki-laki
            </label>

            <label class="custom-checkbox">
                <input type="checkbox"
                    class="filter-checkbox single-filter"
                    data-filter="gender"
                    name="gender"
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
                            <input type="checkbox"
                                class="filter-checkbox"
                                data-filter="division"
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

    <!-- ===================== -->
    <!-- TABEL -->

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-semibold mb-0">Daftar Karyawan</h5>
            </div>

            <div class="table-responsive">
                <table id="leaveTable" class="table align-middle mb-0">
                    <thead class="bg-primary">
                        <tr>
                            <th>Nama Karyawan</th>
                            <th>No.Telpon</th>
                            <th>Email</th>
                            <th>Divisi</th>
                            <th>Gender</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($employees as $employee)
                            <tr class="employee-row"
                            data-status="{{ strtolower($employee->status) }}"
                            data-division="{{ strtolower($employee->division->name) }}"
                            data-gender="{{ strtolower($employee->gender) }}">

                                <td>
                                    <div class="d-flex align-items-center gap-3">

                                        @if ($employee->photo)
                                            <img src="{{ asset('storage/employees/' . $employee->photo) }}"
                                                class="avatar-photo" alt="Profile {{ $employee->fullname }}">
                                        @else
                                            <div class="avatar-placeholder">
                                                <i class="fa-solid fa-user"></i>
                                            </div>
                                        @endif

                                        <div class="fw-semibold text-dark">
                                            {{ ucwords($employee->fullname) }}
                                        </div>

                                    </div>
                                </td>

                                <td>{{ $employee->phone }}</td>
                                <td class="text-muted">{{ $employee->email }}</td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold">{{ ucwords($employee->division->name) }}</span>
                                        <span class="fw-light">{{ ucwords($employee->position->name) }}</span>
                                    </div>
                                </td>
                                <td>{{ ucwords($employee->gender) }}</td>

                                <td>
                                    <span @class([
                                        'badge rounded-pill px-3 py-2 fw-medium status-badge' => true,
                                        'status-active' => $employee->status == 'active',
                                        'status-inactive' => $employee->status == 'inactive',
                                    ])>
                                        {{ ucwords($employee->status) }}
                                    </span>
                                </td>

                                <!-- AKSI PRESISI -->
                                <td>
                                    <div class="action-wrapper">
                                        <a href="{{ route('employee.edit', $employee->id) }}" class="btn-action btn-edit">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>

                                        <form action="{{ route('employee.destroy', $employee->id) }}" method="POST"
                                            id="deleteForm{{ $employee->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" onclick="confirmDelete({{ $employee->id }})"
                                                class="btn-action btn-delete">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <!-- ===================== -->
    <!-- STYLE -->

    <style>

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

.custom-checkbox input:checked + .checkmark {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.custom-checkbox input:checked + .checkmark::after {
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

        .status-badge {
            font-size: 13px;
            backdrop-filter: blur(6px);
            border: 1px solid transparent;
        }

        /* Active - Hijau Transparan Premium */
        .status-active {
            background: rgba(16, 185, 129, 0.15);
            color: #059669;
            border-color: rgba(16, 185, 129, 0.30);
        }

        /* Inactive - Merah Transparan Elegan */
        .status-inactive {
            background: rgba(239, 68, 68, 0.15);
            color: #dc2626;
            border-color: rgba(239, 68, 68, 0.30);
        }

        .stat-card {
            transition: all 0.3s ease;
            color: #2c3e50;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.06);
        }

        .stat-total {
            background: linear-gradient(135deg, #eef2ff, #e0e7ff);
            border-left: 4px solid #6366f1;
        }

        .stat-active {
            background: linear-gradient(135deg, #ecfdf5, #d1fae5);
            border-left: 4px solid #10b981;
        }

        .stat-inactive {
            background: linear-gradient(135deg, #f9f1f3, #e2e8f0);
            border-left: 4px solid #bd2727;
        }

        .stat-label {
            font-size: 14px;
            color: #64748b;
            font-weight: 500;
        }

        .stat-icon {
            width: 55px;
            height: 55px;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.6);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }

        #leaveTable th,
        #leaveTable td {
            padding: 16px;
            font-size: 14px;
        }

        #leaveTable thead {
            background: linear-gradient(100deg, #b83e48 0%, #eb8697 100%) !important;
        }

        #leaveTable thead th {
            background: linear-gradient(100deg, #b83e48 0%, #eb8697 100%) !important;
            color: white !important;
            font-weight: 600;
            border-color: transparent !important;
            letter-spacing: 0.5px;
        }

        #leaveTable tbody tr:hover {
            background-color: #f8f9fa;
        }

        .avatar-img {
            transition: 0.2s ease;
        }

        tr:hover .avatar-img {
            transform: scale(1.05);
        }

        .avatar-initial {
            width: 48px;
            height: 48px;
            background: #e9ecef;
            color: #495057;
            font-weight: 600;
            font-size: 18px;
        }

        /* ===================== */
        /* AKSI ICON PRESISI */
        /* ===================== */

        .action-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }

        .btn-action {
            width: 38px;
            height: 38px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            background: #f1f5f9;
            transition: all 0.2s ease-in-out;
        }

        .btn-edit i {
            color: #f59e0b;
            font-size: 15px;
        }

        .btn-delete i {
            color: #ef4444;
            font-size: 15px;
        }

        .btn-action:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.08);
            background: #e2e8f0;
        }

        @media (max-width: 768px) {
            .btn-action {
                width: 34px;
                height: 34px;
            }
        }

        .table-responsive {
            border-radius: 14px;
            overflow: hidden;
        }

        /* ========================= */
        /* AVATAR FOTO PRESISI       */
        /* ========================= */

        .avatar-photo {
            width: 50px;
            height: 50px;
            min-width: 50px;
            border-radius: 50%;
            object-fit: cover;
            object-position: center;
            display: block;
            border: 2px solid #ffffff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
            transition: all 0.25s ease;
        }

        /* Avatar Default (Icon Orang) */
        .avatar-placeholder {
            width: 50px;
            height: 50px;
            min-width: 50px;
            border-radius: 50%;
            background: #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: #64748b;
            border: 2px solid #ffffff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
        }

        /* Hover effect */
        tr:hover .avatar-photo,
        tr:hover .avatar-placeholder {
            transform: scale(1.07);
        }

        /* Responsive */
        @media (max-width: 768px) {

            .avatar-photo,
            .avatar-placeholder {
                width: 42px;
                height: 42px;
                min-width: 42px;
                font-size: 18px;
            }

            /* Membuat tabel bisa digeser horizontal pada mobile */
            .table-responsive {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                /* Smooth scrolling pada iOS */
            }

            /* Pastikan tabel tidak terlalu lebar */
            #leaveTable {
                min-width: 800px;
                /* Atur min-width agar tabel bisa digeser */
                font-size: 12px;
                /* Perkecil font untuk menghemat ruang */
            }

            #leaveTable th,
            #leaveTable td {
                padding: 8px;
                /* Kurangi padding untuk menghemat ruang */
                white-space: nowrap;
                /* Mencegah teks wrap */
            }

            /* Sembunyikan scrollbar default dan tambahkan custom jika perlu */
            .table-responsive::-webkit-scrollbar {
                height: 8px;
            }

            .table-responsive::-webkit-scrollbar-track {
                background: #f1f1f1;
                border-radius: 10px;
            }

            .table-responsive::-webkit-scrollbar-thumb {
                background: #c1c1c1;
                border-radius: 10px;
            }

            .table-responsive::-webkit-scrollbar-thumb:hover {
                background: #a8a8a8;
            }
        }
    </style>

    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('DataTables/datatables.min.js') }}"></script>

    <script>
        let table = new DataTable('#leaveTable', {
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                paginate: {
                    previous: "Sebelumnya",
                    next: "Berikutnya",
                },
            },
            columnDefs: [{
                targets: 6,
                orderable: false,
                searchable: false
            }]
        });

        // Filter 
       document.addEventListener('DOMContentLoaded', function () {



    const checkboxes = document.querySelectorAll('.filter-checkbox');
    const resetButton = document.getElementById('resetFilter');


    // =========================
    // SINGLE FILTER
    // STATUS & GENDER
    // =========================

    document.querySelectorAll('.single-filter').forEach(checkbox => {

        checkbox.addEventListener('change', function () {

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

    DataTable.ext.search.push(function (settings, data, dataIndex) {

        if (settings.nTable.id !== 'leaveTable') {
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

        const statusMatch =
            !selectedStatus ||
            status === selectedStatus;


        // =========================
        // FILTER GENDER
        // =========================

        const genderMatch =
            !selectedGender ||
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

        checkbox.addEventListener('change', function () {
            table.draw();
        });

    });


    // =========================
    // RESET
    // =========================

    resetButton.addEventListener('click', function () {

        checkboxes.forEach(checkbox => {
            checkbox.checked = false;
        });

        table.draw();

    });

});
    </script>

@endsection
