@extends('layout.dashboard')
@section('header', 'Tugas')
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
                <a href="{{ route('task.create') }}" type="button" class="btn btn-primary">
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
                            <th>Nama Tugas</th>
                            <th>Deskripsi</th>
                            <th>Tanggal Mulai</th>
                            <th>Tanggal Selesai</th>
                            <th>Status</th>
                            <th>Presensi</th>
                            <th>QR</th>
                            <th>Opsi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tasks as $task)
                            <tr>
                                <td>{{ ucwords($task->name) }}</td>
                                <td>{{ ucwords(Str::limit($task->description, 50)) }}</td>
                                <td>{{ Carbon\Carbon::parse($task->start_time)->format('d F Y') }}</td>
                                <td>{{ Carbon\Carbon::parse($task->end_time)->format('d F Y') }}</td>
                                <td><span @class([
                                    'badge bg-success text-white text-center p-2' => $task->status == 'done',
                                    'badge bg-info text-white text-center p-2' => $task->status == 'on duty',
                                    'badge bg-warning text-white text-center p-2' => $task->status == 'pending',
                                ])>{{ ucwords($task->status) }}</span>
                                </td>
                                <td>
                                    @if ($task->status == 'on duty')
                                        <a href="{{ route('presences.scan', $task->id) }}"
                                            class="btn btn-sm btn-info text-white"><i class="fa-solid fa-qrcode"></i>
                                            Presensi</a>
                                    @elseif ($task->status == 'done')
                                        <button onclick="failed({{ $task->id }})"
                                            class="btn btn-sm btn-secondary">Presensi</button>
                                    @else
                                        <button onclick="warning({{ $task->id }})"
                                            class="btn btn-sm btn-secondary">Presensi</button>
                                    @endif
                                </td>
                                <td>
                                    @if ($task->status == 'on duty')
                                        <a href="{{ route('qr.show', $task->id) }}"><i class="fa-solid fa-qrcode"></i>
                                            Presensi</a>
                                    @else
                                        <button onclick="failed({{ $task->id }})"
                                            class="btn btn-sm btn-secondary">Presensi</button>
                                    @endif
                                </td>
                                <td>
                                    <select name="" id="" class="form-select"
                                        onchange="window.location.href=this.value">
                                        <option value="">Pilih...</option>
                                        @if ($task->status == 'done')
                                            <option value="{{ route('task.onduty', $task->id) }}">
                                                On duty
                                            </option>
                                            <option value="{{ route('task.pending', $task->id) }}">
                                                Pending
                                            </option>
                                        @elseif ($task->status == 'pending')
                                            <option value="{{ route('task.onduty', $task->id) }}">
                                                On duty
                                            </option>
                                            <option value="{{ route('task.done', $task->id) }}">
                                                Done
                                            </option>
                                        @else
                                            <option value="{{ route('task.done', $task->id) }}">
                                                Done
                                            </option>
                                            <option value="{{ route('task.pending', $task->id) }}">
                                                Pending
                                            </option>
                                        @endif
                                    </select>
                                </td>
                                <td>
                                    <a href="{{ route('task.edit', $task->id) }}" class="btn btn-warning btn-sm"><i
                                            class="fa-solid fa-pen"></i></a>
                                    <form action="{{ route('task.destroy', $task->id) }}" method="POST" class="d-inline"
                                        id="deleteForm{{ $task->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm" type="button"
                                            onclick="confirmDelete({{ $task->id }})"><i
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

    <script>
        function failed(id) {
            Swal.fire({
                title: "Gagal!",
                text: "Anda sudah tidak dapat melakukan absensi",
                icon: "error",
            })
        };

        function warning(id) {
            Swal.fire({
                title: "warning",
                text: "Tugas sudah selesai!",
                icon: "error",
            })
        };
    </script>

@endsection
