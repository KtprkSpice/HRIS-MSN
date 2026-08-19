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

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="mb-4 p-4 rounded-4">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <h6 class="fw-bold mb-0">
                <i class="fa-solid fa-circle-info me-2 text-primary"></i>
                Informasi Pengajuan Cuti
            </h6>

            <span id="badgeWarning" class="badge bg-danger d-none">
                Melebihi Kuota!
            </span>
        </div>

        <div class="alert alert-warning rounded-3 py-2 px-3 mb-3">
            <i class="fa-solid fa-triangle-exclamation me-2"></i>
            Pengajuan cuti harus sesuai dengan kebijakan perusahaan dan sisa cuti yang tersedia.
        </div>

        <div class="row text-center d-none">
            <div class="col-md-4 mb-2">
                <div class="p-2 border rounded-3">
                    <small class="text-muted">Kuota Jenis Cuti</small>
                    <h6 class="mb-0 fw-bold text-primary" id="kuotaCuti">-</h6>
                </div>
            </div>
            <div class="col-md-4 mb-2">
                <div class="p-2 border rounded-3">
                    <small class="text-muted">Digunakan</small>
                    <h6 class="mb-0 fw-bold text-danger" id="cutiTerpakai">0</h6>
                </div>
            </div>
            <div class="col-md-4 mb-2">
                <div class="p-2 border rounded-3">
                    <small class="text-muted">Sisa Cuti</small>
                    <h6 class="mb-0 fw-bold text-success" id="sisaCuti">-</h6>
                </div>
            </div>
        </div>

        <div class="mt-3">
            <small class="text-muted">Estimasi pengajuan:</small>
            <span id="estimasiHari" class="fw-bold text-dark">0 hari</span>
        </div>
    </div>

    <form class="row g-3" action="{{ route('leave-request.update', $leaveRequest->id) }}" method="post"
        enctype="multipart/form-data" id="leaveForm">
        @method('PUT')
        @csrf
        <div class="col-md-6">
            <label for="employee_id" class="form-label">Nama Karyawan</label>
            <select name="employee_id" id="employee_id" required
                class="form-select @error('employee_id')
                is-invalid
            @enderror">
                <option value="">Choose...</option>
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
            <label for="leave_id" class="form-label">Jenis Cuti</label>
            <select name="leave_id" id="leave_id" required
                class="form-select @error('leave_id')
                is-invalid
            @enderror">
                <option value="">Choose...</option>
                @foreach ($types as $type)
                    <option value="{{ $type->id }}" data-requires="{{ $type->document }}"
                        {{ $type->id == $leaveRequest->leave_id ? 'selected' : '' }}>
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
                    <a href="{{ asset($leaveRequest->document_file) }}" target="_blank">
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
        const leaveStats = @json($leaveStats);

        function selectedPeriodDate() {
            const startInput = document.querySelector("input[name='start_date']");
            return startInput.value ? new Date(startInput.value) : new Date();
        }

        function usedLeaveDays(employeeId, leaveId, leaveType, periodDate) {
            if (!employeeId || !leaveId || !leaveType) return 0;

            return leaveStats.approved_leaves
                .filter(leave => leave.employee_id === String(employeeId) && leave.leave_id === String(leaveId))
                .filter(leave => {
                    if (leaveType.limit_type === 'yearly') {
                        return leave.year === periodDate.getFullYear();
                    }

                    if (leaveType.limit_type === 'monthly') {
                        return leave.year === periodDate.getFullYear() && leave.month === periodDate.getMonth() + 1;
                    }

                    return true;
                })
                .reduce((total, leave) => total + Number(leave.days), 0);
        }

        let remainingLeaveDays = Number.POSITIVE_INFINITY;

        function hitungHari() {
            const startInput = document.querySelector("input[name='start_date']");
            const endInput = document.querySelector("input[name='end_date']");
            const start = new Date(startInput.value);
            const end = new Date(endInput.value);

            if (!startInput.value || !endInput.value) return 0;

            const diffTime = end - start;
            const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24)) + 1;

            return diffDays > 0 ? diffDays : 0;
        }

        function updateRequestEstimate() {
            const badge = document.getElementById("badgeWarning");
            const estimate = document.getElementById("estimasiHari");
            const totalHari = hitungHari();

            estimate.innerText = `${totalHari} hari`;

            if (Number.isFinite(remainingLeaveDays) && totalHari > remainingLeaveDays) {
                badge.classList.remove("d-none");
                estimate.classList.remove("text-dark");
                estimate.classList.add("text-danger");
            } else {
                badge.classList.add("d-none");
                estimate.classList.remove("text-danger");
                estimate.classList.add("text-dark");
            }
        }

        function updateLeaveStatistics() {
            const employeeId = document.querySelector("select[name='employee_id']").value;
            const leaveId = document.querySelector("select[name='leave_id']").value;
            const leaveType = leaveStats.types[String(leaveId)];

            if (!leaveType) {
                remainingLeaveDays = Number.POSITIVE_INFINITY;
                document.getElementById("kuotaCuti").innerText = "-";
                document.getElementById("cutiTerpakai").innerText = "0";
                document.getElementById("sisaCuti").innerText = "-";
                updateRequestEstimate();
                return;
            }

            const usedDays = usedLeaveDays(employeeId, leaveId, leaveType, selectedPeriodDate());
            const limitDays = leaveType && leaveType.limit_days ? Number(leaveType.limit_days) : null;

            document.getElementById("cutiTerpakai").innerText = usedDays;

            if (limitDays) {
                remainingLeaveDays = Math.max(limitDays - usedDays, 0);
                document.getElementById("kuotaCuti").innerText = limitDays;
                document.getElementById("sisaCuti").innerText = remainingLeaveDays;
            } else {
                remainingLeaveDays = Number.POSITIVE_INFINITY;
                document.getElementById("kuotaCuti").innerText = "Tidak dibatasi";
                document.getElementById("sisaCuti").innerText = "Tidak dibatasi";
            }

            updateRequestEstimate();
        }

        document.querySelector("select[name='employee_id']").addEventListener("change", updateLeaveStatistics);
        document.querySelector("select[name='leave_id']").addEventListener("change", updateLeaveStatistics);
        document.querySelector("input[name='start_date']").addEventListener("change", updateLeaveStatistics);
        document.querySelector("input[name='end_date']").addEventListener("change", updateRequestEstimate);

        updateLeaveStatistics();

        document.getElementById("leaveForm").addEventListener("submit", function(e) {

            const leaveSelect = document.querySelector("select[name='leave_id']");
            const selectedOption = leaveSelect.options[leaveSelect.selectedIndex];

            if (!selectedOption.value) {
                return;
            }

            const requiresDocument = selectedOption.dataset.requires === "1";
            const document_file = document.querySelector("input[name='document_file']");
            const hasExistingDocument = @json((bool) $leaveRequest->document_file);

            if (requiresDocument && !hasExistingDocument && document_file.files.length === 0) {
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
