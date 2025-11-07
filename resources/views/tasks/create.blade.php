@extends('layout.dashboard')
@section('header', 'Tambah Tugas')

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

    <form class="row g-3" action="{{ route('employee.store') }}" method="post">
        @csrf
        <div class="col-md-6">
            <label for="fullname" class="form-label">Nama Lengkap</label>
            <input type="text" class="form-control @error('fullname')
                is-invalid
            @enderror"
                id="fullname" name="fullname" required value="{{ old('fullname') }}">
            @error('fullname')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
        <div class="col-md-6">
            <label for="nik"
                class="form-label @error('nik')
                is-invalid
            @enderror">NIK</label>
            <input type="number" min="0" class="form-control" id="nik" placeholder="123456" required
                value="{{ old('nik') }}" name="nik">
            @error('nik')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
        <div class="col-md-6">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control @error('email')
                is-invalid
            @enderror"
                id="email" name="email" required value="{{ old('email') }}">
            @error('email')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
        <div class="col-md-6">
            <label for="phone" class="form-label">No Telepon</label>
            <div class="input-group">
                <div class="input-group-text">+62</div>
                <input type="number"
                    class="form-control @error('phone')
                    is-invalid
                @enderror"
                    min="0" id="phone" placeholder="8911235516" value="{{ old('phone') }}" name="phone"
                    required>
                @error('phone')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="col-md-6">
            <label for="no_bpjs_kesehatan" class="form-label">No BPJS Kesehatan</label>
            <input type="number"
                class="form-control @error('bpjs_kesehatan')
                is-invalid
            @enderror"
                id="no_bpjs_kesehatan" placeholder="123456" min="0" name="bpjs_kesehatan" required
                value="{{ old('bpjs_kesehatan') }}">
            @error('bpjs_kesehatan')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
        <div class="col-md-6">
            <label for="no_bpjs_ketenagakerjaan" class="form-label">No BPJS Ketenagakerjaan</label>
            <input type="number"
                class="form-control @error('bpjs_ketenagakerjaan')
                is-invalid
            @enderror"
                min="0" id="no_bpjs_ketenagakerjaan" placeholder="123456" name="bpjs_ketenagakerjaan" required
                value="{{ old('bpjs_ketenagakerjaan') }}">
            @error('bpjs_ketenagakerjaan')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
        <div class="col-md-6">
            <label for="birh_date" class="form-label">Tanggal Lahir</label>
            <input type="date" class="form-control @error('born_date')
                is-invalid
            @enderror"
                id="birh_date" name="born_date" required value="{{ old('born_date') }}">
            @error('born_date')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
        <div class="col-md-6">
            <label for="hire_date" class="form-label">Tanggal Kerja</label>
            <input type="date" class="form-control @error('hire_date')
                is-invalid
            @enderror"
                id="hire_date" name="hire_date" required value="{{ old('hire_date') }}">
            @error('hire_date')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
        <div class="col-md-6">
            <label for="npwp" class="form-label">NPWP</label>
            <input type="text" min="0" pattern="[0-9\.\-]+"
                class="form-control @error('npwp')
                is-invalid
            @enderror" id="npwp"
                placeholder="12356.63127-1.123" name="npwp" required value="{{ old('npwp') }}">
            @error('npwp')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
        <div class="col-12">
            <label for="address" class="form-label">Alamat</label>
            <input type="text"
                class="form-control @error('address')
                is-invalid
            @enderror" id="address"
                placeholder="Jl.Kenari...." name="address" required value="{{ old('address') }}">
            @error('npwp')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </form>
@endsection
