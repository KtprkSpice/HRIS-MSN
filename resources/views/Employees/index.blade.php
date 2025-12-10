@extends('layout.dashboard')
@section('header', 'Data Karyawan')
@section('content')

    {{-- Create Employees Button --}}
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">Tambah Karyawan</div>
        @if (session('success'))
            <span class="alert alert-success">{{ session('success') }}</span>
        @endif
        <div class="card-body">
            <a href="{{ route('employee.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i>
                Tambah Data Karyawan</a>
        </div>
    </div>

    {{-- Employees DattaTables --}}
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">Daftar Karyawan</div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="employeeTable" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Telepon</th>
                            <th>Jenis Kelamin</th>
                            <th>Divisi</th>
                            <th>Tanggal Lahir</th>
                            <th>NPWP</th>
                            <th>Skor</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($employees as $employee)
                            <tr>
                                <td>{{ ucwords($employee->fullname) }}</td>
                                <td>{{ $employee->email }}</td>
                                <td>{{ $employee->phone }}</td>
                                <td>{{ ucwords($employee->gender) }}</td>
                                <td>{{ ucwords($employee->division->name) }}</td>
                                <td>{{ \Carbon\Carbon::parse($employee->born_date)->translatedFormat('d F Y') }}</td>
                                <td>{{ $employee->npwp }}</td>
                                <td>1990</td>
                                <td><span @class([
                                    'badge bg-success text-white text-center p-2' =>
                                        $employee->status == 'active',
                                    'badge bg-warning text-white text-center p-2' =>
                                        $employee->status == 'inactive',
                                ])>{{ ucwords($employee->status) }}</span> </td>
                                <td>
                                    <a href="{{ route('employee.edit', $employee->id) }}"
                                        class="btn btn-sm btn-warning editBtn"><i class="fa-solid fa-pen"></i></a>
                                    <form action="{{ route('employee.destroy', $employee->id) }}" method="post"
                                        class="d-inline" id="deleteForm{{ $employee->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-danger"
                                            onclick="confirmDelete({{ $employee->id }})">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- jQuery + DataTables -->
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('DataTables/datatables.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            let table = new DataTable('#employeeTable', {
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
                    targets: 8,
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
    </script>

@endsection
