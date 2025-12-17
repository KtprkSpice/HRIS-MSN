@extends('layout.dashboard')
@section('header', 'Tambah Jenis Cuti')

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

    <form class="row g-3" action="{{ route('leave-type.store') }}" method="post">
        @csrf

        <div class="col-md-6">
            <label for="name" class="form-label">Nama Cuti</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                value="{{ old('name') }}" required>

            @error('name')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <div class="col-md-6">
            <label class="form-label d-block">Dibayar</label>

            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="is_paid" id="paid_yes" value="1"
                    {{ old('is_paid', '1') == '1' ? 'checked' : '' }}>
                <label class="form-check-label" for="paid_yes">Ya</label>
            </div>

            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="is_paid" id="paid_no" value="0"
                    {{ old('is_paid') == '0' ? 'checked' : '' }}>
                <label class="form-check-label" for="paid_no">Tidak</label>
            </div>

            @error('is_paid')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-6" id="deduction-wrapper">
            <label for="deduction" class="form-label">Potongan per Hari</label>
            <input type="number" class="form-control @error('deduction') is-invalid @enderror" name="deduction"
                id="deduction" value="{{ old('deduction', 0) }}" min="0">

            @error('deduction')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <div class="col-md-6">
            <label for="max_days" class="form-label">Maksimal Hari</label>
            <input type="number" class="form-control @error('max_days') is-invalid @enderror" name="max_days"
                id="max_days" value="{{ old('max_days') }}" min="1">

            @error('max_days')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <div class="col-md-6">
            <label for="description" class="form-label">Deskripsi</label>
            <input type="text" class="form-control @error('description') is-invalid @enderror" name="description"
                id="description" value="{{ old('description') }}" min="1">

            @error('description')
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
