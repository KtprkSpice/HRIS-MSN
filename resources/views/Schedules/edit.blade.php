@extends('layout.dashboard')
@section('header', 'Edit Jadwal Manual')

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

    <form class="row g-3" action="{{ route('schedule.update', $schedule->id) }}" method="post">
        @csrf
        @method('PUT')
        <div class="col-md-6">
            <label for="name" class="form-label">Nama Karyawan</label>
            <select name="employee_id"
                class="form-select @error('employee_id')
                'is-invalid'
            @enderror">
                @foreach ($employees as $employee)
                    <option value="{{ $employee->id }}"
                        {{ old('employee_id', $schedule->employee_id) == $employee->id ? 'selected' : '' }}>
                        {{ ucwords($employee->fullname) }}</option>
                @endforeach
            </select>
            @error('employee_id')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <div class="col-md-6">
            <label for="name" class="form-label">Jadwal Shift</label>
            <select name="shift_id"
                class="form-select @error('shift_id')
                'is-invalid'
            @enderror">
                @foreach ($shifts as $shift)
                    <option value="{{ $shift->id }}"
                        {{ old('shift_id', $schedule->shift_id) == $shift->id ? 'selected' : '' }}>
                        {{ ucwords($shift->name) }}</option>
                @endforeach
            </select>
            @error('shift_id')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <div class="col-md-6">
            <label for="name" class="form-label">Nama tugas</label>
            <select name="task_id" class="form-select @error('task_id')
                'is-invalid'
            @enderror">
                @foreach ($tasks as $task)
                    <option value="{{ $task->id }}" {{ old('task_id', $schedule->task_id) == $task->id }}>
                        {{ ucwords($task->name) }}</option>
                @endforeach
            </select>
            @error('task_id')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <div class="col-md-6">
            <label for="date" class="form-label">Tanggal</label>
            <input type="date" class="form-control @error('date') is-invalid @enderror" name="date" id="date"
                value="{{ old('date', $schedule->date) }}">
            @error('date')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <div class="col-12">
            <button type="submit" class="btn btn-primary">
                Simpan
            </button>
        </div>
    </form>

    <script>
        function toggleDeduction() {
            const isPaid = document.querySelector('input[name="is_paid"]:checked').value;
            const wrapper = document.getElementById('deduction-wrapper');
            const input = document.getElementById('deduction');

            if (isPaid == 1) {
                wrapper.style.display = 'none';
                input.value = 0;
            } else {
                wrapper.style.display = 'block';
            }
        }

        document.querySelectorAll('input[name="is_paid"]').forEach(el => {
            el.addEventListener('change', toggleDeduction);
        });

        toggleDeduction();
    </script>
@endsection
