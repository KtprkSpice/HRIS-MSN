@extends('layout.dashboard')
@section('header', 'Edit Data Kehadiran')

@section('content')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        .select2-container--default .select2-selection--single {
            height: 45px !important;
            padding: 8px !important;
            border: 1px solid #dee2e6 !important;
            border-radius: 8px !important;
            box-shadow: 0 .125rem .25rem rgba(0, 0, 0, .075) !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 42px !important;
        }

        .select2-dropdown {
            border: 1px solid #bc5e6b !important;
            border-radius: 8px !important;
        }

        .btn-back {
            border-radius: 10px;
            background-color: #f8f9fa;
            color: #6c757d;
            border: 1px solid #dee2e6;
            transition: all 0.3s;
        }

        .btn-back:hover {
            background-color: #e2e6ea;
            color: #495057;
        }
    </style>

    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow border-0">

                <!-- HEADER -->
                <div class="card-header text-white"
                    style="background: linear-gradient(90deg, #bc5e6b 0%, #a34a57 100%); border: none; padding: 1.2rem;">
                    <h5 class="mb-0 fw-bold">
                        <i class="fa-solid fa-pen-to-square me-2"></i> Edit Data Kehadiran
                    </h5>
                </div>

                <!-- BODY -->
                <div class="card-body p-4">

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0 small">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                                    @if (session('warning'))
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        {{ session('warning') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

                    <!-- FORM (TIDAK DIUBAH LOGIKA) -->
                    <form action="{{ route('presence.update', $presence->id) }}" method="post">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="employee_id" class="form-label fw-bold text-secondary">
                                    <i class="fa-solid fa-user me-2" style="color: #bc5e6b;"></i> Nama Karyawan
                                </label>
                                <select name="employee_id" id="employee_id"
                                    class="form-control select2-js @error('employee_id') is-invalid @enderror">
                                    <option value="">-- Pilih Karyawan --</option>
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}"
                                            {{ old('employee_id', $presence->employee_id) == $employee->id ? 'selected' : '' }}>
                                            {{ ucwords($employee->fullname) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('employee_id')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="task_id" class="form-label fw-bold text-secondary">
                                    <i class="fa-solid fa-tasks me-2" style="color: #bc5e6b;"></i> Nama Tugas
                                </label>
                                <select name="task_id" id="task_id"
                                    class="form-control select2-js @error('task_id') is-invalid @enderror">
                                    <option value="">-- Pilih Tugas --</option>
                                </select>
                                @error('task_id')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="shift_id" class="form-label fw-bold text-secondary">
                                    <i class="fa-solid fa-calendar-day me-2" style="color: #bc5e6b;"></i> Shift
                                </label>
                                <select name="shift_id" id="shift_id"
                                    class="form-control select2-js @error('shift_id') is-invalid @enderror">
                                    <option value="">-- Pilih Shift --</option>
                                </select>
                                @error('shift_id')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="date" class="form-label fw-bold text-secondary">
                                    <i class="fa-solid fa-calendar-day me-2" style="color: #bc5e6b;"></i> Tanggal
                                </label>
                                <input type="date" class="form-control shadow-sm @error('date') is-invalid @enderror"
                                    id="date" name="date" required
                                    value="{{ old('date', $presence->date?->format('Y-m-d')) }}"
                                    style="height: 45px; border-radius: 8px;">
                                @error('date')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="check_in" class="form-label fw-bold text-secondary">
                                    <i class="fa-solid fa-right-to-bracket me-2" style="color: #bc5e6b;"></i> Jam Masuk
                                </label>
                                <input type="datetime-local"
                                    class="form-control shadow-sm @error('check_in') is-invalid @enderror" id="check_in"
                                    name="check_in" required
                                    value="{{ old('check_in', $presence->check_in ? \Carbon\Carbon::parse($presence->check_in)->format('Y-m-d\TH:i') : '') }}"
                                    style="height: 45px; border-radius: 8px;">
                                @error('check_in')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="check_out" class="form-label fw-bold text-secondary">
                                    <i class="fa-solid fa-right-from-bracket me-2" style="color: #bc5e6b;"></i> Jam Keluar
                                </label>
                                <input type="datetime-local"
                                    class="form-control shadow-sm @error('check_out') is-invalid @enderror" id="check_out"
                                    name="check_out"
                                    value="{{ old('check_out', $presence->check_out ? \Carbon\Carbon::parse($presence->check_out)->format('Y-m-d\TH:i') : '') }}"
                                    style="height: 45px; border-radius: 8px;">
                                @error('check_out')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4 opacity-50">

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-lg text-white shadow"
                                style="background: linear-gradient(90deg, #bc5e6b 0%, #a34a57 100%); border: none; border-radius: 10px;">
                                <i class="fa-solid fa-save me-2"></i> Update Data Kehadiran
                            </button>
                            <a href="{{ route('presence.index') }}" class="btn btn-lg btn-back shadow-sm">
                                <i class="fa-solid fa-arrow-left me-2"></i> Batal & Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            $('.select2-js').select2({
                width: '100%'
            });

            const scheduleOptions = @json($scheduleOptions);
            const initialTaskId = @json((string) old('task_id', $presence->task_id));
            const initialShiftId = @json((string) old('shift_id', $presence->shift_id));

            function matchingSchedules() {
                const employeeId = $('#employee_id').val();
                const date = $('#date').val();

                return scheduleOptions.filter((schedule) => {
                    return schedule.employee_id === employeeId && schedule.date === date;
                });
            }

            function resetSelect(selector, placeholder) {
                $(selector).empty().append(new Option(placeholder, ''));
            }

            function refreshTasks() {
                const schedules = matchingSchedules();
                const selectedTask = $('#task_id').val() || initialTaskId;
                const tasks = new Map();

                resetSelect('#task_id', schedules.length ? '-- Pilih Tugas --' : '-- Tidak ada jadwal --');

                schedules.forEach((schedule) => {
                    if (!tasks.has(schedule.task_id)) {
                        tasks.set(schedule.task_id, schedule.task_name);
                    }
                });

                tasks.forEach((taskName, taskId) => {
                    $('#task_id').append(new Option(taskName, taskId));
                });

                if (tasks.has(selectedTask)) {
                    $('#task_id').val(selectedTask);
                } else if (tasks.size === 1) {
                    $('#task_id').val([...tasks.keys()][0]);
                } else {
                    $('#task_id').val('');
                }

                $('#task_id').trigger('change.select2');
                refreshShifts();
            }

            function refreshShifts() {
                const schedules = matchingSchedules();
                const taskId = $('#task_id').val();
                const selectedShift = $('#shift_id').val() || initialShiftId;
                const shifts = schedules.filter((schedule) => schedule.task_id === taskId);

                resetSelect('#shift_id', shifts.length ? '-- Pilih Shift --' : '-- Tidak ada shift --');

                shifts.forEach((schedule) => {
                    $('#shift_id').append(new Option(schedule.shift_name, schedule.shift_id));
                });

                if (shifts.some((schedule) => schedule.shift_id === selectedShift)) {
                    $('#shift_id').val(selectedShift);
                } else if (shifts.length === 1) {
                    $('#shift_id').val(shifts[0].shift_id);
                } else {
                    $('#shift_id').val('');
                }

                $('#shift_id').trigger('change.select2');
            }

            $('#employee_id, #date').on('change', refreshTasks);
            $('#task_id').on('change', refreshShifts);
            refreshTasks();
        });
    </script>
@endsection
