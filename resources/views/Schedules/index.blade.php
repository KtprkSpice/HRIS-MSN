@extends('layout.dashboard')
@section('header', 'Jadwal')
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
                <a href="{{ route('schedule.create') }}" type="button" class="btn btn-primary">
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
                <table id="scheduleTable" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Nama Karyawan</th>
                            <th>Shift</th>
                            <th>Tugas</th>
                            <th>Tipe</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($schedules as $schedule)
                            <tr>
                                <td>{{ ucwords($schedule->employee->fullname) }}</td>
                                <td>{{ ucwords($schedule->shift->name) }}</td>
                                <td>{{ ucwords($schedule->task->name) }}</td>
                                <td>{{ ucwords($schedule->source) }}</td>
                                <td>{{ Carbon\Carbon::parse($schedule->date)->format('d F Y') }}</td>
                                <td>
                                    <a href="{{ route('schedule.edit', $schedule->id) }}"
                                        class="btn btn-warning btn-sm text-white"><i class="fa-solid fa-pen"></i></a>
                                    <form action="{{ route('schedule.destroy', $schedule->id) }}" method="POST"
                                        class="d-inline" id="deleteForm{{ $schedule->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm" type="button"
                                            onclick="confirmDelete({{ $schedule->id }})"><i
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
        let table = new DataTable('#scheduleTable', {
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
                targets: 4,
                orderable: false,
                searchable: false
            }]
        });
    </script>

@endsection
