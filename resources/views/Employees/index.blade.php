@extends('layout.dashboard')
@section('header', 'Daftar Karyawan')
@section('content')
    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white">
            <i class="fa-solid fa-plus-circle me-2"></i> Tambah Data Karyawan
        </div>
        @if (session('success'))
            <span class="alert alert-success">{{ session('success') }}</span>
        @endif
        <div class="card-body">
            <div class="col-md-1 d-grid align-self-end">
                <a href="{{ route('employee.create') }}" type="button" class="btn btn-primary">
                    <i class="fa-solid fa-plus"></i> Tambah
                </a>
            </div>
        </div>
    </div>

    <!-- Tabel Employee -->
    <div class="card shadow">
        <div class="card-body">
            <h5 class="card-title"><i class="fa-solid fa-list-check"></i> Daftar Karyawan</h5>
            <div class="table-responsive">
                <table id="leaveTable" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Nama Karyawan</th>
                            <th>No.Telpon</th>
                            <th>Email</th>
                            <th>Divisi</th>
                            <th>Gender</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($employees as $employee)
                            <tr>
                                <td>{{ ucwords($employee->fullname) }}</td>
                                <td>{{ $employee->phone }}</td>
                                <td>{{ $employee->email }}</td>
                                <td>{{ ucwords($employee->division->name) }}</td>
                                <td>{{ ucwords($employee->gender) }}</td>
                                <td>
                                    <span @class([
                                        'badge bg-success text-white text-center p-2' =>
                                            $employee->status == 'active',
                                        'badge bg-warning text-white text-center p-2' =>
                                            $employee->status == 'inactive',
                                    ])>{{ ucwords($employee->status) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('employee.edit', $employee->id) }}"
                                        class="btn btn-warning btn-sm text-white"><i class="fa-solid fa-pen"></i></a>
                                    <form action="{{ route('employee.destroy', $employee->id) }}" method="POST"
                                        class="d-inline" id="deleteForm{{ $employee->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm" type="button"
                                            onclick="confirmDelete({{ $employee->id }})"><i
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
                targets: 5,
                orderable: false,
                searchable: false
            }]
        });
    </script>

@endsection
