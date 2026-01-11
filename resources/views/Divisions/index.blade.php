@extends('layout.dashboard')
@section('header', 'Divisions')
@section('content')
    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white">
            <i class="fa-solid fa-plus-circle me-2"></i> Tambah Divisi
        </div>
        @if (session('success'))
            <span class="alert alert-success">{{ session('success') }}</span>
        @endif
        <div class="card-body">
            <div class="col-md-1 d-grid align-self-end">
                <a href="{{ route('division.create') }}" type="button" class="btn btn-primary">
                    <i class="fa-solid fa-plus"></i> Tambah
                </a>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="mb-3 d-flex align-items-center">
        <label class="me-2 fw-bold">Filter Status:</label>
        <select id="filterStatus" class="form-select w-auto me-3">
            <option value="">Semua</option>
            <option value="Belum Selesai">Belum Selesai</option>
            <option value="Sedang Dikerjakan">Sedang Dikerjakan</option>
            <option value="Selesai">Selesai</option>
            <option value="Menunggu ACC HRD">Menunggu ACC HRD</option>
            <option value="Ditolak HRD">Ditolak HRD</option>
        </select>

        <label class="me-2 fw-bold">Filter Karyawan:</label>
        <select id="filterKaryawan" class="form-select w-auto">
            <option value="">Semua Karyawan</option>
            <option value="Budi Santoso">Budi Santoso</option>
            <option value="Siti Aminah">Siti Aminah</option>
            <option value="Rudi Hartono">Rudi Hartono</option>
        </select>
    </div>

    <!-- Tabel Tugas -->
    <div class="card shadow">
        <div class="card-body">
            <h5 class="card-title"><i class="fa-solid fa-list-check"></i> Daftar Divisi</h5>
            <div class="table-responsive">
                <table id="leaveTable" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Nama Divisi</th>
                            <th>description</th>
                            <th>Status</th>
                            <th>Opsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($divisions as $division)
                            <tr>
                                <td>{{ ucwords($division->name) }}</td>
                                <td>
                                    @if ($division->description)
                                        {{ ucwords($division->description) }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ ucwords($division->status) }}</td>
                                <td>
                                    <a href="{{ route('leave-request.edit', $division->id) }}"
                                        class="btn btn-warning btn-sm"><i class="fa-solid fa-pen"></i></a>
                                    <form action="{{ route('leave-request.destroy', $division->id) }}" method="POST"
                                        class="d-inline" id="deleteForm{{ $division->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm" type="button"
                                            onclick="confirmDelete({{ $division->id }})"><i
                                                class="fa-solid fa-trash"></i></button>
                                    </form>
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
                    targets: 3,
                    orderable: false,
                    searchable: false
                }]
            });

            // Custom filtering
            DataTable.ext.search.push(function(settings, data, dataIndex) {

                let filterKaryawan = $("#filterKaryawan").val();
                let filterStatus = $("#filterStatus").val();

                let namaKaryawan = data[0]; // kolom nama karyawan
                let status = data[4]; // kolom status (cek index tabel kamu)

                if (
                    (filterKaryawan === "" || namaKaryawan.includes(filterKaryawan)) &&
                    (filterStatus === "" || status.includes(filterStatus))
                ) {
                    return true;
                }
                return false;
            });

            // Re-draw table on dropdown change
            $("#filterKaryawan, #filterStatus").on("change", function() {
                table.draw();
            });
        });

        function changeStatus(select) {
            if (select.value) {
                window.location.href = select.value
            }
        }
    </script>

@endsection
