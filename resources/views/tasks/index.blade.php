@extends('layout.dashboard')
@section('header', 'Tugas')

@section('content')

    <style>
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
    </style>

    @if (in_array($userRole, ['hr', 'owner']))
        <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-body p-5">

                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="fw-semibold mb-0">
                        <i class="fa-solid fa-plus-circle me-2 text-danger"></i>
                        Manajemen Tugas
                    </h5>

                    <div class="d-flex gap-2">
                        <a href="{{ route('task.create') }}" class="btn btn-primary rounded-3 px-3">
                            <i class="fa-solid fa-plus me-1"></i> Tambah
                        </a>

                        <a href="{{ route('qr.generate') }}" class="btn btn-outline-primary rounded-3 px-3">
                            Generate QR
                        </a>

                        <a href="{{ route('schedule.generate') }}" class="btn btn-outline-secondary rounded-3 px-3">
                            Generate Jadwal
                        </a>
                    </div>
                </div>

            </div>
        </div>
    @endif


    <!-- FILTER -->





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
                            <th>Nama Tugas</th>
                            <th>Deskripsi</th>
                            <th>Tanggal Mulai</th>
                            <th>Tanggal Selesai</th>
                            <th>Status</th>
                            <th>Presensi</th>
                            @if (in_array($userRole, ['hr', 'owner']))
                                <th>Aksi</th>
                            @endif
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($tasks as $task)
                            <tr>

                                <td class="fw-semibold">
                                    {{ ucwords($task->name) }}
                                </td>

                                <td class="text-muted">
                                    {{ ucwords(Str::limit($task->description, 50)) }}
                                </td>

                                <td>
                                    {{ Carbon\Carbon::parse($task->start_time)->format('d M Y') }}
                                </td>

                                <td>
                                    {{ Carbon\Carbon::parse($task->end_time)->format('d M Y') }}
                                </td>

                                <td>
                                    <span @class([
                                        'badge rounded-pill px-3 py-2 fw-medium status-badge' => true,
                                        'status-done' => $task->status == 'done',
                                        'status-onduty' => $task->status == 'on duty',
                                        'status-pending' => $task->status == 'pending',
                                    ])>
                                        {{ ucwords($task->status) }}
                                    </span>
                                </td>

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

                                @if (in_array($userRole, ['hr', 'owner']))
                                    <td>
                                        <div class="action-wrapper">

                                            <a href="{{ route('task.show', $task->id) }}" class="btn-action btn-view">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>

                                            <a href="{{ route('task.edit', $task->id) }}" class="btn-action btn-edit">
                                                <i class="fa-solid fa-pen"></i>
                                            </a>

                                            <form action="{{ route('task.destroy', $task->id) }}" method="POST"
                                                id="deleteForm{{ $task->id }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" onclick="confirmDelete({{ $task->id }})"
                                                    class="btn-action btn-delete">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </form>

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
                    targets: 6,
                    orderable: false,
                    searchable: false
                }]
            @else
                columnDefs: [{
                    targets: 5,
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
    </script>

@endsection
