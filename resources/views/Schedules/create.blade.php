@extends('layout.dashboard')
@section('header', 'Tambah Jadwal Manual')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Styling agar Select2 menyatu dengan tema modern */
    .select2-container--default .select2-selection--single {
        height: 45px !important;
        padding: 8px !important;
        border: 1px solid #dee2e6 !important;
        border-radius: 8px !important;
        box-shadow: 0 .125rem .25rem rgba(0,0,0,.075)!important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 42px !important;
    }
    .select2-dropdown {
        border: 1px solid #bc5e6b !important;
        border-radius: 8px !important;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
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
    <div class="col-md-8">
        <div class="card shadow border-0">
            <div class="card-header text-white" style="background: linear-gradient(90deg, #bc5e6b 0%, #a34a57 100%); border: none; padding: 1.2rem;">
                <h5 class="mb-0 fw-bold"><i class="fa-solid fa-plus-circle me-2"></i> Form Tambah Jadwal</h5>
            </div>
            
            <div class="card-body p-4">
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0 small">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form action="{{ route('schedule.store') }}" method="post">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="employee_id" class="form-label fw-bold text-secondary">
                            <i class="fa-solid fa-user me-2" style="color: #bc5e6b;"></i> Nama Karyawan
                        </label>
                        <select name="employee_id" id="employee_id" class="form-control select2-js @error('employee_id') is-invalid @enderror">
                            <option value="">-- Cari Nama Karyawan --</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                    {{ ucwords($employee->fullname) }}
                                </option>
                            @endforeach
                        </select>
                        @error('employee_id')
                            <span class="invalid-feedback d-block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="shift_id" class="form-label fw-bold text-secondary">
                            <i class="fa-solid fa-clock me-2" style="color: #bc5e6b;"></i> Jadwal Shift
                        </label>
                        <select name="shift_id" id="shift_id" class="form-control select2-js @error('shift_id') is-invalid @enderror">
                            <option value="">-- Pilih Shift --</option>
                            @foreach ($shifts as $shift)
                                <option value="{{ $shift->id }}" data-task-id="{{ $shift->task_id }}" {{ old('shift_id') == $shift->id ? 'selected' : '' }}>
                                    {{ ucwords($shift->name) }} - {{ ucwords($shift->task->name ?? '-') }}
                                </option>
                            @endforeach
                        </select>
                        @error('shift_id')
                            <span class="invalid-feedback d-block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="task_id" class="form-label fw-bold text-secondary">
                            <i class="fa-solid fa-list-check me-2" style="color: #bc5e6b;"></i> Nama Tugas
                        </label>
                        <select name="task_id" id="task_id" class="form-control select2-js @error('task_id') is-invalid @enderror">
                            <option value="">-- Cari Tugas --</option>
                            @foreach ($tasks as $task)
                                <option value="{{ $task->id }}" {{ old('task_id') == $task->id ? 'selected' : '' }}>
                                    {{ ucwords($task->name) }}
                                </option>
                            @endforeach
                        </select>
                        @error('task_id')
                            <span class="invalid-feedback d-block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="date" class="form-label fw-bold text-secondary">
                            <i class="fa-solid fa-calendar-plus me-2" style="color: #bc5e6b;"></i> Tanggal Pelaksanaan
                        </label>
                        <input type="date" class="form-control shadow-sm @error('date') is-invalid @enderror" 
                            name="date" id="date" value="{{ old('date') }}" style="height: 45px; border-radius: 8px;">
                        @error('date')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <hr class="my-4 opacity-50">

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-lg text-white shadow" style="background: linear-gradient(90deg, #bc5e6b 0%, #a34a57 100%); border: none; border-radius: 10px;">
                            <i class="fa-solid fa-save me-2"></i> Simpan Jadwal Baru
                        </button>
                        <a href="{{ route('schedule.index') }}" class="btn btn-lg btn-back shadow-sm">
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
        // Inisialisasi Select2
        $('.select2-js').select2({
            theme: "default",
            width: '100%'
        });

        // Tetap menyertakan fungsi toggleDeduction jika sewaktu-waktu field is_paid ditambahkan kembali
        function toggleDeduction() {
            const isPaidEl = document.querySelector('input[name="is_paid"]:checked');
            const wrapper = document.getElementById('deduction-wrapper');
            const input = document.getElementById('deduction');

            if (isPaidEl && wrapper && input) {
                if (isPaidEl.value == 1) {
                    wrapper.style.display = 'none';
                    input.value = 0;
                } else {
                    wrapper.style.display = 'block';
                }
            }
        }

        document.querySelectorAll('input[name="is_paid"]').forEach(el => {
            el.addEventListener('change', toggleDeduction);
        });

        $('#task_id').on('change', function() {
            const taskId = $(this).val();
            $('#shift_id option').each(function() {
                const optionTaskId = $(this).data('task-id')?.toString();
                $(this).prop('disabled', taskId && optionTaskId && optionTaskId !== taskId);
            });

            if ($('#shift_id option:selected').prop('disabled')) {
                $('#shift_id').val('').trigger('change');
            }

            $('#shift_id').trigger('change.select2');
        }).trigger('change');
    });
</script>
@endsection
