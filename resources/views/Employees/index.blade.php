@extends('layout.dashboard')
@section('header', 'Data Karyawan')
@section('content')

    {{-- Create Employees Button --}}
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">Tambah Karyawan</div>
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
                <table id="tugasTable" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Telepon</th>
                            <th>Jenis Kelamin</th>
                            <th>Divisi</th>
                            <th>Tanggal Lahir</th>
                            <th>Skor</th>
                            <th>aksi</th>
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
                                <td>1990</td>
                                <td>
                                    <button class="btn btn-sm btn-warning editBtn"><i class="fa-solid fa-pen"></i></button>
                                    <button class="btn btn-sm btn-danger"><i class="fa-solid fa-trash"></i></button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
