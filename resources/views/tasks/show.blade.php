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

    <div class="row g-3">
        {{-- Detail Tugas --}}
        <div class="card shadow">
            <div class="card-body">
                <div class="col-md-6">
                    <label for="name" class="form-label">Nama Tugas</label>
                    <input type="text" readonly
                        class="form-control @error('name')
                is-invalid
                @enderror"
                        id="name" name="name" required value="{{ old('name', $task->name) }}">
                    @error('name')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="start_time" class="form-label">Tanggal Mulai</label>
                    <input type="date" readonly
                        class="form-control @error('start_time')
                is-invalid
                 @enderror"
                        id="start_time" name="start_time" required value="{{ old('start_time', $task->start_time) }}">
                    @error('start_time')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="end_time" class="form-label">Tanggal Selesai</label>
                    <input type="date" readonly
                        class="form-control @error('end_time')
                is-invalid
            @enderror"
                        id="end_time" name="end_time" required value="{{ old('end_time', $task->end_time) }}">
                    @error('end_time')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-12">
                    <label for="description" class="form-label">Deskripsi</label>
                    <input type="text" readonly
                        class="form-control @error('description')
                is-invalid
            @enderror"
                        id="description" placeholder="" name="description" required
                        value="{{ old('description', $task->description) }}">
                    @error('description')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Table tugas --}}
        <div class="card shadow">
            <div class="card-body">
                <h5 class="card-title"><i class="fa-solid fa-list-check"></i> Daftar Karyawan</h5>
                <div class="table-responsive">
                    <table class="table table-bordered" id="EmployeeTable">
                        <thead>
                            <tr>
                                <th>Nama Karyawan</th>
                                <th>Divisi</th>
                                <th>Posisi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($employees as $employee)
                                <tr> {{-- PERBAIKAN: Tambahkan tag <tr> yang hilang --}}
                                    <td>{{ ucwords($employee->fullname) }}</td>
                                    <td>{{ ucwords($employee->division->name) }}</td>
                                    <td>{{ ucwords($employee->position->name) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Table Jadwal --}}
        <div class="card shadow">
            <div class="card-body">
                <h5 class="card-title"><i class="fa-solid fa-list-check"></i> Jadwal Tugas</h5>
                <div class="table-responsive">
                    <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                        {{-- PERBAIKAN: Ubah ID dan value radio button --}}
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
                    <table class="table table-bordered mt-3">
                        <thead>
                            <tr>
                                <th>Nama Karyawan</th>
                                <th>Shift</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        {{-- PERBAIKAN: Gunakan tbody dengan ID yang sesuai dengan radio button --}}
                        <tbody id="pagi">
                            @foreach ($schedules['1'] ?? [] as $schedule)
                                <tr>
                                    <td>{{ $schedule->employee->fullname ?? 'N/A' }}</td>
                                    <td>{{ $schedule->shift->name ?? 'N/A' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($schedule->date)->format('d F Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>

                        <tbody id="sore" style="display:none">
                            @foreach ($schedules['2'] ?? [] as $schedule)
                                <tr>
                                    <td>{{ $schedule->employee->fullname ?? 'N/A' }}</td>
                                    <td>{{ $schedule->shift->name ?? 'N/A' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($schedule->date)->format('d F Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>

                        <tbody id="malam" style="display:none">
                            @foreach ($schedules['3'] ?? [] as $schedule)
                                <tr>
                                    <td>{{ $schedule->employee->fullname ?? 'N/A' }}</td>
                                    <td>{{ $schedule->shift->name ?? 'N/A' }}</td>
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
