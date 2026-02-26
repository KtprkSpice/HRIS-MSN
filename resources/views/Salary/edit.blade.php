@extends('layout.dashboard')
@section('header', 'Edit Gaji Manual')

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

    <form class="row g-3" action="{{ route('salary.update', $salary->id) }}" method="post">
        @method('PUT')
        @csrf
        <div class="col-md-6">
            <label for="name" class="form-label">Nama Karyawan</label>
            <select name="employee_id"
                class="form-select @error('employee_id')
                is-invalid
            @enderror">
                <option>Choose...</option>
                @foreach ($employees as $employee)
                    <option value="{{ $employee->id }}"
                        {{ old('employee_id', $salary->employee_id == $employee->id ? 'selected' : '') }}>
                        {{ ucwords($employee->fullname) }}</option>
                @endforeach
                @error('employee_id')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </select>
        </div>
        <div class="col-md-6">
            <label for="net_salary" class="form-label">Gaji</label>
            <input type="input" id="salary"
                class="form-control @error('net_salary')
                is-invalid
            @enderror" id="net_salary"
                name="net_salary" required value="{{ old('net_salary', number_format($salary->net_salary, 0, ',', '.')) }}">
            @error('net_salary')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
        <div class="col-md-6">
            <label for="cuts" class="form-label">Potongan</label>
            <input type="input" id="salary"
                class="form-control @error('cuts')
                is-invalid
            @enderror" id="cuts"
                name="cuts" required value="{{ old('cuts', number_format($salary->cuts, 0, ',', '.')) }}">
            @error('cuts')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
        <div class="col-md-6">
            <label for="bonus" class="form-label">Bonus</label>
            <input type="input" id="salary"
                class="form-control @error('bonus')
                is-invalid
            @enderror" id="bonus"
                name="bonus" required value="{{ old('bonus', number_format($salary->bonus, 0, ',', '.')) }}">
            @error('bonus')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
        <div class="col-md-6">
            <label for="date" class="form-label">Tanggal Gaji</label>
            <input type="date" class="form-control @error('date')
                is-invalid
            @enderror"
                id="date" name="date" required value="{{ old('date', $salary->date) }}">
            @error('date')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-danger">Submit</button>
        </div>
    </form>
@endsection
