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

    <form class="row g-3" action="{{ route('leave-request.update', $leaveRequest->id) }}" method="post"
        enctype="multipart/form-data">
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
            <select name="leave_id" id="employee_id"
                class="form-select @error('leave_id')
                is-invalid
            @enderror">
                <option>Choose...</option>
                @foreach ($types as $type)
                    <option value="{{ $type->id }}" {{ $type->id == $leaveRequest->leave_id ? 'selected' : '' }}>
                        {{ ucwords($type->name) }}</option>
                @endforeach
                @error('leave_id')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </select>
        </div>

        <div class="col-md-6">
            <label class="form-label">Upload Surat Bukti</label>

            <input type="file" name="document_file" class="form-control" accept="image/*, application/pdf">

            @if ($leaveRequest->document_file)
                <small class="text-muted d-block mt-2">
                    File saat ini:
                    <a href="{{ asset('storage/' . $leaveRequest->document_file) }}" target="_blank">
                        Lihat Dokumen
                    </a>
                </small>
            @endif
        </div>

        <div class="col-12">
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </form>

    <script>
        document.getElementById("leaveForm").addEventListener("submit", function(e) {

            const leaveSelect = document.querySelector("select[name='leave_id']");
            const selectedOption = leaveSelect.options[leaveSelect.selectedIndex];

            if (!selectedOption.value) {
                return;
            }

            const requiresDocument = selectedOption.dataset.requires === "1";
            const document_file = document.querySelector("input[name='document_file']");

            if (requiresDocument && document_file.files.length === 0) {
                e.preventDefault();

                Swal.fire({
                    icon: 'warning',
                    title: 'Dokumen Wajib Upload',
                    text: 'Jenis cuti ini mewajibkan upload surat bukti!'
                });
            }
        });
    </script>
@endsection
