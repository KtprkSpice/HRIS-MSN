@extends('layout.dashboard')
@section('header', 'Edit Profile')

@section('content')

<style>
    body { background-color: #f8f9fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; transition: background 0.5s, color 0.5s; }
    body.bg-dark { background-color: #1e1e2f; color: #e0e0e0; }

    .sidebar { width: 240px; min-height: 100vh; background: linear-gradient(to bottom, #343a40, #1d1f27); color: white; position: fixed; top: 0; left: 0; padding-top: 20px; transition: all 0.3s ease; }
    .sidebar .logo img { max-height: 45px; margin-right: 10px; transition: transform 0.3s ease; }
    .sidebar .logo:hover img { transform: rotate(15deg) scale(1.1); }
    .sidebar ul { padding: 0; list-style: none; }
    .sidebar ul li a { color: #cfd8dc; display: block; padding: 14px 20px; text-decoration: none; border-radius: 10px; transition: all 0.3s ease; }
    .sidebar ul li a:hover, .sidebar ul li a.active { background: #495057; color: #fff; transform: translateX(5px); }

    .main-container { margin-left: 260px; padding: 30px; transition: all 0.3s ease; }
    header h1 { color: #2c3e50; font-weight: 700; }
    .profile-img { width: 150px; height: 150px; object-fit: cover; border-radius: 50%; border: 4px solid #007bff; transition: transform 0.3s ease; }
    .profile-img:hover { transform: scale(1.05); }

    .card-profile { max-width: 900px; margin: 0 auto; border-radius: 15px; box-shadow: 0 6px 18px rgba(0,0,0,0.1); padding: 30px; background: #ffffff; transition: transform 0.3s ease, box-shadow 0.3s ease; }
    .card-profile:hover { transform: translateY(-5px); box-shadow: 0 12px 25px rgba(0,0,0,0.15); }

    .btn-primary { background-color: #007bff; border-color: #007bff; }
    .btn-primary:hover { background-color: #0056b3; border-color: #0056b3; }

    label { font-weight: 500; }
    </style>

    <div class="card card-profile">
            <div class='alert alert-success'>msg</div>
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
