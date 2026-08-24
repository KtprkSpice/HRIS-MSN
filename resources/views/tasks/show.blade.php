@extends('layout.dashboard')
@section('header', 'Tugas ' . $task->name)

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    <style>
        <style>

        /* ========================= */
        /* CARD CONSISTENT STYLE     */
        /* ========================= */
        .card {
            border-radius: 16px !important;
            border: none !important;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.04) !important;
            margin-bottom: 24px;
        }

        .detail-card {
            min-height: 100%;
        }

        .card-body {
            padding: 2rem !important;
        }

        /* ========================= */
        /* BUTTON SHIFT STYLE        */
        /* ========================= */
        .btn-group .btn-outline-primary {
            border: 1.5px solid #b83e48;
            color: #b83e48;
            font-weight: 500;
        }

        .btn-group .btn-check:checked+.btn-outline-primary {
            background: linear-gradient(100deg, #b83e48 0%, #eb8697 100%);
            border-color: transparent;
            color: #ffffff;
        }

        /* ========================= */
        /* TABLE HEADER (SAMA)       */
        /* ========================= */
        #jadwalTable thead {
            background: linear-gradient(100deg, #b83e48 0%, #eb8697 100%) !important;
        }

        #jadwalTable thead th {
            background: linear-gradient(100deg, #b83e48 0%, #eb8697 100%) !important;
            color: #ffffff !important;
            font-weight: 600;
            border: none !important;
            letter-spacing: 0.4px;
            padding: 14px;
        }

        /* ========================= */
        /* TABLE BODY (SAMA)         */
        /* ========================= */
        #jadwalTable tbody tr {
            background: #ffffff;
            transition: all 0.2s ease;
        }

        #jadwalTable tbody tr:nth-child(even) {
            background: #fdf2f4;
        }

        #jadwalTable tbody tr:hover {
            background: #f8f9fa;
        }

        #jadwalTable td {
            padding: 14px;
            vertical-align: middle;
        }

        .table>:not(caption)>*>* {
            border-bottom-width: 0px !important;
        }

        /* ========================= */
        /* TABLE GRADIENT MERAH      */
        /* ========================= */

        #EmployeeTable thead {
            background: linear-gradient(100deg, #b83e48 0%, #eb8697 100%) !important;
        }

        #EmployeeTable thead th {
            background: linear-gradient(100deg, #b83e48 0%, #eb8697 100%) !important;
            color: #ffffff !important;
            font-weight: 600;
            border: none !important;
            padding: 14px;
            letter-spacing: 0.4px;
        }

        /* Body */
        #EmployeeTable tbody tr {
            background: #ffffff;
            transition: all 0.2s ease;
        }

        #EmployeeTable tbody tr:nth-child(even) {
            background: #fdf2f4;
            /* soft pink */
        }

        #EmployeeTable tbody tr:hover {
            background: #fce7eb;
        }

        /* Cell spacing */
        #EmployeeTable td {
            padding: 14px;
            vertical-align: middle;
        }

        /* Hilangkan border default bootstrap */
        .table>:not(caption)>*>* {
            border-bottom-width: 0px !important;
        }

        /* ========================= */
        /* RESPONSIVE FIX            */
        /* ========================= */
        @media (max-width: 768px) {
            .card-body {
                padding: 1.25rem !important;
            }
        }
    </style>


    <div class="container-fluid px-3 px-md-4">
        <div class="row">
            <div class="col-12">

                <div class="card shadow-sm border-0 rounded-4 mb-4">
                    <div class="card-body p-4">

                        <div class="d-flex align-items-center gap-3 mb-5 pb-3 border-bottom">
                            <div class="rounded-3 d-flex align-items-center justify-content-center"
                                style="width:48px;height:48px;
    background:linear-gradient(100deg,#b83e48 0%, #eb8697 100%);
    color:white;">
                                <i class="fa-solid fa-clipboard-list"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-0">Detail Tugas</h5>
                                <small class="text-muted">Informasi lengkap tugas yang telah dibuat</small>
                            </div>
                            @if (in_array($userRole, ['hr', 'owner']))
                                <div class="ms-auto">
                                    <form action="{{ route('task.export', $task->id) }}" method="GET"
                                        class="d-flex flex-wrap align-items-end gap-2">
                                        <div>
                                            <label for="week_start" class="form-label small fw-semibold mb-1">Minggu
                                                Mulai</label>
                                            <input type="date" id="week_start" name="week_start"
                                                class="form-control form-control-sm"
                                                value="{{ $weekStart->format('Y-m-d') }}">
                                        </div>
                                        <button type="submit" class="btn btn-success rounded-pill px-4">
                                            <i class="fa-solid fa-file-excel me-2"></i> Export Template
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>

                        <div class="row g-4">

                            <div class="col-12 col-md-6 col-lg-4">
                                <label for="name" class="form-label fw-semibold">Nama Perusahaan</label>
                                <input type="text" class="form-control modern-input @error('name') is-invalid @enderror"
                                    id="name" name="name" readonly value="{{ old('name', $task->name) }}">
                                @error('name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6 col-lg-4">
                                <label for="start_time" class="form-label fw-semibold">Tanggal Mulai</label>
                                <input type="date"
                                    class="form-control modern-input @error('start_time') is-invalid @enderror"
                                    id="start_time" name="start_time" readonly
                                    value="{{ old('start_time', $task->start_time) }}">
                                @error('start_time')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6 col-lg-4">
                                <label for="end_time" class="form-label fw-semibold">Tanggal Selesai</label>
                                <input type="date"
                                    class="form-control modern-input @error('end_time') is-invalid @enderror" id="end_time"
                                    name="end_time" readonly value="{{ old('end_time', $task->end_time) }}">
                                @error('end_time')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="description" class="form-label fw-semibold">Deskripsi</label>
                                <input type="text"
                                    class="form-control modern-input @error('description') is-invalid @enderror"
                                    id="description" name="description" readonly
                                    value="{{ old('description', $task->description) }}">
                                @error('description')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                        </div>

                        <div class="col-12 mt-4">

                            <hr class="opacity-50">

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">Shift Tugas</h6>

                            </div>

                            <div id="position-wrapper">

                                @forelse ($shifts as $shift)
                                    <div class="row g-4 position-item align-items-end">

                                        <div class="col-sm-2">
                                            <label class="form-label">
                                                <i class="fa-solid fa-briefcase me-2 accent-icon"></i>
                                                Nama Shift
                                            </label>
                                            <input type="text" class="form-control" name="shift_name[]" readonly
                                                placeholder="Pagi" value="{{ $shift->name }}">
                                        </div>

                                        <div class="col-sm-2">
                                            <label class="form-label">
                                                <i class="fa-solid fa-money-bill me-2 accent-icon"></i>
                                                Jam Masuk
                                            </label>

                                            <div class="input-group shadow-sm">
                                                <span class="input-group-text"><i class="fa-regular fa-clock"></i></span>

                                                <input type="time" class="form-control money-input" name="shift_start[]"
                                                    readonly
                                                    value="{{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }}">
                                            </div>
                                        </div>

                                        <div class="col-sm-2">
                                            <label class="form-label">
                                                <i class="fa-solid fa-clock me-2 accent-icon"></i>
                                                Jam Keluar
                                            </label>

                                            <div class="input-group shadow-sm">
                                                <span class="input-group-text"><i class="fa-regular fa-clock"></i></span>

                                                <input type="time" class="form-control money-input" name="shift_end[]"
                                                    readonly
                                                    value="{{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }}">
                                            </div>
                                        </div>

                                        {{-- Toleransi telat --}}
                                        <div class="col-sm-2">
                                            <label class="form-label">
                                                <i class="fa-solid fa-clock me-2 accent-icon"></i>
                                                Toleransi Telat (Menit)
                                            </label>

                                            <div class="input-group shadow-sm">
                                                <span class="input-group-text"><i class="fa-regular fa-clock"></i></span>

                                                <input type="number" min="0" class="form-control money-input"
                                                    name="shift_late_tolerance[]" readonly
                                                    value="{{ $shift->late_tolerance_minutes }}">
                                            </div>
                                        </div>


                                    </div>
                                @empty
                                    <div class="alert alert-warning mb-0">
                                        Belum ada shift untuk tugas ini.
                                    </div>
                                @endforelse

                            </div>

                            <hr class="opacity-50">


                        </div>
                    </div>
                </div>

            </div>

            @if (in_array($userRole, ['hr', 'owner']))
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
                            <h5 class="card-title mb-0">
                                <i class="fa-solid fa-file-import me-2 text-danger"></i>
                                Import Jadwal Mingguan
                            </h5>
                        </div>

                        <form action="{{ route('task.importSchedule', $task->id) }}" method="POST"
                            enctype="multipart/form-data" class="row g-3 align-items-end">
                            @csrf
                            <div class="col-12 col-md-8">
                                <label for="schedule_file" class="form-label fw-semibold">File Excel Jadwal</label>
                                <input type="file" class="form-control @error('schedule_file') is-invalid @enderror"
                                    id="schedule_file" name="schedule_file" accept=".xlsx,.xls,.csv" required>
                                @error('schedule_file')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-12 col-md-4">
                                <button type="submit" class="btn btn-primary rounded-pill px-4 w-100">
                                    <i class="fa-solid fa-upload me-2"></i> Import Jadwal
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            {{-- Table Employee --}}
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4">

                    <h5 class="card-title">
                        <i class="fa-solid fa-list-check me-2 text-danger"></i>
                        Daftar Karyawan
                    </h5>

                    <div class="table-responsive">
                        <table class="table align-middle mb-0" id="EmployeeTable">
                            <thead>
                                <tr>
                                    <th>Nama Karyawan</th>
                                    <th>Divisi</th>
                                    <th>Posisi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($employees as $employee)
                                    <tr>
                                        <td class="fw-semibold text-dark">
                                            {{ ucwords($employee->fullname) }}
                                        </td>
                                        <td>
                                            {{ ucwords($employee->division->name) }}
                                        </td>
                                        <td>{{ ucwords($employee->position->name) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>

            {{-- Table Jadwal --}}
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-end flex-wrap gap-3 mb-3">
                        <div>
                            <h5 class="card-title mb-1">
                                <i class="fa-solid fa-calendar-week me-2 text-danger"></i>
                                Jadwal Mingguan
                            </h5>
                            <small class="text-muted">
                                Periode {{ $weekStart->format('d M Y') }} - {{ $weekEnd->format('d M Y') }}
                            </small>
                        </div>

                        <form action="{{ route('task.show', $task->id) }}" method="GET"
                            class="d-flex align-items-end gap-2">
                            <div>
                                <label for="schedule_week_start" class="form-label small fw-semibold mb-1">Minggu</label>
                                <input type="date" id="schedule_week_start" name="week_start"
                                    class="form-control form-control-sm" value="{{ $weekStart->format('Y-m-d') }}">
                            </div>
                            <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3">
                                <i class="fa-solid fa-eye me-1"></i> Lihat
                            </button>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle mb-0" id="jadwalTable">
                            <thead>
                                <tr>
                                    <th>Nama Karyawan</th>
                                    @foreach ($weekDates as $date)
                                        <th class="text-center">
                                            {{ $date->translatedFormat('D') }}<br>
                                            <span class="fw-normal">{{ $date->format('d M') }}</span>
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($employees as $employee)
                                    <tr>
                                        <td class="fw-semibold text-dark">
                                            {{ ucwords($employee->fullname) }}
                                            <div class="small text-muted">{{ ucwords($employee->position->name ?? '-') }}
                                            </div>
                                        </td>
                                        @foreach ($weekDates as $date)
                                            @php
                                                $schedule = $schedules
                                                    ->get($employee->id . '_' . $date->toDateString())
                                                    ?->first();
                                            @endphp
                                            <td class="text-center">
                                                @if ($schedule && $schedule->shift)
                                                    <span class="badge bg-light text-dark border d-block mb-1">
                                                        {{ ucwords($schedule->shift->name) }}
                                                    </span>
                                                    <small class="text-muted">
                                                        {{ \Carbon\Carbon::parse($schedule->shift->start_time)->format('H:i') }}
                                                        -
                                                        {{ \Carbon\Carbon::parse($schedule->shift->end_time)->format('H:i') }}
                                                    </small>
                                                @else
                                                    <span
                                                        class="badge bg-secondary-subtle text-secondary border">Libur</span>
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">
                                            Belum ada karyawan yang di-assign ke tugas ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>

                        </table>
                    </div>
                </div>
            </div>

            @if (in_array($userRole, ['hr', 'owner']))
                <div class="card shadow mt-3">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fa-solid fa-location-dot"></i> Lokasi Tugas
                        </h5>
                        <div id="map" style="height: 400px;"></div>
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <label>Latitude</label>
                                <input type="text" id="latitude" name="latitude" class="form-control" readonly
                                    value="{{ old('latitude', $locations->latitude) }}">
                            </div>
                            <div class="col-md-4">
                                <label>Longitude</label>
                                <input type="text" id="longitude" name="longitude" class="form-control" readonly
                                    value="{{ old('longitude', $locations->longitude) }}">
                            </div>
                            <div class="col-md-4">
                                <label>Radius (meter)</label>
                                <input type="number" id="radius" name="radius" class="form-control" readonly
                                    value="{{ old('radius', $locations->radius) }}">
                            </div>
                        </div>
                    </div>
                </div>
            @endif


        </div>
        {{-- Maps --}}
        <script src="{{ asset('leaflet/leaflet.js') }}"></script>
        <link rel="stylesheet" href="{{ asset('leaflet/leaflet.css') }}">
        {{-- Datatables & Jquery --}}
        <script src="{{ asset('js/jquery.min.js') }}"></script>
        <script src="{{ asset('DataTables/datatables.min.js') }}"></script>

        <script>
            // Table
            let table = new DataTable('#EmployeeTable', {
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                    paginate: {
                        previous: "Sebelumnya",
                        next: "Berikutnya",
                    },
                }
            });

            const lat = {{ $locations?->latitude }};
            const lng = {{ $locations?->longitude }};
            const radius = {{ $locations?->radius }};

            const map = L.map('map').setView([lat, lng], 15);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap'
            }).addTo(map);

            L.marker([lat, lng]).addTo(map);
            L.circle([lat, lng], {
                radius
            }).addTo(map);
        </script>


    @endsection
