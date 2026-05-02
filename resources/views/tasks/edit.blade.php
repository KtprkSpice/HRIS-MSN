@extends('layout.dashboard')
@section('header', 'Edit Tugas')

@section('content')

    <style>
        /* ========================= */
        /* CARD STYLE CONSISTEN      */
        /* ========================= */
        .card {
            border: 0;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }

        .card-title {
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-title i {
            background: linear-gradient(135deg, #b83e48, #eb8697);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* ========================= */
        /* FORM STYLE                */
        /* ========================= */
        .form-label {
            font-weight: 500;
            color: #555;
        }

        .form-control {
            border-radius: 10px;
            border: 1px solid #e5e7eb;
            padding: 10px 14px;
            transition: 0.2s;
        }

        .form-control:focus {
            border-color: #dc3545;
            box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.15);
        }

        /* ========================= */
        /* TABLE STYLE GRADIENT      */
        /* ========================= */
        #employeeTable thead {
            background: linear-gradient(100deg, #b83e48 0%, #eb8697 100%);
        }

        #employeeTable thead th {
            background: transparent !important;
            color: #fff !important;
            font-weight: 600;
            border: none !important;
            padding: 14px;
        }

        #employeeTable tbody tr {
            background: #fff;
            transition: 0.2s;
        }

        #employeeTable tbody tr:nth-child(even) {
            background: #fdf2f4;
        }

        #employeeTable tbody tr:hover {
            background: #fce7eb;
        }

        #employeeTable td {
            padding: 14px;
            vertical-align: middle;
        }

        .table>:not(caption)>*>* {
            border-bottom-width: 0px !important;
        }

        /* ========================= */
        /* BUTTON STYLE              */
        /* ========================= */
        .btn-primary {
            background: linear-gradient(100deg, #b83e48 0%, #eb8697 100%);
            border: none;
            border-radius: 10px;
            padding: 10px 22px;
            font-weight: 500;
            transition: 0.3s;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(184, 62, 72, 0.25);
        }
    </style>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form class="row g-4" action="{{ route('task.update', $task->id) }}" method="post">
        @method('PUT')
        @csrf

        <div class="card shadow-sm mb-4">
            <div class="card-body">

                <div class="row g-4">

                    <div class="col-12 col-md-6 col-lg-4">
                        <label for="name" class="form-label fw-semibold">Nama Tugas</label>
                        <input type="text" id="name" name="name" required
                            class="form-control modern-input @error('name') is-invalid @enderror"
                            value="{{ old('name', $task->name) }}">
                        @error('name')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6 col-lg-4">
                        <label for="start_time" class="form-label fw-semibold">Tanggal Mulai</label>
                        <input type="date" id="start_time" name="start_time" required
                            class="form-control modern-input @error('start_time') is-invalid @enderror"
                            value="{{ old('start_time', $task->start_time) }}">
                        @error('start_time')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6 col-lg-4">
                        <label for="end_time" class="form-label fw-semibold">Tanggal Selesai</label>
                        <input type="date" id="end_time" name="end_time" required
                            class="form-control modern-input @error('end_time') is-invalid @enderror"
                            value="{{ old('end_time', $task->end_time) }}">
                        @error('end_time')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="description" class="form-label fw-semibold">Deskripsi</label>
                        <textarea id="description" name="description" rows="4" required
                            class="form-control modern-input @error('description') is-invalid @enderror">{{ old('description', $task->description) }}</textarea>
                        @error('description')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                </div>

            </div>
        </div>
        {{-- Table Employee --}}
        <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-body p-4">

                <h5 class="card-title fw-semibold mb-3">
                    <i class="fa-solid fa-list-check me-2 text-danger"></i>
                    Daftar Karyawan
                </h5>

                <div class="table-responsive">
                    <table id="employeeTable" class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="width: 120px;">
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
                                        <input type="checkbox" class="form-check-input" name="selected_employee[]"
                                            value="{{ $employee->id }}"
                                            {{ in_array($employee->id, $task->employees->pluck('id')->toArray()) ? 'checked' : '' }}>
                                    </td>
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

        {{-- Lokasi --}}
        <div class="card shadow mt-3">
            <div class="card-body">
                <h5 class="card-title">
                    <i class="fa-solid fa-location-dot"></i> Lokasi Tugas
                </h5>

                <div id="map" style="height: 400px;"></div>

                <div class="row mt-3">
                    <div class="col-md-4">
                        <label>Latitude</label>
                        <input type="text" id="latitude" name="latitude" class="form-control"
                            value="{{ old('latitude', $locations->latitude) }}">
                    </div>
                    <div class="col-md-4">
                        <label>Longitude</label>
                        <input type="text" id="longitude" name="longitude" class="form-control"
                            value="{{ old('longitude', $locations->longitude) }}">
                    </div>
                    <div class="col-md-4">
                        <label>Radius (meter)</label>
                        <input type="number" id="radius" name="radius" class="form-control"
                            value="{{ old('radius', $locations->radius) }}">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-15">
            <button type="submit" class="btn btn-primary px-3">Submit</button>
        </div>
    </form>

    {{-- Maps --}}
    <script src="{{ asset('leaflet/leaflet.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('leaflet/leaflet.css') }}">
    {{-- Jquery & datatables --}}
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('DataTables/datatables.min.js') }}"></script>

    <script>
        const form = document.querySelector('form');
        const selectAll = document.getElementById('selectAll');
        const allEmployeeIds = @json($employees->pluck('id')->map(fn ($id) => (string) $id)->values());
        const selectedEmployeeIds = new Set(@json(collect(old('selected_employee', $task->employees->pluck('id')->toArray()))->map(fn ($id) => (string) $id)->values()));

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

        function currentPageCheckboxes() {
            return Array.from(table.rows({
                page: 'current'
            }).nodes()).map(row => row.querySelector('input[name="selected_employee[]"]')).filter(Boolean);
        }

        function syncCurrentPageCheckboxes() {
            currentPageCheckboxes().forEach(checkbox => {
                checkbox.checked = selectedEmployeeIds.has(checkbox.value);
            });
            updateSelectAllState();
        }

        function updateSelectAllState() {
            const selectedCount = allEmployeeIds.filter(id => selectedEmployeeIds.has(id)).length;

            selectAll.checked = allEmployeeIds.length > 0 && selectedCount === allEmployeeIds.length;
            selectAll.indeterminate = selectedCount > 0 && selectedCount < allEmployeeIds.length;
        }

        document.getElementById('employeeTable').addEventListener('change', function(event) {
            if (!event.target.matches('input[name="selected_employee[]"]')) {
                return;
            }

            if (event.target.checked) {
                selectedEmployeeIds.add(event.target.value);
            } else {
                selectedEmployeeIds.delete(event.target.value);
            }

            updateSelectAllState();
        });

        selectAll.addEventListener('change', function() {
            if (this.checked) {
                allEmployeeIds.forEach(id => selectedEmployeeIds.add(id));
            } else {
                selectedEmployeeIds.clear();
            }

            syncCurrentPageCheckboxes();
        });

        table.on('draw', syncCurrentPageCheckboxes);
        syncCurrentPageCheckboxes();

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

        form.addEventListener('submit', function() {
            form.querySelectorAll('.selected-employee-hidden').forEach(input => input.remove());
            document.querySelectorAll('input[name="selected_employee[]"]').forEach(input => {
                input.disabled = true;
            });

            selectedEmployeeIds.forEach(function(value) {
                let input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'selected_employee[]';
                input.value = value;
                input.className = 'selected-employee-hidden';
                form.appendChild(input);
            });
        });
    </script>


@endsection
