@extends('layout.dashboard')
@section('header', 'Edit Data Kehadiran')

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

    <form class="row g-3" action="{{ route('presence.update', $presence->id) }}" method="post">
        @csrf
        @method('PUT')
        <div class="col-md-6">
            <label for="fullname" class="form-label">Nama Karyawan</label>
            <select name="employee_id"
                class="form-select @error('employee_id')
                is-invalid
            @enderror">
                @foreach ($employees as $employee)
                    <option value="{{ $employee->id }}" {{ $employee->id == $presence->employee_id ? 'selected' : '' }}>{{ ucwords($employee->fullname) }}</option>
                @endforeach
                @error('employee_id')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </select>
        </div>
        <div class="col-md-6">
            <label for="date" class="form-label">Tanggal</label>
            <input type="date" class="form-control @error('date')
                is-invalid
            @enderror"
                id="date" name="date" required value="{{ old('date', $presence->date) }}">
            @error('date')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
        <div class="col-md-6">
            <label for="check_in" class="form-label">Jam Masuk</label>
            <input type="datetime-local"
                class="form-control @error('check_in')
                is-invalid
            @enderror" id="check_in"
                name="check_in" required value="{{ old('check_in', $presence->check_in) }}">
            @error('check_in')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
        <div class="col-md-6">
            <label for="check_out" class="form-label">Jam Keluar</label>
            <input type="datetime-local"
                class="form-control @error('check_out')
                is-invalid
            @enderror" id="check_out"
                name="check_out" required value="{{ old('check_out', $presence->check_out) }}">
            @error('check_out')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </form>
@endsection
