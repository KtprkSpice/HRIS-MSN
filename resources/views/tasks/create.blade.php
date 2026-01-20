@extends('layout.dashboard')
@section('header', 'Buat Tugas')

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

    <form class="row g-3" action="{{ route('task.store') }}" method="post">
        @csrf
        <div class="card shadow">
            <div class="card-body">
                <div class="col-md-6">
                    <label for="name" class="form-label">Nama Tugas</label>
                    <input type="text"
                        class="form-control @error('name')
                is-invalid
            @enderror" id="name"
                        name="name" required value="{{ old('name') }}">
                    @error('name')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="start_time" class="form-label">Tanggal Mulai</label>
                    <input type="date"
                        class="form-control @error('start_time')
                is-invalid
            @enderror"
                        id="start_time" name="start_time" required value="{{ old('start_time') }}">
                    @error('start_time')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="end_time" class="form-label">Tanggal Selesai</label>
                    <input type="date"
                        class="form-control @error('end_time')
                is-invalid
            @enderror"
                        id="end_time" name="end_time" required value="{{ old('end_time') }}">
                    @error('end_time')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-12">
                    <label for="description" class="form-label">Deskripsi</label>
                    <input type="text"
                        class="form-control @error('description')
                is-invalid
            @enderror"
                        id="description" placeholder="" name="description" required value="{{ old('description') }}">
                    @error('description')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>
        <div class="card shadow">
            <div class="card-body">
                <h5 class="card-title"><i class="fa-solid fa-list-check"></i> Daftar Karyawan</h5>
                <div class="table-responsive">
                    <table id="employeeTable" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" id="selectAll">
                                    Pilih Semua
                                </th>
                                <th>Nama Karyawan</th>
                                <th>Divisi</th>
                                <th>Posisi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($employees as $employee)
                                <tr>
                                    <td>
                                        <input type="checkbox" name="selected_employee[]" value="{{ $employee->id }}">
                                    </td>
                                    <td>{{ ucwords($employee->fullname) }}</td>
                                    <td>{{ ucwords($employee->division->name) }}</td>
                                    <td> Posisi </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card shadow mt-3">
            <div class="card-body">
                <h5 class="card-title">
                    <i class="fa-solid fa-location-dot"></i> Lokasi Tugas
                </h5>

                <div id="map" style="height: 400px;"></div>

                <div class="row mt-3">
                    <div class="col-md-4">
                        <label>Latitude</label>
                        <input type="text" id="latitude" name="latitude" class="form-control" readonly>
                    </div>
                    <div class="col-md-4">
                        <label>Longitude</label>
                        <input type="text" id="longitude" name="longitude" class="form-control" readonly>
                    </div>
                    <div class="col-md-4">
                        <label>Radius (meter)</label>
                        <input type="number" id="radius" name="radius" class="form-control" value="50">
                    </div>
                </div>
            </div>
        </div>


        <div class="col-12">
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </form>
    {{-- Maps --}}
    <script src="{{ asset('leaflet/leaflet.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('leaflet/leaflet.css') }}">

    {{-- Jquery & datatables --}}
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('DataTables/datatables.min.js') }}"></script>

    <script>
        document.getElementById('selectAll').addEventListener('click', function() {
            const checkboxes = document.querySelectorAll('input[name="selected_employee[]"]');
            checkboxes.forEach(cb => cb.checked = this.checked);
        });

        // Table
        let table = new DataTable('#employeeTable', {
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
                targets: 0,
                orderable: false,
                searchable: false
            }]
        });


        const map = L.map('map').setView([-6.2, 106.8], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        let marker, circle;

        map.on('click', function(e) {
            const lat = e.latlng.lat;
            const lng = e.latlng.lng;
            const radius = document.getElementById('radius').value || 50;

            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;

            if (marker) map.removeLayer(marker);
            if (circle) map.removeLayer(circle);

            marker = L.marker([lat, lng]).addTo(map);
            circle = L.circle([lat, lng], {
                radius
            }).addTo(map);
        });

        document.getElementById('radius').addEventListener('input', function() {
            if (circle) {
                circle.setRadius(this.value);
            }
        });
    </script>

@endsection
