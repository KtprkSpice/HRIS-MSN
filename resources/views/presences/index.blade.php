@extends('layout.dashboard')
@section('header', 'Data Kehadiran')
@section('content')

    {{-- Create Employees Button --}}
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">Tambah Kehadiran</div>
        @if (session('success'))
            <span class="alert alert-success">{{ session('success') }}</span>
        @endif
        <div class="card-body">
            <a href="{{ route('presence.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i>
                Tambah</a>
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
                            <th>Nama Karyawan</th>
                            <th>Nama Tugas</th>
                            <th>Tanggal</th>
                            <th>Waktu Masuk</th>
                            <th>Waktu Keluar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($presences as $presence)
                            <tr>
                                <td>{{ ucwords($presence->employee->fullname) }}</td>
                                <td>{{ ucwords($presence->task->name) }}</td>
                                <td>{{ \Carbon\Carbon::parse($presence->date)->format('d F Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($presence->check_in)->format('H:i') }}</td>
                                <td>{{$presence->check_out ?  \Carbon\Carbon::parse($presence->check_out)->format('H:i') : '-' }}</td>
                                <td>
                                    <a href="{{ route('presence.edit', $presence->id) }}"
                                        class="btn btn-sm btn-warning editBtn"><i class="fa-solid fa-pen"></i></a>
                                    <form action="{{ route('presence.destroy', $presence->id) }}" method="post"
                                        class="d-inline" id="deleteForm{{ $presence->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-danger"
                                            onclick="confirmDelete({{ $presence->id }})">
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


@endsection
