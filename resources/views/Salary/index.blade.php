@extends('layout.dashboard')
@section('header', 'Slip Gaji')
@section('content')
    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white">
            <i class="fa-solid fa-plus-circle me-2"></i> Tambah Tugas Baru
        </div>
        @if (session('success'))
            <span class="alert alert-success">{{ session('success') }}</span>
        @endif
        <div class="card-body">
            <div class="col-md-1 d-grid align-self-end">
                <a href="{{ route('salary.create') }}" type="button" class="btn btn-primary">
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
            <h5 class="card-title"><i class="fa-solid fa-list-check"></i> Daftar Tugas</h5>
            <div class="table-responsive">
                <table id="tugasTable" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Nama Karyawan</th>
                            <th>Divisi</th>
                            <th>Gaji</th>
                            <th>Potongan</th>
                            <th>Bonus</th>
                            <th>Total</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($salaries as $salary)
                            <tr>
                                <td>{{ ucwords($salary->employee->fullname) }}</td>
                                <td>{{ strtoupper($salary->employee->division->name) }}</td>
                                <td>Rp. {{ number_format($salary->net_salary) }}</td>
                                <td>Rp. {{ number_format($salary->cuts) }}</td>
                                <td>Rp. {{ number_format($salary->bonus) }}</td>
                                <td>Rp. {{ number_format($salary->total) }}</td>
                                <td>
                                    <a href="{{ route('salary.edit', $salary->id) }}" class="btn btn-warning btn-sm"><i
                                            class="fa-solid fa-pen"></i></a>
                                    <form action="{{ route('salary.destroy', $salary->id) }}" method="POST" class="d-inline"
                                        id="deleteForm{{ $salary->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm" type="button"
                                            onclick="confirmDelete({{ $salary->id }})"><i
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
@endsection
