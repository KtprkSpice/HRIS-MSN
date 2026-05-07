@extends('layout.dashboard')
@section('header', 'Tambah Cuti')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<style>
    /* Card Container - Modern & Solid */
    .form-container-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        padding: 2.5rem;
        margin-bottom: 2rem;
    }

    /* Label Styling with Icons */
    .form-label {
        font-weight: 600;
        font-size: 0.85rem;
        color: #374151;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
    }

    .form-label i {
        margin-right: 8px;
        color: #bc5e6b;
        width: 18px;
        text-align: center;
    }

    /* Custom Styling untuk Select2 agar serasi dengan Bootstrap 5 */
    .select2-container--default .select2-selection--single {
        border-radius: 10px !important;
        height: 45px !important;
        border: 1px solid #d1d5db !important;
        display: flex;
        align-items: center;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        padding-left: 14px !important;
        color: #1f2937 !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 43px !important;
    }

    .select2-dropdown {
        border-radius: 10px !important;
        border: 1px solid #e5e7eb !important;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
        overflow: hidden;
    }

    .select2-search__field {
        border-radius: 6px !important;
    }

    /* Input & File Styling */
    .form-control {
        border-radius: 10px;
        padding: 10px 14px;
        border: 1px solid #d1d5db;
        font-size: 0.95rem;
        transition: all 0.2s ease;
    }

    .form-control:focus {
        border-color: #bc5e6b;
        box-shadow: 0 0 0 4px rgba(188, 94, 107, 0.1);
    }

    /* Button Styling */
    .btn-submit {
        background: linear-gradient(135deg, #bc5e6b 0%, #8e444f 100%);
        border: none;
        border-radius: 10px;
        padding: 12px 24px;
        font-weight: 700;
        color: white;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
    }

    .btn-submit:hover {
        transform: translateY(-1px);
        filter: brightness(1.1);
        color: white;
    }

    .btn-back {
        color: #6b7280;
        font-size: 0.85rem;
        text-decoration: none;
    }
    
</style>
<style>
@keyframes pulseWarning {
    0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(220,53,69,0.6); }
    70% { transform: scale(1.05); box-shadow: 0 0 0 10px rgba(220,53,69,0); }
    100% { transform: scale(1); }
}

.badge-warning-animate {
    animation: pulseWarning 1.2s infinite;
}
</style>

<div class="row">
    <div class="col-lg-10 mx-auto">
        <div class="mb-4 p-4 rounded-4 ">
    
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h6 class="fw-bold mb-0">
            <i class="fa-solid fa-circle-info me-2 text-primary"></i>
            Informasi Pengajuan Cuti
        </h6>

        <span id="badgeWarning" class="badge bg-danger d-none">
            Melebihi Kuota!
        </span>
    </div>

    <!-- Note -->
    <div class="alert alert-warning rounded-3 py-2 px-3 mb-3">
        <i class="fa-solid fa-triangle-exclamation me-2"></i>
        Pengajuan cuti harus sesuai dengan kebijakan perusahaan dan sisa cuti yang tersedia.
    </div>

    <!-- Statistik -->
    <div class="row text-center">
        <div class="col-md-4 mb-2">
            <div class="p-2 border rounded-3">
                <small class="text-muted">Kuota Cuti Tahunan</small>
                <h6 class="mb-0 fw-bold text-primary" id="kuotaCuti">12</h6>
            </div>
        </div>
        <div class="col-md-4 mb-2">
            <div class="p-2 border rounded-3">
                <small class="text-muted">Digunakan</small>
                <h6 class="mb-0 fw-bold text-danger" id="cutiTerpakai">4</h6>
            </div>
        </div>
        <div class="col-md-4 mb-2">
            <div class="p-2 border rounded-3">
                <small class="text-muted">Sisa Cuti</small>
                <h6 class="mb-0 fw-bold text-success" id="sisaCuti">8</h6>
            </div>
        </div>
    </div>

    <!-- Estimasi -->
    <div class="mt-3">
        <small class="text-muted">Estimasi pengajuan:</small>
        <span id="estimasiHari" class="fw-bold text-dark">0 hari</span>
    </div>
    </div>

</div>


<div class="row">
    <div class="col-lg-10 mx-auto">
        <div class="form-container-card">
            
            @if ($errors->any())
                <div class="alert alert-danger mb-4 rounded-3 border-0">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li><i class="fa-solid fa-circle-exclamation me-2"></i> {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger mb-4 rounded-3 border-0">
                    <i class="fa-solid fa-circle-exclamation me-2"></i> {{ session('error') }}
                </div>
            @endif

            <form class="row g-4" action="{{ route('leave-request.store') }}" method="POST" enctype="multipart/form-data" id="leaveForm">
                @csrf

                @if (in_array($userRole, ['hr', 'owner']))
                    <div class="col-md-12">
                        <label class="form-label"><i class="fa-solid fa-user-tie"></i> Nama Karyawan</label>
                        <select name="employee_id" class="form-select select-search" required>
                            <option value="">Cari Nama Karyawan...</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}">
                                    {{ ucwords($employee->fullname) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div class="col-md-6">
                    <label class="form-label"><i class="fa-solid fa-calendar-plus"></i> Tanggal Mulai</label>
                    <input type="date" name="start_date" class="form-control" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label"><i class="fa-solid fa-calendar-check"></i> Tanggal Selesai</label>
                    <input type="date" name="end_date" class="form-control" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label"><i class="fa-solid fa-list-check"></i> Jenis Cuti</label>
                    <select name="leave_id" class="form-select select-search" required>
                        <option value="">Cari Jenis Cuti...</option>
                        @foreach ($types as $type)
                            <option value="{{ $type->id }}" data-requires="{{ $type->document }}">
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label"><i class="fa-solid fa-file-arrow-up"></i> Upload Surat Bukti</label>
                    <input type="file" name="document_file" class="form-control" accept="image/*, application/pdf">
                </div>

                <div class="col-12 mt-5 d-flex align-items-center justify-content-between">
                    <a href="{{ url()->previous() }}" class="btn-back">
                        <i class="fa-solid fa-arrow-left me-1"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-submit">
                        Kirim Pengajuan <i class="fa-solid fa-paper-plane ms-2"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        // Inisialisasi Search pada Dropdown
        $('.select-search').select2({
            width: '100%',
            placeholder: "Ketik untuk mencari...",
            allowClear: true
        });

        // Logika Validasi Submit
        document.getElementById("leaveForm").addEventListener("submit", function(e) {
            // Karena menggunakan select2, ambil value lewat jquery agar lebih aman
            const leaveSelect = $("select[name='leave_id']");
            const selectedOption = leaveSelect.find(':selected');

            if (!selectedOption.val()) return;

            const requiresDocument = selectedOption.data('requires') == "1";
            const document_file = document.querySelector("input[name='document_file']");

            if (requiresDocument && document_file.files.length === 0) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Dokumen Wajib Upload',
                    text: 'Jenis cuti ini mewajibkan upload surat bukti sebagai persyaratan!',
                    confirmButtonColor: '#bc5e6b'
                });
            }
        });
    });
</script>
<script>
document.addEventListener("DOMContentLoaded", function () {

    const startInput = document.querySelector("input[name='start_date']");
    const endInput = document.querySelector("input[name='end_date']");
    const badge = document.getElementById("badgeWarning");

    let sisaCuti = parseInt(document.getElementById("sisaCuti").innerText);

    function hitungHari() {
        const start = new Date(startInput.value);
        const end = new Date(endInput.value);

        if (!startInput.value || !endInput.value) return 0;

        const diffTime = end - start;
        const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24)) + 1;

        return diffDays > 0 ? diffDays : 0;
    }

    function updateUI() {
        const totalHari = hitungHari();

        document.getElementById("estimasiHari").innerText = totalHari;

        if (totalHari > sisaCuti) {
            badge.classList.remove("d-none");

            // warna estimasi jadi merah
            document.getElementById("estimasiHari").classList.remove("text-dark");
            document.getElementById("estimasiHari").classList.add("text-danger");

        } else {
            badge.classList.add("d-none");

            document.getElementById("estimasiHari").classList.remove("text-danger");
            document.getElementById("estimasiHari").classList.add("text-dark");
        }
    }

    startInput.addEventListener("change", updateUI);
    endInput.addEventListener("change", updateUI);

});
</script>
@endsection
