@extends('layout.dashboard')
@section('header', 'Tipe Cuti')

@section('content')

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* Styling Tabel Modern */
        .table {
            font-size: 0.9rem;
            vertical-align: middle;
        }

        #leaveTable thead th {
            background: linear-gradient(180deg, #bc5e6b 0%, #a34a57 100%);
            color: white;
            border: none;
            padding: 15px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.8rem;
        }

        #leaveTable tbody td {
            padding: 12px 15px;
            color: #4a4a4a;
        }

        /* Action Buttons */
        .btn-action {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            transition: all 0.2s;
            border: none;
        }

        .btn-edit {
            background-color: #ffc107;
            color: #000;
        }

        .btn-edit:hover {
            background-color: #e0a800;
            color: #000;
            transform: translateY(-2px);
        }

        .btn-delete {
            background-color: #dc3545;
            color: #fff;
        }

        .btn-delete:hover {
            background-color: #bb2d3b;
            color: #fff;
            transform: translateY(-2px);
        }

        /* Card Styling */
        .card-custom-header {
            background: linear-gradient(90deg, #bc5e6b 0%, #a34a57 100%);
            color: white;
            border: none;
            padding: 12px 20px;
        }

        .add-btn-custom {
            background-color: #215cda;
            color: white;
            border-radius: 8px;
            font-weight: 500;
            padding: 8px 20px;
            transition: 0.3s;
        }

        .add-btn-custom:hover {
            background-color: #1a4ab1;
            color: white;
            box-shadow: 0 4px 12px rgba(33, 92, 218, 0.3);
        }
    </style>

    <div class="card shadow-sm border-0 rounded-4 mb-4">

    <div class="card-header d-flex justify-content-between align-items-center fw-semibold">
        <span class="d-flex align-items-center">
            <i class="fa-solid fa-layer-group me-2"></i> 
            Pengaturan Tipe Cuti
        </span>

        <a href="{{ route('leave-type.create') }}" class="btn btn-primary rounded-pill px-4">
            <i class="fa-solid fa-plus me-2"></i> Tambah Baru
        </a>
    </div>

    @if (session('success'))
        <div class="px-4 pt-2">
            <div class="alert alert-success alert-dismissible fade show rounded-3 mb-0" role="alert">
                <i class="fa-solid fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    <div class="card-body pt-2 pb-3">
        <p class="text-muted mb-0 small">
            <i class="fa-solid fa-circle-info me-1 text-primary"></i> 
            Kelola kategori cuti, jumlah maksimal hari, dan potongan gaji per hari di sini.
        </p>
    </div>

</div>

    <div class="card shadow border-0">
        <div class="card-body p-4">
            <h5 class="card-title fw-bold mb-4 text-secondary">
                <i class="fa-solid fa-list-check me-2" style="color: #bc5e6b;"></i> Master Data Jenis Cuti
            </h5>

            <div class="table-responsive">
                <table id="leaveTable" class="table table-hover border">
                    <thead>
                        <tr class="text-center">
                            <th class="text-start">Nama Cuti</th>
                            <th>Potongan (IDR)</th>
                            <th>Maks Hari</th>
                            <th>Periode Kouta</th>
                            <th>Kouta Cuti</th>
                            <th width="100px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($leaveTypes as $leaveType)
                            <tr class="text-center">
                                <td class="text-start fw-bold text-dark">{{ ucwords($leaveType->name) }}</td>
                                <td class="text-danger fw-medium">
                                    Rp {{ number_format($leaveType->deduction, 0, ',', '.') }}
                                </td>
                                <td>
                                    @if ($leaveType->max_days)
                                        <span class="badge bg-info text-dark px-3" style="font-weight: 500;">
                                            {{ $leaveType->max_days }} Hari / Tahun
                                        </span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info text-dark px-3" style="font-weight: 500;">
                                        {{ ucwords($leaveType->limit_type) }}
                                    </span>
                                </td>
                                <td>
                                    @if ($leaveType->limit_days)
                                        <span class="badge bg-info text-dark px-3" style="font-weight: 500;">
                                            {{ $leaveType->limit_days }}
                                        </span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ route('leave-type.edit', $leaveType->id) }}"
                                            class="btn-action btn-edit shadow-sm" title="Ubah">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>

                                        <form action="{{ route('leave-type.destroy', $leaveType->id) }}" method="POST"
                                            class="d-inline" id="deleteForm{{ $leaveType->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn-action btn-delete shadow-sm" type="button"
                                                onclick="confirmDelete({{ $leaveType->id }})" title="Hapus">
                                                <i class="fa-solid fa-trash-can"></i>
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
            $('#leaveTable').DataTable({
                language: {
                    search: "Cari:",
                    lengthMenu: "_MENU_",
                    info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                    paginate: {
                        previous: "<",
                        next: ">",
                    },
                },
                columnDefs: [{
                    targets: 5,
                    orderable: false,
                    search: false
                }]
            });
        });

        /**
         * Konfirmasi hapus menggunakan SweetAlert2
         */
        function confirmDelete(id) {
            Swal.fire({
                title: 'Hapus data ini?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#bc5e6b', // Menyesuaikan tema merah marun Anda
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true // Memposisikan "Batal" di kiri dan "Hapus" di kanan
            }).then((result) => {
                if (result.isConfirmed) {
                    // Submit form jika user menekan tombol Konfirmasi
                    document.getElementById('deleteForm' + id).submit();
                }
            });
        }
    </script>

@endsection
