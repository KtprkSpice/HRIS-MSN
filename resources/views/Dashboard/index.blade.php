@extends('layout.dashboard')

@section('header', 'Dashboard PT. Swat Service Indonesia')

@section('content')
    <!-- Notice Board + Notifikasi -->
    <div class="row mb-4 d-flex align-items-stretch">
        <div class="col-md-10 d-flex">
            <div class="notice-board shadow-sm flex-fill">
                <h3><i class="fa-solid fa-bullhorn"></i> INFORMASI</h3>
                <p>Selamat datang di sistem dashboard PT. Swat Service Indonesia.<br>Harap periksa pengumuman terbaru HRD,
                    laporan mingguan, serta pengingat cuti di sini.</p>
            </div>
        </div>
        <div class="col-md-2 d-flex">
            <div class="card shadow-sm flex-fill">
                <div class="card-header">
                    <h5 class="mb-0">Notifikasi Terbaru</h5>
                </div>
                <div class="accordion" id="accordionExample">
                     <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                                <i class="fa-solid fa-user-check text-primary me-2"></i>
                                <strong>Absensi</strong>
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                            <div class="accordion-body">
                                <p>Ada 3 karyawan terlambat hari ini.</p>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                <i class="fa-solid fa-calendar-check text-success me-2"></i>
                                <strong>Pengajuan Cuti</strong>
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                            <div class="accordion-body">
                                <p>2 pengajuan cuti menunggu persetujuan.</p>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                <i class="fa-solid fa-file-lines text-info me-2"></i>
                                <strong>Laporan Tugas</strong>
                            </button>
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                            <div class="accordion-body">
                                <p>5 laporan tugas baru telah diunggah.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cards -->
    <div class="row g-3 mb-4 cards">
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="fs-2 text-primary"><i class="fa-solid fa-users"></i></div>
                    <h5 class="card-title">Jumlah Karyawan</h5>
                    <p class="card-text">85</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="fs-2 text-success"><i class="fa-solid fa-building"></i></div>
                    <h5 class="card-title">Departemen</h5>
                    <p class="card-text">4</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="fs-2 text-danger"><i class="fa-solid fa-calendar-xmark"></i></div>
                    <h5 class="card-title">Cuti Pending</h5>
                    <p class="card-text">5</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="fs-2 text-warning"><i class="fa-solid fa-user-check"></i></div>
                    <h5 class="card-title">Kehadiran</h5>
                    <p class="card-text">100%</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Visi/Misi + Table Terburuk + Chart -->
    <div class="row g-4 mt-4 align-items-stretch">
        <div class="col-md-4 d-flex">
            <div class="card shadow-sm text-center flex-fill">
                <div class="card-body">
                    <img src="{{ asset('build/assets/img/logo.png') }}" alt="Logo" class="visi-logo mb-3">
                    <h5>Visi</h5>
                    <p>Menjadi perusahaan pelayanan terbaik serta memiliki standar internasional dan terpercaya.</p>
                    <h5>Misi</h5>
                    <p>Menciptakan standar pelayanan melalui peningkatan kualitas dan memberikan rasa aman serta nyaman.</p>
                </div>
            </div>
        </div>

        <div class="col-md-4 d-flex">
            <div class="card shadow-sm flex-fill">
                <div class="card-header bg-primary text-white"><i class="fa-solid fa-user-times"></i> 10 Karyawan dengan
                    Kinerja Terendah</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table id="karyawanTable" class="table table-striped table-hover mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Nama</th>
                                    <th>Departemen</th>
                                    <th>Skor</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>nama</td>
                                    <td>Departemen</td>
                                    <td>skor</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 d-flex">
            <div class="card shadow-sm flex-fill">
                <div class="card-header bg-primary text-white"><i class="fa-solid fa-chart-pie"></i> Karyawan Berdasarkan
                    Gender</div>
                <div class="card-body">
                    <div id="genderChart"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Carousel -->
    <div class="row g-4 mt-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <span><i class="fa-solid fa-bullhorn"></i> Pengumuman & Iklan</span>
                    <small class="text-light">Informasi resmi perusahaan</small>
                </div>
                <div class="card-body p-0">
                    <div id="carouselIklanFull" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            <div class="carousel-item active"><a href="#"><img
                                        src="{{ asset('build/assets/img/background.jpg') }}"
                                        class="d-block w-100 carousel-img-full" alt="Iklan 1"></a></div>
                            <div class="carousel-item"><a href="#"><img
                                        src="{{ asset('build/assets/img/background2.jpg') }}"
                                        class="d-block w-100 carousel-img-full" alt="Iklan 2"></a></div>
                            <div class="carousel-item"><a href="#"><img
                                        src="{{ asset('build/assets/img/background3.jpg') }}"
                                        class="d-block w-100 carousel-img-full" alt="Iklan 3"></a></div>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselIklanFull"
                            data-bs-slide="prev"><span class="carousel-control-prev-icon"></span></button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carouselIklanFull"
                            data-bs-slide="next"><span class="carousel-control-next-icon"></span></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
