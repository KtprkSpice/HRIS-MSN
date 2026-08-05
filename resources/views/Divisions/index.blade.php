@extends('layout.dashboard')
@section('header', 'Manajemen Divisi')
@section('content')

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        /* Table Styling Modern */
        .table {
            font-size: 0.85rem;
            vertical-align: middle;
        }

        #leaveTable thead th {
            background: linear-gradient(180deg, #bc5e6b 0%, #a34a57 100%);
            color: white;
            border: none;
            padding: 15px;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
        }

        /* Card Customization */
        .card-custom-header {
            background: linear-gradient(90deg, #bc5e6b 0%, #a34a57 100%);
            color: white;
            font-weight: bold;
            padding: 12px 20px;
            border-radius: 12px 12px 0 0 !important;
        }

        /* Filter Section */
        .filter-section {
            background-color: #ffffff;
            border-radius: 12px;
            padding: 20px;
            border: 1px solid #dee2e6;
            box-shadow: 0 .125rem .25rem rgba(0, 0, 0, .075);
        }

        /* Select2 Adjustment */
        .select2-container--default .select2-selection--single {
            height: 40px !important;
            padding: 5px !important;
            border: 1px solid #dee2e6 !important;
            border-radius: 8px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 38px !important;
        }

        /* Modern Buttons */
        .btn-action {
            width: 35px;
            height: 35px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            transition: all 0.3s;
            border: none;
            color: white !important;
            text-decoration: none;
        }

        .btn-action:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.15);
        }

        .btn-edit {
            background: linear-gradient(45deg, #ffc107, #ff9800);
        }

        .btn-delete {
            background: linear-gradient(45deg, #dc3545, #b02a37);
        }

        /* Badge Status */
        .badge-status {
            font-weight: 600;
            padding: 7px 12px;
            border-radius: 8px;
            width: 90px;
            display: inline-block;
            text-align: center;
            font-size: 0.75rem;
        }

        .status-active {
            background-color: #d1e7dd;
            color: #0f5132;
            border: 1px solid #badbcc;
        }

        .status-inactive {
            background-color: #f8d7da;
            color: #842029;
            border: 1px solid #f5c2c7;
        }
    </style>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body d-flex justify-content-between align-items-center p-3">

                    <div class="d-flex align-items-center">
                        <div class="bg-light p-2 rounded-3 me-3">
                            <i class="fa-solid fa-sitemap fs-5 text-dark"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-semibold">Data Divisi</h5>
                            <p class="text-muted small mb-0">Kelola departemen organisasi</p>
                        </div>
                    </div>

                    <a href="{{ route('division.create') }}" class="btn btn-primary rounded-pill px-4">
                        <i class="fa-solid fa-plus me-2"></i> Tambah Divisi
                    </a>

                </div>
            </div>
        </div>
    </div>


    <div class="card shadow-sm border-0" style="border-radius: 12px; overflow: hidden;">
        <div class="card-header card-custom-header">
            <i class="fa-solid fa-list me-2"></i> List Departemen / Divisi
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="leaveTable" class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Nama Divisi</th>
                            <th>Deskripsi</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Opsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($divisions as $division)
                            <tr>
                                <td class="ps-4 fw-bold text-dark">{{ ucwords($division->name) }}</td>
                                <td class="text-muted small">{{ $division->description ?: '-' }}</td>
                                <td class="text-center">
                                    <select onchange="changeStatus(this)" @class([
                                        'form-select form-select-sm fw-medium text-center status-badge' => true,
                                        'status-active' => $division->status == 'active',
                                        'status-inactive' => $division->status == 'inactive',
                                    ])>

                                      <option value="{{ route('division.active', [$division->id, 'active']) }}" 
                                         {{ $division->status == 'active' ? 'selected' : '' }}>
    Active
</option>

<option value="{{ route('division.inactive', [$division->id, 'inactive']) }}" 
     {{ $division->status == 'inactive' ? 'selected' : '' }}>
    Inactive
</option>
                                    </select>
                                </td>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ route('division.edit', $division->id) }}" class="btn-action btn-edit"
                                            title="Edit">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <button type="button" class="btn-action btn-delete"
                                            onclick="confirmDelete({{ $division->id }})" title="Hapus">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                        <form id="delete-form-{{ $division->id }}"
                                            action="{{ route('division.destroy', $division->id) }}" method="POST"
                                            style="display: none;">
                                            @csrf
                                            @method('DELETE')
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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('DataTables/datatables.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            $('.select2-js').select2({
                theme: "default",
                width: '100%'
            });

            let table = $('#leaveTable').DataTable({
                language: {
                    search: "Cari:",
                    lengthMenu: "_MENU_",
                    info: "Total: _TOTAL_ data"
                },
                columnDefs: [{
                    targets: 3,
                    orderable: false
                }]
            });

            $('#filterStatus, #filterKaryawan').on('change', function() {
                table.draw();
            });

            DataTable.ext.search.push(function(settings, data, dataIndex) {
                let fName = $("#filterKaryawan").val().toLowerCase();
                let fStat = $("#filterStatus").val().toLowerCase();
                let name = data[0].toLowerCase();
                let stat = data[2].toLowerCase();
                return (fName === "" || name.includes(fName)) && (fStat === "" || stat.includes(fStat));
            });

            // Tampilkan SweetAlert sukses jika ada session
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: "{{ session('success') }}",
                    timer: 3000,
                    showConfirmButton: false
                });
            @endif

            @if (session('error_from_controller'))
                Swal.fire({
                    icon: 'error',
                    title: 'Divisi Tidak Bisa Dihapus',
                    html: `{!! session('error_from_controller') !!}`,
                    confirmButtonText: 'Mengerti'
                });
            @endif
        });

        // Fungsi SweetAlert untuk Konfirmasi Hapus
        function confirmDelete(id) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data divisi yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#bc5e6b',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }

        function changeStatus(select) {
            window.location.href = select.value;
}
    </script>

@endsection
