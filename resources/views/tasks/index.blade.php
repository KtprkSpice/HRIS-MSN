@extends('layout.dashboard')
@section('header', 'Tugas')
@section('content')
    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white">
            <i class="fa-solid fa-plus-circle me-2"></i> Tambah Tugas Baru (Dummy)
        </div>
        <div class="card-body">
            <form class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-bold">Karyawan</label>
                    <select class="form-select">
                        <option>Pilih Karyawan</option>
                        <option>Budi Santoso (IT)</option>
                        <option>Siti Aminah (HRD)</option>
                        <option>Rudi Hartono (Security)</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Nama Tugas</label>
                    <input type="text" class="form-control" placeholder="Contoh: Laporan Mingguan">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Detail Pekerjaan</label>
                    <input type="text" class="form-control" placeholder="Deskripsi tugas">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold">Status</label>
                    <select class="form-select">
                        <option>Belum Selesai</option>
                        <option>Sedang Dikerjakan</option>
                        <option>Selesai</option>
                    </select>
                </div>
                <div class="col-md-1 d-grid align-self-end">
                    <a href="{{ route('task.create') }}" type="button" class="btn btn-primary">
                        <i class="fa-solid fa-plus"></i> Tambah
                    </a>
                </div>
            </form>
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
            <h5 class="card-title"><i class="fa-solid fa-list-check"></i> Daftar Tugas</h5>
            <div class="table-responsive">
                <table id="tugasTable" class="table table-striped table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Nama Tugas</th>
                            <th>Deskripsi</th>
                            <th>Tanggal Mulai</th>
                            <th>Tanggal Selesai</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                       @foreach ($tasks as $task )
                        <tr>
                            <td>{{ ucwords($task->name) }}</td>
                            <td>{{ ucwords(Str::limit($task->description, 50)) }}</td>
                            <td>{{ Carbon\Carbon::parse($task->start_time)->format('d F y') }}</td>
                            <td>{{ Carbon\Carbon::parse($task->end_time)->format('d F y') }}</td>
                            <td><span class="badge bg-warning text-dark">{{ $task->status }}</span></td>
                            <td>
                                <button class="btn btn-warning btn-sm"><i class="fa-solid fa-pen"></i></button>
                                <button class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i></button>
                            </td>
                        </tr>
                       @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection
