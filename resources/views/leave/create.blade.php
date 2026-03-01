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

        @if (in_array($userRole, ['hr', 'owner']))
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
            <input type="date" name="start_date" class="form-control" min="{{ date('Y-m-d') }}" required>
        </div>

        <div class="col-md-6">
            <label class="form-label">Tanggal Selesai</label>
            <input type="date" name="end_date" class="form-control" min="{{ date('Y-m-d') }}" required>
        </div>

        <div class="col-md-6">
            <label class="form-label">Jenis Cuti</label>
            <select name="leave_id" class="form-select" required>
                <option value="">Choose...</option>
                @foreach ($types as $type)
                    <option value="{{ $type->id }}" data-requires="{{ $type->document }}"
                        data-max="{{ $type->max_days }}" data-limit="{{ $type->limit_days }}"
                        data-period="{{ $type->limit_type }}">
                        {{ $type->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-6">
            <label class="form-label">Upload Surat Bukti</label>
            <input type="file" name="document_file" class="form-control" accept="image/*, application/pdf">
        </div>

        {{-- INFO CUTI --}}
        <div class="col-md-12">
            <div id="leaveInfo" class="alert alert-info d-none"></div>
        </div>

        {{-- INFO JUMLAH HARI --}}
        <div class="col-md-12">
            <div id="dayInfo" class="alert alert-warning d-none"></div>
        </div>

        <div class="col-12">
            <button type="submit" class="btn btn-primary">
                Submit
            </button>
        </div>
    </form>

    <script>
        document.addEventListener("DOMContentLoaded", function() {

            const leaveSelect = document.querySelector("select[name='leave_id']");
            const leaveInfo = document.getElementById("leaveInfo");
            const dayInfo = document.getElementById("dayInfo");

            const startInput = document.querySelector("input[name='start_date']");
            const endInput = document.querySelector("input[name='end_date']");
            const form = document.getElementById("leaveForm");

            // =========================
            // Update End Date Minimum
            // =========================
            startInput.addEventListener("change", function() {
                endInput.min = this.value;
                calculateDays();
            });

            endInput.addEventListener("change", calculateDays);

            // =========================
            // Hitung Jumlah Hari
            // =========================
            function calculateDays() {

                if (!startInput.value || !endInput.value) {
                    dayInfo.classList.add("d-none");
                    return;
                }

                const start = new Date(startInput.value);
                const end = new Date(endInput.value);

                const diffTime = end - start;
                const diffDays = (diffTime / (1000 * 60 * 60 * 24)) + 1;

                if (diffDays <= 0) {
                    dayInfo.classList.add("d-none");
                    return;
                }

                const selected = leaveSelect.options[leaveSelect.selectedIndex];
                const max = selected.dataset.max;

                let message = `Anda mengajukan ${diffDays} hari cuti.`;

                if (max && diffDays > max) {
                    message += ` (Melebihi maksimal ${max} hari per pengajuan!)`;
                    dayInfo.classList.remove("alert-warning");
                    dayInfo.classList.add("alert-danger");
                } else {
                    dayInfo.classList.remove("alert-danger");
                    dayInfo.classList.add("alert-warning");
                }

                dayInfo.innerHTML = message;
                dayInfo.classList.remove("d-none");
            }

            // =========================
            // Info Kuota Saat Pilih Jenis
            // =========================
            leaveSelect.addEventListener("change", function() {

                const selected = this.options[this.selectedIndex];

                if (!selected.value) {
                    leaveInfo.classList.add("d-none");
                    return;
                }

                const max = selected.dataset.max;
                const limit = selected.dataset.limit;
                const period = selected.dataset.period;

                let periodText = '';

                if (period === 'yearly') periodText = 'per tahun';
                if (period === 'monthly') periodText = 'per bulan';

                leaveInfo.innerHTML = `
            <strong>Informasi Cuti:</strong><br>
            Kuota: ${limit ? limit + ' hari ' + periodText : 'Tidak dibatasi'}<br>
            Maksimal sekali pengajuan: ${max ? max + ' hari' : 'Tidak dibatasi'}
        `;

                leaveInfo.classList.remove("d-none");

                calculateDays();
            });

            // =========================
            // Validasi Dokumen
            // =========================
            form.addEventListener("submit", function(e) {

                const selected = leaveSelect.options[leaveSelect.selectedIndex];

                if (!selected.value) return;

                const requiresDocument = selected.dataset.requires === "1";
                const documentFile = document.querySelector("input[name='document_file']");

                if (requiresDocument && documentFile.files.length === 0) {

                    e.preventDefault();

                    Swal.fire({
                        icon: 'warning',
                        title: 'Dokumen Wajib Upload',
                        text: 'Jenis cuti ini mewajibkan upload surat bukti!'
                    });
                }
            });

        });
    </script>

@endsection
