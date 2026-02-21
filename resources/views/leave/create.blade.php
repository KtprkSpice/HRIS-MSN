@extends('layout.dashboard')
@section('header', 'Tambah Cuti')

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

    <form class="row g-3" action="{{ route('leave-request.store') }}" method="POST" enctype="multipart/form-data"
        id="leaveForm">

        @csrf

        @if (in_array($userRole, ['owner']))
            <div class="col-md-6">
                <label class="form-label">Nama Karyawan</label>
                <select name="employee_id" class="form-select" required>
                    <option value="">Choose...</option>
                    @foreach ($employees as $employee)
                        <option value="{{ $employee->id }}">
                            {{ ucwords($employee->fullname) }}
                        </option>
                    @endforeach
                </select>
            </div>
        @endif

        <div class="col-md-6">
            <label class="form-label">Tanggal Mulai</label>
            <input type="date" name="start_date" class="form-control" required>
        </div>

        <div class="col-md-6">
            <label class="form-label">Tanggal Selesai</label>
            <input type="date" name="end_date" class="form-control" required>
        </div>

        <div class="col-md-6">
            <label class="form-label">Jenis Cuti</label>
            <select name="leave_id" class="form-select" required>
                <option value="">Choose...</option>
                @foreach ($types as $type)
                    <option value="{{ $type->id }}" data-requires="{{ $type->document }}">
                        {{ $type->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-6">
            <label class="form-label">Upload Surat Bukti</label>
            <input type="file" name="document_file" class="form-control" accept="image/*, application/pdf">
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
