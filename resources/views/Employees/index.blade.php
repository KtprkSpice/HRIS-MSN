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
                                    <a href="{{ route('employee.edit', $employee->id) }}"
                                        class="btn btn-sm btn-warning editBtn"><i class="fa-solid fa-pen"></i></a>
                                        <form action="{{ route('employee.destroy', $employee->id) }}" method="post" class="d-inline" id="deleteForm{{ $employee->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete({{ $employee->id }})">
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

    <script>
     function confirmDelete(id) {
    Swal.fire({
        title: "Apakah kamu yakin?",
        text: "Data ini tidak bisa dikembalikan setelah dihapus!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Ya, hapus!",
        cancelButtonText: "Batal"
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('deleteForm' + id).submit();
        }
    });
}
    </script>
@endsection
