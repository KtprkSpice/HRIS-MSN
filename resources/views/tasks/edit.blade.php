@extends('layout.dashboard')
@section('header', 'Edit Tugas')

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

    <form class="row g-3" action="{{ route('task.update', $task->id) }}" method="post">
        @method('PUT')
        @csrf
        <div class="col-md-6">
            <label for="name" class="form-label">Nama Tugas</label>
            <input type="text" class="form-control @error('name')
                is-invalid
            @enderror"
                id="name" name="name" required value="{{ old('name', $task->name) }}">
            @error('name')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
        <div class="col-md-6">
            <label for="start_time" class="form-label">Tanggal Mulai</label>
            <input type="date" class="form-control @error('start_time')
                is-invalid
            @enderror"
                id="start_time" name="start_time" required value="{{ old('start_time', $task->start_time) }}">
            @error('start_time')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
        <div class="col-md-6">
            <label for="end_time" class="form-label">Tanggal Selesai</label>
            <input type="date" class="form-control @error('end_time')
                is-invalid
            @enderror"
                id="end_time" name="end_time" required value="{{ old('end_time', $task->end_time) }}">
            @error('end_time')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
        <div class="col-12">
            <label for="description" class="form-label">Deskripsi</label>
            <input type="text"
                class="form-control @error('description')
                is-invalid
            @enderror" id="description"
                placeholder="" name="description" required value="{{ old('description', $task->description) }}">
            @error('description')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
        <div class="table-responsive">
            <table id="tugasTable" class="table table-bordered">
                <thead>
                    <th>
                        <input type="checkbox" name="" id="selectAll">
                    </th>
                    <th>Nama Karyawan</th>
                    <th>Divisi</th>
                    <th>Posisi</th>
                </thead>
                <tbody>
                    @foreach ($employees as $employee)
                        <tr>
                            <td>
                                <input type="checkbox" name="selected_employee[]" value="{{ $employee->id }}"
                                    {{ in_array($employee->id, $task->employees->pluck('id')->toArray()) ? 'checked' : '' }}>
                            </td>
                            <td>{{ ucwords($employee->fullname) }}</td>
                            <td>{{ ucwords($employee->division->name) }}</td>
                            <td>Posisi</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </form>


    <script>
        document.getElementById('selectAll').addEventListener('click', function() {
            const checkboxes = document.querySelectorAll('input[name="selected_employee[]"]');
            checkboxes.forEach(cb => cb.checked = this.checked);
        });
    </script>

@endsection
