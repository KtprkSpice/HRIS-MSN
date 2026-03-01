@extends('layout.dashboard')
@section('header', 'Pengaturan Profil')

@section('content')

<style>
    /* Container Styling */
    .profile-card {
        background: #ffffff;
        border: none;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }

    /* Header Profile dengan Background Halus */
    .profile-header-bg {
        background: linear-gradient(135deg, #bc5e6b 0%, #8e444f 100%);
        height: 120px;
        width: 100%;
    }

    /* Image Wrapper */
    .profile-avatar-wrapper {
        margin-top: -60px;
        position: relative;
        margin-bottom: 20px;
    }

    .profile-img {
        width: 130px;
        height: 130px;
        object-fit: cover;
        border-radius: 50%;
        border: 5px solid #ffffff;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        background-color: #f8f9fa;
        transition: all 0.3s ease;
    }

    .profile-img:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(188, 94, 107, 0.2);
    }

    /* Form Styling */
    .form-group-custom {
        margin-bottom: 1.5rem;
    }

    .form-label {
        font-weight: 700;
        color: #344767;
        font-size: 0.82rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
    }

    .form-label i {
        margin-right: 10px;
        color: #bc5e6b; /* Warna ikon maroon konsisten */
        font-size: 1rem;
        width: 20px;
        text-align: center;
    }

    .form-control-custom {
        border-radius: 10px;
        padding: 12px 16px;
        border: 1px solid #d2d6da;
        font-size: 0.95rem;
        transition: all 0.2s;
        width: 100%;
    }

    .form-control-custom:focus {
        border-color: #bc5e6b;
        box-shadow: 0 0 0 2px rgba(188, 94, 107, 0.1);
        outline: none;
    }

    .form-control-custom[readonly] {
        background-color: #f8f9fa;
        color: #7b809a;
        cursor: not-allowed;
    }

    /* Buttons */
    .btn-save-custom {
        background: linear-gradient(90deg, #bc5e6b 0%, #a34a57 100%);
        color: white;
        border: none;
        border-radius: 10px;
        padding: 12px 30px;
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(188, 94, 107, 0.2);
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
    }

    .btn-save-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(188, 94, 107, 0.3);
        color: white;
    }

    .btn-cancel-custom {
        background-color: transparent;
        color: #6c757d;
        border: 1px solid #d2d6da;
        border-radius: 10px;
        padding: 12px 30px;
        font-weight: 600;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
    }

    .btn-cancel-custom:hover {
        background-color: #f8f9fa;
        color: #344767;
    }

    /* Section Title */
    .section-title {
        color: #344767;
        font-weight: 800;
        position: relative;
        padding-bottom: 10px;
        margin-bottom: 25px;
        display: flex;
        align-items: center;
    }

    .section-title::after {
        content: '';
        position: absolute;
        left: 0;
        bottom: 0;
        width: 40px;
        height: 3px;
        background: #bc5e6b;
        border-radius: 3px;
    }

    /* Alert Styling */
    .alert-modern {
        border-radius: 12px;
        border: none;
        font-weight: 500;
    }
</style>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            
            @if (session('success'))
                <div class="alert alert-success alert-modern shadow-sm mb-4">
                    <i class="fa-solid fa-check-circle me-2"></i> {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-modern shadow-sm mb-4">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li><i class="fa-solid fa-circle-exclamation me-2"></i> {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="profile-card shadow">
                <div class="profile-header-bg"></div>

                <form method="POST" enctype="multipart/form-data" action="{{ route('profile.update', $employee->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="card-body p-4 pt-0">
                        <div class="text-center profile-avatar-wrapper">
                            <img id="imagePreview" 
                                 src="{{ $employee->foto ? asset('storage/'.$employee->foto) : 'https://ui-avatars.com/api/?name='.urlencode($employee->fullname).'&background=bc5e6b&color=fff&size=150' }}" 
                                 alt="Avatar" 
                                 class="profile-img">
                        </div>

                        <div class="row px-md-4">
                            <div class="col-12">
                                <h5 class="section-title">
                                    <i class="fa-solid fa-address-card me-2 text-secondary"></i> Informasi Dasar
                                </h5>
                            </div>

                            <div class="col-md-12 form-group-custom">
                                <label class="form-label">
                                    <i class="fa-solid fa-user"></i> Nama Lengkap
                                </label>
                                <input type="text" name="fullname" class="form-control-custom" 
                                       value="{{ old('fullname', $employee->fullname) }}" required>
                            </div>

                            <div class="col-md-6 form-group-custom">
                                <label class="form-label">
                                    <i class="fa-solid fa-envelope"></i> Email
                                </label>
                                <input type="email" class="form-control-custom" 
                                       value="{{ $employee->email }}" readonly>
                            </div>

                            <div class="col-md-6 form-group-custom">
                                <label class="form-label">
                                    <i class="fa-brands fa-whatsapp"></i> No. WhatsApp/Telepon
                                </label>
                                <input type="text" name="phone" class="form-control-custom" 
                                    value="{{ old('phone', $employee->phone) }}" placeholder="08xxxx">
                            </div>

                            <div class="col-md-12 form-group-custom">
                                <label class="form-label">
                                    <i class="fa-solid fa-solid fa-camera"></i> Ganti Foto Profil
                                </label>
                                <div class="input-group-wrapper" style="position: relative;">
                                    <input type="file" name="foto" class="form-control-custom" 
                                        id="inputFoto" onchange="readURL(this);" 
                                        style="padding-left: 45px;">
                                    <i class="fa-solid fa-upload" 
                                    style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #bc5e6b; opacity: 0.7;">
                                    </i>
                                </div>
                                <small class="text-muted mt-2 d-block px-1">
                                    <i class="fa-solid fa-circle-info me-1"></i> Format gambar: JPG atau PNG (Maks. 2MB).
                                </small>
                            </div>

                            <div class="col-12 mt-3 mb-5">
    <div class="p-3 rounded-3 bg-light shadow-sm">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <div class="bg-white p-2 rounded-circle shadow-sm me-3 text-danger">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <div>
                    <h6 class="mb-0 fw-bold" style="color: #344767;">Keamanan Akun</h6>
                    <p class="mb-0 text-muted small">Update kata sandi secara berkala</p>
                </div>
            </div>
            <a href="{{ route('profile.password') }}" class="btn btn-sm btn-outline-danger px-3 fw-bold" style="border-radius: 8px;">
                <i class="fa-solid fa-key me-1"></i> Ganti Password
            </a>
        </div>
    </div>
</div>

                            <div class="col-12 d-flex justify-content-between align-items-center border-top pt-4">
                                <a href="{{ url()->previous() }}" class="btn-cancel-custom text-decoration-none shadow-sm">
                                    <i class="fa-solid fa-arrow-left me-2"></i> Batal
                                </a>
                                <button type="submit" name="update_all" class="btn-save-custom">
                                    <i class="fa-solid fa-floppy-disk me-2"></i> Simpan Perubahan
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Live Preview Gambar
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('imagePreview').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

@endsection