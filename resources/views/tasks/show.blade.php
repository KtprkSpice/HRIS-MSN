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

                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-5 p-md-4">

                        {{-- Header Section --}}
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
                        </div>

                        {{-- Form Content --}}
                        <div class="row g-4">

                            <div class="col-12 col-md-6 col-lg-4">
                                <label class="form-label fw-semibold">Nama Tugas</label>
                                <input type="text" readonly
                                    class="form-control modern-input @error('name') is-invalid @enderror"
                                    value="{{ old('name', $task->name) }}">
                                @error('name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6 col-lg-4">
                                <label class="form-label fw-semibold">Tanggal Mulai</label>
                                <input type="date" readonly
                                    class="form-control modern-input @error('start_time') is-invalid @enderror"
                                    value="{{ old('start_time', $task->start_time) }}">
                                @error('start_time')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6 col-lg-4">
                                <label class="form-label fw-semibold">Tanggal Selesai</label>
                                <input type="date" readonly
                                    class="form-control modern-input @error('end_time') is-invalid @enderror"
                                    value="{{ old('end_time', $task->end_time) }}">
                                @error('end_time')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Deskripsi</label>
                                <textarea readonly rows="4" class="form-control modern-input @error('description') is-invalid @enderror">{{ old('description', $task->description) }}</textarea>
                                @error('description')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                        </div>

                    </div>
                </div>

            </div>

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

                    <h5 class="card-title mb-3">
                        <i class="fa-solid fa-list-check me-2 text-danger"></i>
                        Jadwal Tugas
                    </h5>

                    <div class="btn-group mb-3" role="group">
                        <input type="radio" class="btn-check" name="shift" id="btn-pagi" value="pagi"
                            autocomplete="off" checked>
                        <label class="btn btn-outline-primary" for="btn-pagi">Pagi</label>

                        <input type="radio" class="btn-check" name="shift" id="btn-sore" value="sore"
                            autocomplete="off">
                        <label class="btn btn-outline-primary" for="btn-sore">Sore</label>

                        <input type="radio" class="btn-check" name="shift" id="btn-malam" value="malam"
                            autocomplete="off">
                        <label class="btn btn-outline-primary" for="btn-malam">Malam</label>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle mb-0" id="jadwalTable">
                            <thead>
                                <tr>
                                    <th>Nama Karyawan</th>
                                    <th>Shift</th>
                                    <th>Jam Masuk</th>
                                    <th>Jam Keluar</th>
                                    <th>Tanggal</th>
                                </tr>
                            </thead>

                            <tbody id="pagi">
                                @foreach ($schedules['1'] ?? [] as $schedule)
                                    <tr>
                                        <td>{{ $schedule->employee->fullname ?? 'N/A' }}</td>
                                        <td>{{ $schedule->shift->name ?? 'N/A' }}</td>
                                        <td>{{ \Carbon\Carbon::parse($schedule->shift->start_time)->format('H:i') ?? 'N/A' }}
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($schedule->shift->end_time)->format('H:i') ?? 'N/A' }}
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($schedule->date)->format('d F Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>

                            <tbody id="sore" style="display:none">
                                @foreach ($schedules['2'] ?? [] as $schedule)
                                    <tr>
                                        <td>{{ $schedule->employee->fullname ?? 'N/A' }}</td>
                                        <td>{{ $schedule->shift->name ?? 'N/A' }}</td>
                                        <td>{{ \Carbon\Carbon::parse($schedule->shift->start_time)->format('H:i') ?? 'N/A' }}
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($schedule->shift->end_time)->format('H:i') ?? 'N/A' }}
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($schedule->date)->format('d F Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>

                            <tbody id="malam" style="display:none">
                                @foreach ($schedules['3'] ?? [] as $schedule)
                                    <tr>
                                        <td>{{ $schedule->employee->fullname ?? 'N/A' }}</td>
                                        <td>{{ $schedule->shift->name ?? 'N/A' }}</td>
                                        <td>{{ \Carbon\Carbon::parse($schedule->shift->start_time)->format('H:i') ?? 'N/A' }}
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($schedule->shift->end_time)->format('H:i') ?? 'N/A' }}
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($schedule->date)->format('d F Y') }}</td>
                                    </tr>
                                @endforeach
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

            // PERBAIKAN: Fungsi untuk menampilkan shift yang sesuai
            function showShift(shiftId) {
                // Sembunyikan semua tbody
                document.getElementById('pagi').style.display = 'none';
                document.getElementById('sore').style.display = 'none';
                document.getElementById('malam').style.display = 'none';

                // Tampilkan tbody yang sesuai
                document.getElementById(shiftId).style.display = '';
            }

            // PERBAIKAN: Event listener untuk radio button
            document.addEventListener('DOMContentLoaded', function() {
                // Set tampilan awal (pagi)
                showShift('pagi');

                // Event listener untuk radio button
                document.getElementById('btn-pagi').addEventListener('click', function() {
                    showShift('pagi');
                });

                document.getElementById('btn-sore').addEventListener('click', function() {
                    showShift('sore');
                });

                document.getElementById('btn-malam').addEventListener('click', function() {
                    showShift('malam');
                });
            });

            // Optional: Jika ingin menggunakan jQuery untuk toggle yang lebih smooth
            $(document).ready(function() {
                $('input[name="shift"]').change(function() {
                    var selectedShift = $(this).val();
                    $('#pagi, #sore, #malam').hide();
                    $('#' + selectedShift).show();
                });
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
