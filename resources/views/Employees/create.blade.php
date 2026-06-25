@extends('layout.dashboard')
@section('header', 'Tambah Data Karyawan')

@section('content')
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body">
            <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
                <div class="icon-header">
                    <i class="fa-solid fa-user-plus"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-0">Tambah Karyawan Baru</h5>
                    <p class="text-muted small mb-0">Lengkapi data karyawan dengan informasi yang akurat</p>
                </div>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger border-0 rounded-3 mb-4" role="alert">
                    <div class="d-flex align-items-start gap-3">
                        <i class="fa-solid fa-circle-exclamation mt-1" style="flex-shrink: 0; font-size: 18px;"></i>
                        <div>
                            <h6 class="fw-semibold mb-2">Terjadi Kesalahan</h6>
                            <ul class="mb-0 small">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <form class="row g-4" action="{{ route('employee.store') }}" method="post">
                @csrf
                <div class="col-md-6">
                    <label for="fullname" class="form-label fw-semibold mb-2">
                        <i class="fa-solid fa-user me-2" style="color: #4f4f5a;"></i>Nama Lengkap
                    </label>
                    <input type="text" class="form-control form-control-modern @error('fullname') is-invalid @enderror"
                        id="fullname" name="fullname" required value="{{ old('fullname') }}"
                        placeholder="Masukkan nama lengkap">
                    @error('fullname')
                        <span class="invalid-feedback d-block mt-2"><i
                                class="fa-solid fa-triangle-exclamation me-1"></i>{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="nik" class="form-label fw-semibold mb-2">
                        <i class="fa-solid fa-id-card me-2" style="color: #4f4f5a;"></i>NIK
                    </label>
                    <input type="number" min="0"
                        class="form-control form-control-modern @error('nik') is-invalid @enderror" id="nik"
                        placeholder="Nomor Induk Kependudukan" required value="{{ old('nik') }}" name="nik">
                    @error('nik')
                        <span class="invalid-feedback d-block mt-2"><i
                                class="fa-solid fa-triangle-exclamation me-1"></i>{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="email" class="form-label fw-semibold mb-2">
                        <i class="fa-solid fa-envelope me-2" style="color: #4f4f5a;"></i>Email
                    </label>
                    <input type="email" class="form-control form-control-modern @error('email') is-invalid @enderror"
                        id="email" name="email" required value="{{ old('email') }}" placeholder="nama@gmail.com">
                    @error('email')
                        <span class="invalid-feedback d-block mt-2"><i
                                class="fa-solid fa-triangle-exclamation me-1"></i>{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="phone" class="form-label fw-semibold mb-2">
                        <i class="fa-solid fa-phone me-2" style="color: #4f4f5a;"></i>No Telepon
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">+62</span>
                        <input type="number" class="form-control form-control-modern @error('phone') is-invalid @enderror"
                            min="0" id="phone" placeholder="8912345678" value="{{ old('phone') }}"
                            name="phone" required>
                        @error('phone')
                            <span class="invalid-feedback d-block mt-2"><i
                                    class="fa-solid fa-triangle-exclamation me-1"></i>{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="position_id" class="form-label fw-semibold mb-2">
                        <i class="fa-solid fa-sitemap me-2" style="color: #4f4f5a;"></i>Posisi
                    </label>
                    <select id="position_id"
                        class="form-select form-control-modern @error('position_id') is-invalid @enderror"
                        name="position_id" required>
                        <option value="">Pilih Posisi...</option>
                        @foreach ($positions as $position)
                            <option value="{{ $position->id }}"
                                {{ old('position_id') == $position->id ? 'selected' : '' }}>
                                {{ ucwords($position->name) }}</option>
                        @endforeach
                    </select>
                    @error('position_id')
                        <span class="invalid-feedback d-block mt-2"><i
                                class="fa-solid fa-triangle-exclamation me-1"></i>{{ $message }}</span>
                    @enderror
                </div>
                @if (auth()->user()->role?->name === 'owner')
                    <div class="col-md-6">
                        <label for="role_id" class="form-label fw-semibold mb-2">
                            <i class="fa-solid fa-user-shield me-2" style="color: #4f4f5a;"></i>Role
                        </label>
                        <select id="role_id"
                            class="form-select form-control-modern @error('role_id') is-invalid @enderror" name="role_id"
                            required>
                            <option value="">Pilih Role...</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                    {{ ucwords($role->name) }}
                                </option>
                            @endforeach
                        </select>
                        @error('role_id')
                            <span class="invalid-feedback d-block mt-2">
                                <i class="fa-solid fa-triangle-exclamation me-1"></i>{{ $message }}
                            </span>
                        @enderror
                    </div>
                @elseif(auth()->user()->role?->name === 'hr')
                    <input type="hidden" name="role_id" value="{{ $roles->where('name', 'employee')->first()?->id }}">
                @endif
                <div class="col-md-6">
                    <label for="no_bpjs_kesehatan" class="form-label fw-semibold mb-2">
                        <i class="fa-solid fa-heart me-2" style="color:#4f4f5a;"></i>No BPJS Kesehatan
                    </label>
                    <input type="number"
                        class="form-control form-control-modern @error('bpjs_kesehatan') is-invalid @enderror"
                        id="no_bpjs_kesehatan" placeholder="Nomor BPJS Kesehatan" min="0" name="bpjs_kesehatan"
                        required value="{{ old('bpjs_kesehatan') }}">
                    @error('bpjs_kesehatan')
                        <span class="invalid-feedback d-block mt-2"><i
                                class="fa-solid fa-triangle-exclamation me-1"></i>{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="no_bpjs_ketenagakerjaan" class="form-label fw-semibold mb-2">
                        <i class="fa-solid fa-shield me-2" style="color: #4f4f5a;"></i>No BPJS Ketenagakerjaan
                    </label>
                    <input type="number"
                        class="form-control form-control-modern @error('bpjs_ketenagakerjaan') is-invalid @enderror"
                        min="0" id="no_bpjs_ketenagakerjaan" placeholder="Nomor BPJS Ketenagakerjaan"
                        name="bpjs_ketenagakerjaan" required value="{{ old('bpjs_ketenagakerjaan') }}">
                    @error('bpjs_ketenagakerjaan')
                        <span class="invalid-feedback d-block mt-2"><i
                                class="fa-solid fa-triangle-exclamation me-1"></i>{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="birh_date" class="form-label fw-semibold mb-2">
                        <i class="fa-solid fa-cake-candles me-2" style="color: #4f4f5a;"></i>Tanggal Lahir
                    </label>
                    <input type="date"
                        class="form-control form-control-modern @error('born_date') is-invalid @enderror" id="birh_date"
                        name="born_date" required value="{{ old('born_date') }}">
                    @error('born_date')
                        <span class="invalid-feedback d-block mt-2"><i
                                class="fa-solid fa-triangle-exclamation me-1"></i>{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="hire_date" class="form-label fw-semibold mb-2">
                        <i class="fa-solid fa-handshake me-2" style="color: #4f4f5a;"></i>Tanggal Kerja
                    </label>
                    <input type="date"
                        class="form-control form-control-modern @error('hire_date') is-invalid @enderror" id="hire_date"
                        name="hire_date" required value="{{ old('hire_date') }}">
                    @error('hire_date')
                        <span class="invalid-feedback d-block mt-2"><i
                                class="fa-solid fa-triangle-exclamation me-1"></i>{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="npwp" class="form-label fw-semibold mb-2">
                        <i class="fa-solid fa-receipt me-2" style="color:#4f4f5a;"></i>NPWP
                    </label>
                    <input type="text" pattern="[0-9\.\-]+"
                        class="form-control form-control-modern @error('npwp') is-invalid @enderror" id="npwp"
                        placeholder="12.345.678.9-123.456" name="npwp" required value="{{ old('npwp') }}">
                    @error('npwp')
                        <span class="invalid-feedback d-block mt-2"><i
                                class="fa-solid fa-triangle-exclamation me-1"></i>{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-12">
                    <label for="address" class="form-label fw-semibold mb-2">
                        <i class="fa-solid fa-map-location-dot me-2" style="color:#4f4f5a;"></i>Alamat
                    </label>
                    <input type="text" class="form-control form-control-modern @error('address') is-invalid @enderror"
                        id="address" placeholder="Jalan, No, Kelurahan, Kecamatan, Kota..." name="address" required
                        value="{{ old('address') }}">
                    @error('address')
                        <span class="invalid-feedback d-block mt-2"><i
                                class="fa-solid fa-triangle-exclamation me-1"></i>{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-12 pt-3">
                    <div class="d-flex gap-3 justify-content-end">
                        <a href="{{ route('employee.index') }}" class="btn btn-light border px-5 rounded-3">
                            <i class="fa-solid fa-arrow-left me-2"></i>Batal
                        </a>
                        <button type="submit" class="btn btn-primary px-5 rounded-3">
                            <i class="fa-solid fa-floppy-disk me-2"></i>Simpan Karyawan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <style>
        .icon-header {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #57565f 0%, #7b7b7e 100%);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
        }

        .form-control-modern,
        .form-select {
            border: 2px solid #e5e7eb !important;
            border-radius: 12px !important;
            padding: 12px 16px !important;
            font-size: 15px;
            transition: all 0.3s ease;
        }

        .form-control-modern:focus,
        .form-select:focus {
            border-color: #4f46e5 !important;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1) !important;
            background-color: #f9fafb;
        }

        .form-control-modern::placeholder {
            color: #9ca3af;
            font-size: 14px;
        }

        .form-label {
            color: #1f2937;
            font-size: 15px;
        }

        .input-group-text {
            border: 2px solid #e5e7eb !important;
            border-radius: 12px 0 0 12px !important;
            font-weight: 600;
            color: #6b7280;
        }

        .input-group .form-control-modern {
            border-radius: 0 12px 12px 0 !important;
            border-left: none !important;
        }

        .invalid-feedback {
            font-size: 13px;
            color: #dc2626 !important;
        }

        .form-control-modern.is-invalid,
        .form-select.is-invalid {
            border-color: #fca5a5 !important;
            background-image: none;
        }

        .form-control-modern.is-invalid:focus,
        .form-select.is-invalid:focus {
            border-color: #dc2626 !important;
            box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1) !important;
        }

        .btn {
            font-weight: 600;
            transition: all 0.2s ease;
            border: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
            box-shadow: 0 4px 15px rgba(79, 70, 229, 0.3);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #4338ca 0%, #4f46e5 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(79, 70, 229, 0.4);
        }

        .btn-light:hover {
            background-color: #f3f4f6 !important;
            transform: translateY(-2px);
        }

        .alert {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            border-left: 4px solid #dc2626 !important;
        }

        .alert-danger {
            color: #7f1d1d;
        }
    </style>
@endsection
