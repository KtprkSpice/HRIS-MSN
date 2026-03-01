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
        <div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="card-body p-4">

        <h5 class="card-title fw-semibold mb-4 d-flex align-items-center gap-2">
            <div class="rounded-3 d-flex align-items-center justify-content-center"
                style="width:42px;height:42px;
                background:linear-gradient(135deg,#b83e48,#eb8697); color:white;">
                <i class="fa-solid fa-clipboard-list"></i>
            </div>
            Informasi Tugas
        </h5>

        <div class="row g-4">

            <div class="col-12 col-md-6 col-lg-4">
                <label for="name" class="form-label fw-semibold">Nama Tugas</label>
                <input type="text"
                    class="form-control modern-input @error('name') is-invalid @enderror"
                    id="name" name="name" required value="{{ old('name') }}">
                @error('name')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="col-12 col-md-6 col-lg-4">
                <label for="start_time" class="form-label fw-semibold">Tanggal Mulai</label>
                <input type="date"
                    class="form-control modern-input @error('start_time') is-invalid @enderror"
                    id="start_time" name="start_time" required value="{{ old('start_time') }}">
                @error('start_time')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="col-12 col-md-6 col-lg-4">
                <label for="end_time" class="form-label fw-semibold">Tanggal Selesai</label>
                <input type="date"
                    class="form-control modern-input @error('end_time') is-invalid @enderror"
                    id="end_time" name="end_time" required value="{{ old('end_time') }}">
                @error('end_time')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="col-12">
                <label for="description" class="form-label fw-semibold">Deskripsi</label>
                <input type="text"
                    class="form-control modern-input @error('description') is-invalid @enderror"
                    id="description" name="description" required value="{{ old('description') }}">
                @error('description')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

        </div>
    </div>
</div>
       <div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="card-body p-4">

        <h5 class="card-title fw-semibold mb-3 d-flex align-items-center gap-2">
            <div class="rounded-3 d-flex align-items-center justify-content-center"
                style="width:42px;height:42px;
                background:linear-gradient(135deg,#b83e48,#eb8697); color:white;">
                <i class="fa-solid fa-users"></i>
            </div>
            Daftar Karyawan
        </h5>

        <div class="table-responsive">
            <table id="employeeTable" class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width:120px;">
                            <input type="checkbox" id="selectAll" class="form-check-input me-2">
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
                                <input type="checkbox" name="selected_employee[]"
                                    value="{{ $employee->id }}" class="form-check-input">
                            </td>
                            <td class="fw-semibold text-dark">
                                {{ ucwords($employee->fullname) }}
                            </td>
                            <td>{{ ucwords($employee->division->name) }}</td>
                            <td>Posisi</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
</div>

       <div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="card-body p-4">

        <h5 class="card-title fw-semibold mb-3 d-flex align-items-center gap-2">
            <div class="rounded-3 d-flex align-items-center justify-content-center"
                style="width:42px;height:42px;
                background:linear-gradient(135deg,#b83e48,#eb8697); color:white;">
                <i class="fa-solid fa-location-dot"></i>
            </div>
            Lokasi Tugas
        </h5>

        <div id="map" style="height:400px;border-radius:14px;"></div>

        <div class="row mt-4 g-4">
            <div class="col-md-4">
                <label class="fw-semibold">Latitude</label>
                <input type="text" id="latitude" name="latitude"
                    class="form-control modern-input" readonly>
            </div>
            <div class="col-md-4">
                <label class="fw-semibold">Longitude</label>
                <input type="text" id="longitude" name="longitude"
                    class="form-control modern-input" readonly>
            </div>
            <div class="col-md-4">
                <label class="fw-semibold">Radius (meter)</label>
                <input type="number" id="radius" name="radius"
                    class="form-control modern-input" value="50">
            </div>
        </div>

    </div>
</div>
<style>
.modern-input {
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    padding: 12px 14px;
    transition: 0.2s ease;
}

.modern-input:focus {
    border-color: #dc3545;
    box-shadow: 0 0 0 3px rgba(220,53,69,0.15);
}

/* TABLE */
#employeeTable thead {
    background: linear-gradient(100deg, #b83e48 0%, #eb8697 100%);
}

#employeeTable thead th {
    color: #af3434;
    font-weight: 600;
    border: none;
    padding: 14px;
}

#employeeTable tbody tr:nth-child(even){
    background:#ffe6e6;
}

#employeeTable tbody tr:hover{
    background:#ffb3b3;
}
#employeeTable tbody tr {
    background: linear-gradient(90deg, #b83e48, #eb8697); /* gradien merah horizontal */
    color: white; /* teks tetap terbaca */
}

.table>:not(caption)>*>*{
    border-bottom-width:0px !important;
}

/* BUTTON */
.btn-primary{
    background: linear-gradient(100deg,#b83e48,#eb8697);
    border:none;
    border-radius:10px;
    padding:10px 24px;
}

</style>


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
