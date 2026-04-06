@extends('layout.dashboard')
@section('header', 'Jadwal')
@section('content')

    <style>
        /* Styling Header dengan Gradasi Merah Profesional */
        #scheduleTable thead th {
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

        /* Warna tombol aksi yang lebih soft */
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

        /* Hover effect pada baris tabel */
        .table-hover tbody tr:hover {
            background-color: rgba(188, 94, 107, 0.05);
        }
    </style>

    <div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-3">

        <h5 class="mb-0 fw-semibold d-flex align-items-center">
            <i class="fa-solid fa-plus-circle me-2 text-dark"></i>
            Tambah Jadwal
        </h5>

        <div class="d-flex align-items-center gap-2">
            <div class="d-grid">
                <a href="{{ route('schedule.create') }}" class="btn btn-primary rounded-pill px-4">
                    <i class="fa-solid fa-plus me-2"></i> Tambah
                </a>
            </div>
        </div>

    </div>

    <div class="card-body pt-0">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-3 mt-2" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    </div>
</div>

    <div class="card shadow">
        <div class="card-body">
            <h5 class="card-title mb-4"><i class="fa-solid fa-list-check"></i> Daftar Karyawan</h5>
            <div class="table-responsive">
                <table id="scheduleTable" class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Nama Karyawan</th>
                            <th>Shift</th>
                            <th>Tugas</th>
                            <th>Type</th>
                            <th>Tanggal</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($schedules as $schedule)
                            <tr>
                                <td class="fw-bold text-dark">{{ ucwords($schedule->employee->fullname) }}</td>
                                <td><span
                                        class="badge bg-light text-dark border">{{ ucwords($schedule->shift->name) }}</span>
                                </td>
                                <td class="text-muted">{{ ucwords($schedule->task->name) }}</td>
                                <td>{{ ucwords($schedule->source) }}</td>
                                <td data-search="{{ \Carbon\Carbon::parse($schedule->date)->translatedFormat('d F Y') }}"
                                    data-order="{{ $schedule->date }}">
                                    {{ \Carbon\Carbon::parse($schedule->date)->format('d F Y') }}</td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ route('schedule.edit', $schedule->id) }}"
                                            class="btn btn-action btn-edit btn-sm" title="Edit">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>

                                        <form action="{{ route('schedule.destroy', $schedule->id) }}" method="POST"
                                            class="d-inline" id="deleteForm{{ $schedule->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-action btn-delete btn-sm" type="button"
                                                onclick="confirmDelete({{ $schedule->id }})" title="Hapus">
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

    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('DataTables/datatables.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            let table = $('#scheduleTable').DataTable({
                "pageLength": 10, // Menampilkan 10 data per halaman
                "ordering": true,
                "responsive": true,
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
                    targets: 5,
                    orderable: false,
                    searchable: false
                }]
            });
        });
    </script>

@endsection
