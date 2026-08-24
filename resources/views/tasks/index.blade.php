@extends('layout.dashboard')
@section('header', 'Tugas')

@section('content')

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

        /* ========================= */
        /* MODERN TABLE STYLE       */
        /* ========================= */

        /* ============================= */
        /* TUGAS TABLE - DASHBOARD STYLE */
        /* ============================= */

        /* HEADER GRADIENT */
        #tugasTable thead {
            background: linear-gradient(100deg, #b83e48 0%, #eb8697 100%) !important;
        }

        #tugasTable thead th {
            background: linear-gradient(100deg, #b83e48 0%, #eb8697 100%) !important;
            color: #ffffff !important;
            font-weight: 600;
            border-color: transparent !important;
            letter-spacing: 0.5px;
            padding: 18px 16px;
        }

        /* BODY ROW */
        #tugasTable tbody tr {
            background: #ffffff;
            transition: all 0.2s ease;
        }

        /* Alternating row (optional biar lebih hidup) */
        #tugasTable tbody tr:nth-child(even) {
            background: #fdf2f4;
        }

        /* Hover effect */
        #tugasTable tbody tr:hover {
            background-color: #f8f9fa;
        }

        .status-badge {
            font-size: 12px;
        }

        .status-done {
            background: #dcfce7;
            color: #166534;
        }

        .status-onduty {
            background: #dbeafe;
            color: #1e3a8a;
        }

        .status-pending {
            background: #fef9c3;
            color: #854d0e;
        }

        .action-wrapper {
            display: flex;
            gap: 6px;
        }

        .btn-action {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            font-size: 13px;
        }

        .btn-view {
            background: #e0f2fe;
            color: #0369a1;
        }

        .btn-edit {
            background: #fef3c7;
            color: #92400e;
        }

        .btn-delete {
            background: #fee2e2;
            color: #991b1b;
        }

        .status-badge {
            border: none;
            border-radius: 50rem !important;
            font-size: 12px;
            cursor: pointer;
            min-width: 120px;
        }

        .status-done {
            background: #dcfce7 !important;
            color: #166534 !important;
        }

        .status-onduty {
            background: #dbeafe !important;
            color: #1e3a8a !important;
        }

        .status-pending {
            background: #fef9c3 !important;
            color: #854d0e !important;
        }
    </style>

    @if (in_array($userRole, ['hr', 'owner']))
        <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-body p-4">

                <div class="d-flex justify-content-between align-items-center flex-wrap gap-5">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fa-solid fa-plus-circle me-2 text-dark"></i>
                        Manajemen Tugas
                    </h5>

                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('task.create') }}" class="btn btn-primary rounded-pill px-4">
                            <i class="fa-solid fa-plus me-2"></i> Tambah
                        </a>

                        <a href="{{ route('qr.generate') }}" class="btn btn-outline-primary rounded-pill px-4">
                            Generate QR
                        </a>
                    </div>
                </div>

            </div>
        </div>
    @endif

    {{-- Filter --}}

    <div class="filter-container mb-4">

        <div class="d-flex justify-content-between align-items-center mb-3">

            <h6 class="fw-semibold mb-0">
                <i class="fa-solid fa-filter me-2"></i>
                Filter Tugas
            </h6>

            <button type="button" id="resetFilter" class="btn btn-sm btn-outline-secondary rounded-pill px-3">

                <i class="fa-solid fa-rotate-left me-1"></i>
                Reset

            </button>

        </div>


        <div class="row g-3">

            <div class="col-md-4">

                <div class="filter-group">

                    <label class="filter-title">
                        Status
                    </label>

                    <div class="checkbox-list">

                        <label class="custom-checkbox">

                            <input type="checkbox" class="filter-checkbox single-filter" data-filter="status"
                                value="pending">

                            <span class="checkmark"></span>

                            Pending

                        </label>


                        <label class="custom-checkbox">

                            <input type="checkbox" class="filter-checkbox single-filter" data-filter="status"
                                value="on duty">

                            <span class="checkmark"></span>

                            On Duty

                        </label>


                        <label class="custom-checkbox">

                            <input type="checkbox" class="filter-checkbox single-filter" data-filter="status"
                                value="done">

                            <span class="checkmark"></span>

                            Done

                        </label>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <!-- TABLE -->
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-6">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-semibold mb-0">
                    <i class="fa-solid fa-list-check me-2" style="color: #bc5e6b;"></i>
                    Daftar Tugas
                </h5>
            </div>

            <div class="table-responsive">
                <table id="tugasTable" class="table align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Nama Perusahaan</th>
                            <th>Deskripsi</th>
                            <th>Tanggal Mulai</th>
                            <th>Tanggal Selesai</th>
                            <th>Status</th>
                            <th>Show QR</th>
                            <th>Presensi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($tasks as $task)
                            <tr class="task-row" data-status = "{{ strtolower($task->status) }}">

                                <td class="fw-semibold">
                                    {{ ucwords($task->name) }}
                                </td>

                                <td class="text-muted">
                                    {{ ucwords(Str::limit($task->description, 50)) }}
                                </td>

                                <td data-search="{{ Carbon\Carbon::parse($task->start_time)->translatedFormat('d F Y') }}"
                                    data-order="{{ $task->start_time }}">
                                    {{ Carbon\Carbon::parse($task->start_time)->format('d M Y') }}
                                </td>

                                <td data-search="{{ Carbon\Carbon::parse($task->end_time)->format('d F Y') }}"
                                    data-order="{{ $task->end_time }}">
                                    {{ Carbon\Carbon::parse($task->end_time)->format('d M Y') }}
                                </td>

                                @if (in_array($userRole, ['hr', 'owner']))
                                    <td>
                                        <select onchange="changeStatus(this)" @class([
                                            'form-select form-select-sm fw-medium text-center status-badge' => true,
                                            'status-done' => $task->status == 'done',
                                            'status-onduty' => $task->status == 'on duty',
                                            'status-pending' => $task->status == 'pending',
                                        ])>

                                            <option value="{{ route('task.pending', $task->id) }}"
                                                {{ $task->status == 'pending' ? 'selected' : '' }}>
                                                Pending
                                            </option>

                                            <option value="{{ route('task.onduty', $task->id) }}"
                                                {{ $task->status == 'on duty' ? 'selected' : '' }}>
                                                On Duty
                                            </option>

                                            <option value="{{ route('task.done', $task->id) }}"
                                                {{ $task->status == 'done' ? 'selected' : '' }}>
                                                Done
                                            </option>

                                        </select>
                                    </td>
                                @else
                                    <td>
                                        <span onchange="changeStatus(this)" @class([
                                            'form-select form-select-sm fw-medium text-center status-badge' => true,
                                            'status-done' => $task->status == 'done',
                                            'status-onduty' => $task->status == 'on duty',
                                            'status-pending' => $task->status == 'pending',
                                        ])>
                                            {{ $task->status }}
                                        </span>
                                    </td>
                                @endif

                                <!-- STATUS -->


                                <!-- SHOW QR (BARU) -->
                                <td>
                                    <a href="{{ route('qr.show', $task->id) }}"
                                        class="btn btn-sm btn-dark rounded-3 btn-show-qr">
                                        <i class="fa-solid fa-qrcode"></i>
                                    </a>
                                </td>

                                <!-- PRESENSI (TETAP ASLI, JANGAN DIUBAH) -->
                                <td>
                                    @if ($task->status == 'on duty')
                                        <a href="{{ route('presences.scan', $task->id) }}"
                                            class="btn btn-sm btn-info text-white rounded-3">
                                            <i class="fa-solid fa-qrcode me-1"></i> Presensi
                                        </a>
                                    @else
                                        <button class="btn btn-sm btn-secondary rounded-3">
                                            Presensi
                                        </button>
                                    @endif
                                </td>

                                <td>
                                    <div class="action-wrapper">

                                        <a href="{{ route('task.show', $task->id) }}" class="btn-action btn-view">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                        @if (in_array($userRole, ['hr', 'owner']))
                                            <a href="{{ route('task.export', $task->id) }}" class="btn-action btn-view"
                                                title="Export Excel Penugasan">
                                                <i class="fa-solid fa-file-excel"></i>
                                            </a>
                                            <a href="{{ route('task.edit', $task->id) }}" class="btn-action btn-edit">
                                                <i class="fa-solid fa-pen"></i>
                                            </a>
                                        @endif
                                        @if ($userRole == 'owner')
                                            <form action="{{ route('task.destroy', $task->id) }}" method="POST"
                                                id="deleteForm{{ $task->id }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" onclick="confirmDelete({{ $task->id }})"
                                                    class="btn-action btn-delete">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>

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
        let table = new DataTable('#tugasTable', {
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                paginate: {
                    previous: "Sebelumnya",
                    next: "Berikutnya",
                },
            },
            @if ($userRole === 'owner')
                columnDefs: [{
                    targets: 7,
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

        function confirmDelete(id) {
            Swal.fire({
                title: "Yakin hapus?",
                text: "Data tidak dapat dikembalikan",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                confirmButtonText: "Hapus"
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('deleteForm' + id).submit();
                }
            });
        }

        function changeStatus(select) {
            window.location.href = select.value;
        }

        document.addEventListener('DOMContentLoaded', function() {

            const checkboxes = document.querySelectorAll('.filter-checkbox');
            const resetButton = document.getElementById('resetFilter');


            // =====================================================
            // STATUS - HANYA BOLEH PILIH SATU
            // =====================================================

            document.querySelectorAll('.single-filter').forEach(checkbox => {

                checkbox.addEventListener('change', function() {

                    if (this.checked) {

                        document.querySelectorAll('.single-filter')
                            .forEach(otherCheckbox => {

                                if (otherCheckbox !== this) {
                                    otherCheckbox.checked = false;
                                }

                            });

                    }

                    table.draw();

                });

            });


            // =====================================================
            // CUSTOM DATATABLE FILTER
            // =====================================================

            DataTable.ext.search.push(function(settings, data, dataIndex) {

                // Hanya untuk tabel tugas
                if (settings.nTable.id !== 'tugasTable') {
                    return true;
                }


                const row = settings.aoData[dataIndex].nTr;

                if (!row) {
                    return true;
                }


                // Status dari database
                const status = row.dataset.status;


                // Status yang dipilih
                const selectedStatus =
                    document.querySelector(
                        '.single-filter[data-filter="status"]:checked'
                    )?.value.toLowerCase() || null;


                // Tidak ada filter
                if (!selectedStatus) {
                    return true;
                }


                // Cocokkan status
                return status === selectedStatus;

            });


            // =====================================================
            // RESET
            // =====================================================

            resetButton.addEventListener('click', function() {

                checkboxes.forEach(checkbox => {
                    checkbox.checked = false;
                });

                table.draw();

            });

        });
    </script>

@endsection
