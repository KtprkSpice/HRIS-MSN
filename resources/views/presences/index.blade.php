@extends('layout.dashboard')
@section('header', 'Data Kehadiran')
@section('content')

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        /* Table Styling Modern */
        .table {
            font-size: 0.85rem;
            vertical-align: middle;
        }

        #presencesTable thead th {
            background: linear-gradient(180deg, #bc5e6b 0%, #a34a57 100%);
            color: white;
            border: none;
            padding: 15px;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
        }

        /* Card & Action Row Styling */
        .card-custom-header {
            background: linear-gradient(90deg, #bc5e6b 0%, #a34a57 100%);
            color: white;
            font-weight: bold;
            padding: 12px 20px;
            border-radius: 12px 12px 0 0 !important;
        }

        /* Filter Section - Diperbaiki agar mirip Form Tambah */
        .filter-section {
            background-color: #ffffff;
            border-radius: 12px;
            padding: 20px;
            border: 1px solid #dee2e6;
            box-shadow: 0 .125rem .25rem rgba(0, 0, 0, .075);
        }

        /* Select2 Modern Integration */
        .select2-container--default .select2-selection--single {
            height: 40px !important;
            padding: 5px !important;
            border: 1px solid #dee2e6 !important;
            border-radius: 8px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 38px !important;
        }

        /* Modern Action Buttons */
        .btn-action {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            transition: all 0.2s;
            border: none;
            color: white !important;
        }

        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .btn-view {
            background-color: #17a2b8;
        }

        .btn-edit {
            background-color: #ffc107;
            color: #212529 !important;
        }

        .btn-delete {
            background-color: #dc3545;
        }

        /* Badge Customization */
        .badge-status {
            font-weight: 600;
            padding: 7px 12px;
            border-radius: 8px;
            width: 100px;
            display: inline-block;
            text-align: center;
        }
    </style>

    <div class="card shadow-sm mb-4 border-0" style="border-radius: 12px;">
        <div class="card-header card-custom-header d-flex justify-content-between align-items-center">
            <span><i class="fa-solid fa-clipboard-user me-2"></i> Daftar Kehadiran Karyawan</span>
            @if (in_array($userRole, ['hr', 'owner']))
                <a href="{{ route('presence.create') }}" class="btn btn-primary btn-sm fw-bold shadow-sm"
                    style="border-radius: 8px;">
                    <i class="fa-solid fa-plus-circle me-1"></i> Tambah Kehadiran
                </a>
            @endif
        </div>

        <div class="card-body p-4">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                    <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="filter-section mb-4">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-secondary small">
                            <i class="fa-solid fa-filter me-2" style="color: #bc5e6b;"></i> Filter Status
                        </label>
                        <select id="filterStatus" class="form-control select2-js">
                            <option value="">-- Semua Status --</option>
                            <option value="Belum Selesai">Belum Selesai</option>
                            <option value="Sedang Dikerjakan">Sedang Dikerjakan</option>
                            <option value="Selesai">Selesai</option>
                            <option value="Menunggu ACC HRD">Menunggu ACC HRD</option>
                            <option value="Ditolak HRD">Ditolak HRD</option>
                        </select>
                    </div>

                    @if (in_array($userRole, ['hr', 'owner']))
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-secondary small">
                                <i class="fa-solid fa-user me-2" style="color: #bc5e6b;"></i> Filter Karyawan
                            </label>
                            <select id="filterKaryawan" class="form-control select2-js">
                                <option value="">-- Semua Karyawan --</option>
                                <option value="Budi Santoso">Budi Santoso</option>
                                <option value="Siti Aminah">Siti Aminah</option>
                                <option value="Rudi Hartono">Rudi Hartono</option>
                            </select>
                        </div>
                    @endif

                    <div class="col-md-4 text-end pb-1">
                        <span class="text-muted small">Total Data: <strong
                                class="text-dark">{{ count($presences) }}</strong> baris</span>
                    </div>
                </div>
            </div>

            {{-- Table Section --}}
            <div class="table-responsive">
                <table id="presencesTable" class="table table-hover align-middle border">
                    <thead>
                        <tr class="text-center">
                            <th class="text-start">Nama Karyawan</th>
                            <th>Nama Tugas</th>
                            <th>Tanggal</th>
                            <th>Masuk</th>
                            <th>Keluar</th>
                            <th>Tipe Absen</th>
                            <th>Telat</th>
                            <th>Status</th>
                            @if (in_array($userRole, ['hr', 'owner']))
                                <th width="150px">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($presences as $presence)
                            <tr class="text-center">
                                <td class="text-start fw-bold text-dark">{{ ucwords($presence->employee->fullname) }}</td>
                                <td class="small">{{ ucwords($presence->task->name) }}</td>
                                <td>{{ \Carbon\Carbon::parse($presence->date)->format('d F Y') }}</td>
                                <td class="text-primary fw-bold">
                                    {{ \Carbon\Carbon::parse($presence->check_in)->format('H:i') }}</td>
                                <td>{{ $presence->check_out ? \Carbon\Carbon::parse($presence->check_out)->format('H:i') : '-' }}
                                </td>
                                <td><span class="badge bg-light text-dark border">{{ ucwords($presence->type) }}</span></td>
                                <td class="{{ $presence->late_minutes > 0 ? 'text-danger fw-bold' : '' }}">
                                    {{ $presence->late_minutes ? $presence->late_minutes . ' min' : '-' }}
                                </td>
                                <td>
                                    <span @class([
                                        'badge-status text-white shadow-sm' => true,
                                        'bg-danger' => $presence->status == 'absent',
                                        'bg-warning' => $presence->status == 'late',
                                        'bg-info' => $presence->status == 'on time',
                                    ])>
                                        {{ ucwords(str_replace('_', ' ', $presence->status)) }}
                                    </span>
                                </td>
                                @if (in_array($userRole, ['hr', 'owner']))
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="#" class="btn-action btn-view" title="Lihat">
                                                <i class="fa-solid fa-eye fa-sm"></i>
                                            </a>
                                            <a href="{{ route('presence.edit', $presence->id) }}"
                                                class="btn-action btn-edit" title="Edit">
                                                <i class="fa-solid fa-pen fa-sm"></i>
                                            </a>
                                            <form action="{{ route('presence.destroy', $presence->id) }}" method="post"
                                                class="d-inline" id="deleteForm{{ $presence->id }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn-action btn-delete"
                                                    onclick="confirmDelete({{ $presence->id }})" title="Hapus">
                                                    <i class="fa-solid fa-trash fa-sm"></i>
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

    {{-- Scripts --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('DataTables/datatables.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            // Inisialisasi Select2 untuk Filter
            $('.select2-js').select2({
                width: '100%'
            });

            let table = $('#presencesTable').DataTable({
                language: {
                    search: "Cari Data:",
                    lengthMenu: "_MENU_",
                    info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                    paginate: {
                        previous: "<",
                        next: ">"
                    },
                },
                @if (in_array($userRole, ['hr', 'owner']))
                    columnDefs: [{
                        targets: 8,
                        orderable: false,
                        searchable: false
                    }]
                @endif
            });

            // Event Filter Logic (DataTables)
            $('#filterStatus').on('change', function() {
                table.column(7).search(this.value).draw();
            });

            $('#filterKaryawan').on('change', function() {
                table.column(0).search(this.value).draw();
            });
        });

        function confirmDelete(id) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Hapus Data?',
                    text: "Data kehadiran ini akan dihapus permanen.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#bc5e6b',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('deleteForm' + id).submit();
                    }
                });
            } else {
                if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
                    document.getElementById('deleteForm' + id).submit();
                }
            }
        }
    </script>

@endsection
