@extends('layout.dashboard')
@section('header', 'Tambah Gaji Manual')

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
        <div class="col-md-6">
            <label for="name" class="form-label">Nama Karyawan</label>
            <select name="employee_id" class="form-select @error('employee_id')
                is-invalid
            @enderror">
                <option>Choose...</option>
                @foreach ($employees as $employee)
                    <option value="{{ $employee->id }}">{{ ucwords($employee->fullname) }}</option>
                @endforeach
                @error('employee_id')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </select>
        </div>
        <div class="col-md-6">
            <label for="net_salary" class="form-label">Gaji</label>
            <input type="input" class="form-control @error('net_salary')
                is-invalid
            @enderror"
                id="net_salary" name="net_salary" required value="{{ old('net_salary') }}">
            @error('start_time')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
        <div class="col-md-6">
            <label for="end_time" class="form-label">Tanggal Selesai</label>
            <input type="date" class="form-control @error('end_time')
                is-invalid
            @enderror"
                id="end_time" name="end_time" required value="{{ old('end_time') }}">
            @error('end_time')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
        <div class="col-12">
            <label for="description" class="form-label">Deskripsi</label>
            <input type="textarea"
                class="form-control @error('description')
                is-invalid
            @enderror" id="description"
                placeholder="" name="description" required value="{{ old('description') }}">
            @error('description')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </form>
@endsection
