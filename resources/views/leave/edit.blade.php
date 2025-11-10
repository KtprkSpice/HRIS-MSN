@extends('layout.dashboard')
@section('header', 'Edit Cuti')

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

    <form class="row g-3" action="{{ route('leave-request.update', $leaveRequest->id) }}" method="post">
        @method('PUT')
        @csrf
        <div class="col-md-6">
            <label for="employee_id" class="form-label">Nama Karyawan</label>
            <select name="employee_id" id="employee_id"
                class="form-select @error('employee_id')
                is-invalid
            @enderror">
                <option>Choose...</option>
                @foreach ($employees as $employee)
                    <option value="{{ $employee->id }}" {{ $employee->id == $leaveRequest->employee_id ? 'selected' : '' }}>
                        {{ ucwords($employee->fullname) }}</option>
                @endforeach
                @error('employee_id')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </select>
        </div>
        <div class="col-md-6">
            <label for="start_date" class="form-label">Tanggal Mulai</label>
            <input type="date" class="form-control @error('start_date')
                is-invalid
            @enderror"
                id="start_date" name="start_date" required value="{{ old('start_date', $leaveRequest->start_date) }}">
            @error('start_date')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
        <div class="col-md-6">
            <label for="end_date" class="form-label">Tanggal Selesai</label>
            <input type="date" class="form-control @error('end_date')
                is-invalid
            @enderror"
                id="end_date" name="end_date" required value="{{ old('end_date', $leaveRequest->end_date) }}">
            @error('end_date')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
        <div class="col-md-6">
            <label for="employee_id" class="form-label">Jenis Cuti</label>
            <select name="leave_type" id="employee_id"
                class="form-select @error('leave_type')
                is-invalid
            @enderror">
                <option>Choose...</option>
                @foreach (['sick', 'vacation'] as $type)
                    <option value="{{ $type }}" {{ $leaveRequest->leave_type == $type ? 'selected' : '' }}>
                        {{ ucwords($type) }}</option>
                @endforeach
                @error('leave_type')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </select>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </form>
@endsection
