@extends('layout.dashboard')
@section('header', 'Buat Tugas')

@section('content')

    <style>
        .btn-add-position {
            background: linear-gradient(90deg, #bc5e6b 0%, #a34a57 100%);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 0.35rem 0.75rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-add-position:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(188, 94, 107, 0.3);
            color: white;
        }

        .position-item {
            margin-bottom: 1rem;
        }

        .btn-delete-position {
            width: 45px;
            height: 45px;
            border: none;
            border-radius: 10px;
            background: linear-gradient(135deg, #bc5e6b 0%, #a34a57 100%);
            color: white;
            transition: all .3s ease;
            box-shadow: 0 4px 12px rgba(188, 94, 107, .25);
        }

        .btn-delete-position:hover {
            background: linear-gradient(135deg, #c96d7a 0%, #bc5e6b 100%);
            transform: translateY(-3px);
            box-shadow: 0 8px 18px rgba(188, 94, 107, .35);
            color: white;
        }

        .btn-delete-position i {
            font-size: 14px;
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

    <form id="taskForm" class="row g-3" action="{{ route('task.store') }}" method="post">
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
                        <label for="name" class="form-label fw-semibold">Nama Perusahaan</label>
                        <input type="text" class="form-control modern-input @error('name') is-invalid @enderror"
                            id="name" name="name" required value="{{ old('name') }}">
                        @error('name')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6 col-lg-4">
                        <label for="start_time" class="form-label fw-semibold">Tanggal Mulai</label>
                        <input type="date" class="form-control modern-input @error('start_time') is-invalid @enderror"
                            id="start_time" name="start_time" required value="{{ old('start_time') }}">
                        @error('start_time')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6 col-lg-4">
                        <label for="end_time" class="form-label fw-semibold">Tanggal Selesai</label>
                        <input type="date" class="form-control modern-input @error('end_time') is-invalid @enderror"
                            id="end_time" name="end_time" required value="{{ old('end_time') }}">
                        @error('end_time')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="description" class="form-label fw-semibold">Deskripsi</label>
                        <input type="text" class="form-control modern-input @error('description') is-invalid @enderror"
                            id="description" name="description" required value="{{ old('description') }}">
                        @error('description')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                </div>

                <div class="col-12 mt-4">

                    <hr class="opacity-50">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="mb-0">Shift Tugas</h6>
                            <small class="text-muted">Shift ini dipakai sebagai pilihan saat export/import jadwal
                                mingguan.</small>
                        </div>

                        <button type="button" class="btn btn-sm btn-add-position" id="add-position">
                            <i class="fa fa-plus"></i> Tambah
                        </button>
                    </div>

                    <div id="position-wrapper">

                        <div class="row g-4 position-item align-items-end">

                            <div class="col-sm-2">
                                <label class="form-label">
                                    <i class="fa-solid fa-briefcase me-2 accent-icon"></i>
                                    Nama Shift
                                </label>
                                <input type="text" class="form-control" name="shift_name[]" required placeholder="Pagi">
                            </div>

                            <div class="col-sm-2">
                                <label class="form-label">
                                    <i class="fa-solid fa-money-bill me-2 accent-icon"></i>
                                    Jam Masuk
                                </label>

                                <div class="input-group shadow-sm">
                                    <span class="input-group-text"><i class="fa-regular fa-clock"></i></span>

                                    <input type="time" class="form-control money-input" name="shift_start[]" required>
                                </div>
                            </div>

                            <div class="col-sm-2">
                                <label class="form-label">
                                    <i class="fa-solid fa-clock me-2 accent-icon"></i>
                                    Jam Keluar
                                </label>

                                <div class="input-group shadow-sm">
                                    <span class="input-group-text"><i class="fa-regular fa-clock"></i></span>

                                    <input type="time" class="form-control money-input" name="shift_end[]" required>
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
                                        name="shift_late_tolerance[]" required>
                                </div>
                            </div>

                            <div class="col-md-1">
                                <button type="button" class="btn-delete-position delete-position" title="Hapus Posisi">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>

                        </div>

                    </div>

                    <hr class="opacity-50">

                    {{-- <div class="d-flex justify-content-end gap-2">
                        <a href="{{ url()->previous() }}" class="btn btn-back shadow-sm">
                            <i class="fa-solid fa-arrow-left me-2"></i>
                            Batal
                        </a>

                        <button type="submit" class="btn btn-primary btn-save text-white shadow-sm">
                            <i class="fa-solid fa-save me-2"></i>
                            Simpan Data
                        </button>
                    </div> --}}

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
                                <th style="width:90px;" class="text-nowrap">
                                    <div class="d-flex align-items-center gap-1 small">
                                        <input type="checkbox" id="selectAll" class="form-check-input"
                                            style="transform: scale(0.9);">
                                        <span>Pilih</span>
                                    </div>
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
                                        <input type="checkbox" name="selected_employee[]" value="{{ $employee->id }}"
                                            class="form-check-input"
                                            {{ in_array($employee->id, old('selected_employee', [])) ? 'checked' : '' }}>
                                    </td>
                                    <td class="fw-semibold text-dark">
                                        {{ ucwords($employee->fullname) }}
                                    </td>
                                    <td>{{ ucwords($employee->division->name) }}</td>
                                    <td>{{ ucwords($employee->position->name) }}</td>
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
                        <input type="text" id="latitude" name="latitude" class="form-control modern-input">
                    </div>
                    <div class="col-md-4">
                        <label class="fw-semibold">Longitude</label>
                        <input type="text" id="longitude" name="longitude" class="form-control modern-input">
                    </div>
                    <div class="col-md-4">
                        <label class="fw-semibold">Radius (meter)</label>
                        <input type="number" id="radius" name="radius" class="form-control modern-input"
                            value="50">
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
                box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.15);
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

            #employeeTable tbody tr:nth-child(even) {
                background: #ffe6e6;
            }

            #employeeTable tbody tr:hover {
                background: #ffb3b3;
            }

            #employeeTable tbody tr {
                background: linear-gradient(90deg, #b83e48, #eb8697);
                /* gradien merah horizontal */
                color: white;
                /* teks tetap terbaca */
            }

            .table>:not(caption)>*>* {
                border-bottom-width: 0px !important;
            }

            /* BUTTON */
            .btn-primary {
                background: linear-gradient(100deg, #b83e48, #eb8697);
                border: none;
                border-radius: 10px;
                padding: 10px 24px;
            }
        </style>


        <div class="col-12">
            <div style="display:flex; justify-content:flex-end;">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>
    </form>
    {{-- Maps --}}
    <script src="{{ asset('leaflet/leaflet.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('leaflet/leaflet.css') }}">

    {{-- Jquery & datatables --}}
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('DataTables/datatables.min.js') }}"></script>

    <script>
        const form = document.getElementById('taskForm');
        const selectAll = document.getElementById('selectAll');
        const allEmployeeIds = @json($employees->pluck('id')->map(fn($id) => (string) $id)->values());
        const selectedEmployeeIds = new Set(@json(collect(old('selected_employee', []))->map(fn($id) => (string) $id)->values()));

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
        const latitudeInput = document.getElementById('latitude');
        const longitudeInput = document.getElementById('longitude');
        const radiusInput = document.getElementById('radius');

        function syncMapFromCoordinateInputs(recenter = true) {
            const lat = Number.parseFloat(latitudeInput.value);
            const lng = Number.parseFloat(longitudeInput.value);

            if (!Number.isFinite(lat) || !Number.isFinite(lng) || lat < -90 || lat > 90 || lng < -180 || lng > 180) {
                return false;
            }

            const location = [lat, lng];
            const radius = Number.parseFloat(radiusInput.value) || 50;

            if (marker) {
                marker.setLatLng(location);
            } else {
                marker = L.marker(location).addTo(map);
            }

            if (circle) {
                circle.setLatLng(location).setRadius(radius);
            } else {
                circle = L.circle(location, {
                    radius
                }).addTo(map);
            }

            if (recenter) {
                map.setView(location, Math.max(map.getZoom(), 15));
            }

            return true;
        }

        map.on('click', function(e) {
            const lat = e.latlng.lat;
            const lng = e.latlng.lng;

            latitudeInput.value = lat.toFixed(7);
            longitudeInput.value = lng.toFixed(7);
            syncMapFromCoordinateInputs(false);
        });

        [latitudeInput, longitudeInput].forEach(input => {
            input.addEventListener('input', () => syncMapFromCoordinateInputs());
            input.addEventListener('change', () => syncMapFromCoordinateInputs());
        });

        radiusInput.addEventListener('input', () => syncMapFromCoordinateInputs(false));
        syncMapFromCoordinateInputs();

        form.addEventListener('submit', function() {
            form.querySelectorAll('.selected-employee-hidden').forEach(input => input.remove());
            form.querySelectorAll('input[name="selected_employee[]"]').forEach(input => {
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

        // Scripy Jadawl kerja
        document.getElementById('add-position').addEventListener('click', function() {

            let wrapper = document.getElementById('position-wrapper');
            let firstItem = wrapper.querySelector('.position-item');

            let clone = firstItem.cloneNode(true);

            clone.querySelectorAll('input').forEach(input => {
                input.value = '';
            });

            wrapper.appendChild(clone);
        });

        document.addEventListener('click', function(e) {

            let deleteButton = e.target.closest('.delete-position');

            if (!deleteButton) return;

            let items = document.querySelectorAll('.position-item');

            if (items.length <= 1) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Tidak Bisa Dihapus',
                    text: 'Minimal harus ada 1 data Shift.',
                    confirmButtonColor: '#bc5e6b'
                });
                return;
            }

            Swal.fire({
                title: 'Hapus Shift?',
                text: 'Data Shift ini akan dihapus dari form.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {

                if (result.isConfirmed) {

                    deleteButton.closest('.position-item').remove();

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Data Shift berhasil dihapus.',
                        timer: 1500,
                        showConfirmButton: false
                    });

                }

            });

        });
    </script>

@endsection
