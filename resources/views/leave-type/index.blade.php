@extends('layout.dashboard')
@section('header', 'Tipe Cuti')
@section('content')
    <div class="card shadow mb-4">
        <div class="card-header bg-danger text-white">
            <i class="fa-solid fa-plus-circle me-2"></i> Tambah Jenis Cuti
        </div>
        @if (session('success'))
            <span class="alert alert-success">{{ session('success') }}</span>
        @endif
        <div class="card-body">
            <div class="col-md-1 d-grid align-self-end">
                <a href="{{ route('leave-type.create') }}" type="button" class="btn btn-primary">
                    <i class="fa-solid fa-plus"></i> Tambah
                </a>
            </div>
        </div>
    </div>


    <!-- Tabel Tugas -->
    <div class="card shadow">
        <div class="card-body">
            <h5 class="card-title"><i class="fa-solid fa-list-check"></i> Daftar Tugas</h5>
            <div class="table-responsive">
                <table id="leaveTable" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Nama Cuti</th>
                            <th>Potongan</th>
                            <th>Maks Hari</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($leaveTypes as $leaveType)
                            <tr>
                                <td>{{ ucwords($leaveType->name) }}</td>
                                <td>Rp. {{ number_format($leaveType->deduction) }}</td>
                                <td>
                                    @if ($leaveType->max_days)
                                        {{ $leaveType->max_days }} Hari
                                    @else
                                        -
                                    @endif
                                </td>

                                <td>
                                    <a href="{{ route('leave-type.edit', $leaveType->id) }}"
                                        class="btn btn-warning btn-sm"><i class="fa-solid fa-pen"></i></a>
                                    <form action="{{ route('leave-type.destroy', $leaveType->id) }}" method="POST"
                                        class="d-inline" id="deleteForm{{ $leaveType->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm" type="button"
                                            onclick="confirmDelete({{ $leaveType->id }})"><i
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
                targets: 3,
                orderable: false,
                searchable: false
            }]
        });
    </script>

@endsection
