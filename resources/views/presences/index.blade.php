@extends('layout.dashboard')
@section('header', 'Data Kehadiran')
@section('content')

    {{-- Create Button --}}
    <div class="card shadow mb-4">
        @if (in_array($userRole, ['hr', 'owner']))
            <div class="card-header bg-primary text-white">
                <i class="fa-solid fa-plus-circle me-2"></i> Tambah Tugas Baru
            </div>
            @if (session('success'))
                <span class="alert alert-success">{{ session('success') }}</span>
            @endif
            <div class="card-body d-flex gap-2">
                <div class="col-md-1 d-grid align-self-end">
                    <a href="{{ route('presence.create') }}" type="button" class="btn btn-primary">
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

    @endif

    {{-- Employees DattaTables --}}
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">Daftar Karyawan</div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="presencesTable" class="table table-bordered">
                    <thead>
                        {{-- Table Head Owener And HR --}}
                        @if (in_array($userRole, ['hr', 'owner']))
                            <tr>
                                <th>Nama Karyawan</th>
                                <th>Nama Tugas</th>
                                <th>Tanggal</th>
                                <th>Waktu Masuk</th>
                                <th>Waktu Keluar</th>
                                <th>Tipe Absen</th>
                                <th>Menit Telat</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        @else
                            <tr>
                                <th>Nama Karyawan</th>
                                <th>Nama Tugas</th>
                                <th>Tanggal</th>
                                <th>Waktu Masuk</th>
                                <th>Waktu Keluar</th>
                                <th>Tipe Absen</th>
                                <th>Menit Telat</th>
                                <th>Status</th>
                            </tr>
                        @endif
                    </thead>
                    <tbody>
                        @foreach ($presences as $presence)
                            {{-- Owener and HR --}}
                            @if (in_array($userRole, ['hr', 'owner']))
                                <tr>
                                    <td>{{ ucwords($presence->employee->fullname) }}</td>
                                    <td>{{ ucwords($presence->task->name) }}</td>
                                    <td>{{ \Carbon\Carbon::parse($presence->date)->format('d F Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($presence->check_in)->format('H:i') }}</td>
                                    <td>{{ $presence->check_out ? \Carbon\Carbon::parse($presence->check_out)->format('H:i') : '-' }}
                                    <td>{{ ucwords($presence->type) }}</td>
                                    <td>{{ $presence->late_minutes ? $presence->late_minutes : '-' }}</td>
                                    <td><span @class([
                                        'badge bg-danger text-white text-center p-2' =>
                                            $presence->status == 'invalid',
                                        'badge bg-warning text-white text-center p-2' =>
                                            $presence->status == 'late',
                                        'badge bg-info text-white text-center p-2' =>
                                            $presence->status == 'on_time',
                                    ])>{{ ucwords($presence->status) }}</span></td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-info text-white"><i><i
                                                    class="fa-solid fa-eye"></i></i></a>
                                        <a href="{{ route('presence.edit', $presence->id) }}"
                                            class="btn btn-sm btn-warning text-white"><i class="fa-solid fa-pen"></i></a>
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
                            @else
                                <tr>
                                    <td>{{ ucwords($presence->employee->fullname) }}</td>
                                    <td>{{ ucwords($presence->task->name) }}</td>
                                    <td>{{ \Carbon\Carbon::parse($presence->date)->format('d F Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($presence->check_in)->format('H:i') }}</td>
                                    <td>{{ $presence->check_out ? \Carbon\Carbon::parse($presence->check_out)->format('H:i') : '-' }}
                                    <td>{{ ucwords($presence->type) }}</td>
                                    <td>{{ $presence->late_minutes ? $presence->late_minutes : '-' }}</td>
                                    <td><span @class([
                                        'badge bg-danger text-white text-center p-2' =>
                                            $presence->status == 'invalid',
                                        'badge bg-warning text-white text-center p-2' =>
                                            $presence->status == 'late',
                                        'badge bg-info text-white text-center p-2' =>
                                            $presence->status == 'on_time',
                                    ])>{{ ucwords($presence->status) }}</span></td>
                                </tr>
                            @endif
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
        let table = new DataTable('#presencesTable', {
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                paginate: {
                    previous: "Sebelumnya",
                    next: "Berikutnya",
                },
            },
            @if (in_array($userRole, ['hr', 'owner']))
                columnDefs: [{
                    targets: 8,
                    orderable: false,
                    searchable: false
                }]
            @endif
        });
    </script>

@endsection
