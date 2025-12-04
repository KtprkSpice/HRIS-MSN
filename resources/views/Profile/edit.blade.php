@extends('layout.dashboard')
@section('header', 'Edit Profile')

@section('content')

<style>

    .main-container { margin-left: 260px; padding: 30px; transition: all 0.3s ease; }
    header h1 { color: #2c3e50; font-weight: 700; }
    .profile-img { width: 150px; height: 150px; object-fit: cover; border-radius: 50%; border: 4px solid #007bff; transition: transform 0.3s ease; }
    .profile-img:hover { transform: scale(1.05); }


    label { font-weight: 500; }
    </style>

<div class='alert alert-success'>msg</div>
    <div class="card card-profile p-10">
        <form method="POST" enctype="multipart/form-data">
            <div class="text-center mb-4">
                <img src="" alt="Foto Profil" class="profile-img">
            </div>

            <h5 class="mb-3">Data Profil</h5>
            <div class="mb-3"><label>Nama</label>
                <input type="text" name="nama" class="form-control" value="{{ old('name', $employee->fullname) }}" required>
            </div>
            <div class="mb-3"><label>Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $employee->email) }}">
            </div>
            <div class="mb-3"><label>Nomor Handphone</label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone', $employee->phone) }}"
                    placeholder="Contoh: 081234567890">
            </div>
            <div class="mb-4"><label>Upload Foto Profil</label>
                <input type="file" name="foto" class="form-control">
                <small class="text-muted">Pilih file gambar baru jika ingin mengganti foto</small>
            </div>

            <h5 class="mb-3">Ganti Password</h5>
            <div class="mb-3"><label>Password Baru</label>
                <input type="password" name="new_password" class="form-control">
            </div>
            <div class="mb-4"><label>Konfirmasi Password Baru</label>
                <input type="password" name="confirm_password" class="form-control">
            </div>

            <div class="d-flex justify-content-between">
                <button type="submit" name="update_all" class="btn btn-primary">Simpan Semua</button>
                <a href="dashboard.php" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
@endsection
