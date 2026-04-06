@extends('layout.dashboard')

@section('header', 'Dashboard PT. Megajaya Sarana Nusantara')

@section('content')
    <style>
        /* Responsive card adjustments */
        @media (max-width: 576px) {
            .col-md-3 {
                flex: 0 0 100%;
                max-width: 100%;
            }

            .col-md-4 {
                flex: 0 0 100%;
                max-width: 100%;
            }

            .col-md-10.5 {
                flex: 0 0 100%;
                max-width: 100%;
            }

            .cards .card {
                margin-bottom: 12px;
            }

            .notice-board h3 {
                font-size: 1.1rem;
            }

            .notice-board p {
                font-size: 0.9rem;
            }
        }

        @media (max-width: 992px) and (min-width: 577px) {
            .col-md-3 {
                flex: 0 0 calc(50% - 8px);
                max-width: calc(50% - 8px);
            }

            .col-md-4 {
                flex: 0 0 calc(50% - 8px);
                max-width: calc(50% - 8px);
            }
        }
    </style>
    <!-- Notice Board + Notifikasi -->
    <div class="row mb-4 d-flex align-items-stretch">
        <div class="col-md-10.5 d-flex">
            <div class="notice-board shadow-sm flex-fill">
                <h3><i class="fa-solid fa-bullhorn"></i> INFORMASI</h3>
                <p>Selamat datang di Sistem HR PT. Megajaya Sarana Nusantara.<br>Silakan akses dan kelola data karyawan,
                    absensi, pengajuan cuti, pengumuman resmi, serta laporan kepegawaian melalui dashboard ini.</p>
            </div>
        </div>
        <!--<div class="col-md-2 d-flex">
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
                                                                                        <button class="accordion-button collapsed text-black" type="button" data-bs-toggle="collapse"
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
                        <p class="card-text">{{ $totalEmployee }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="fs-2 text-success"><i class="fa-solid fa-building"></i></div>
                        <h5 class="card-title">Departemen</h5>
                        <p class="card-text">{{ $totalDivisions }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="fs-2 text-danger"><i class="fa-solid fa-calendar-xmark"></i></div>
                        <h5 class="card-title">Cuti Pending</h5>
                        <p class="card-text">{{ $leaveCounts }}</p>
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
                        <img src="{{ asset('build/assets/img/logo.png') }}" alt="Logo" width="110"
                            class="mb-3 d-block mx-auto">
                        <h5>Visi</h5>
                        <p>Menjadi pemimpin dalam industri Services, IT service, building management, office suppliers, dan
                            Office Inovation serta Pengembangan dan Perbaikan secara terus menerus, memiliki produk dan
                            services yang berkualitas dan standar yang tinggi yang mampu mempertahankan profitabilitas kedua
                            pihak dan Good Corporate government.</p>
                        <h5>Misi</h5>
                        <ol class="text-start">
                            <li>1. Menawarkan produk yang berkualitas, inovatif, dan state of the art pelayanan bermutu dan
                                bernilai tambah untuk pelanggan.</li>
                            <li>2. Menawarkan provivilitas dan mendapatkan keuntungan atau manfaat yang baik secara optimal.
                            </li>
                            <li>3. Memaksimalkan potensi karyawan dengan memperhatikan karir dan kesejahteraan karyawan.
                            </li>
                            <li>4. Menciptakan lingkungan yang lebih baik secara langsung atau tak langsung bagi masyarakat.
                            </li>
                        </ol>
                    </div>
                </div>
            </div>

            @if ($userRole === 'employee')
                <div class="col-md-4 d-flex">
                    <div class="card shadow-sm flex-fill">
                        <div class="card-header text-white" style="background-color: #aa2c36;"><i
                                class="fa-solid fa-calendar"></i> Kalender Kerja & Jam Kerja</div>
                        <div class="card-body">
                            <div class="text-center mb-3">
                                <div class="digital-clock" id="digitalClock"
                                    style="font-size: 2.5rem; font-weight: bold; color: #aa2c36; font-family: 'Courier New', monospace;">
                                    00:00:00
                                </div>
                                <small class="text-muted">Waktu Indonesia Barat (WIB)</small>
                            </div>
                            <div id="miniCalendar" style="margin-top: 20px;"></div>
                        </div>
                    </div>
                </div>
            @else
                <div class="col-md-4 d-flex">
                    <div class="card shadow-sm flex-fill">
                        <div class="card-header text-white" style="background-color: #aa2c36;"><i
                                class="fa-solid fa-user-times"></i> 10 Karyawan dengan
                            Kinerja Terendah</div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table id="kpiTable" class="table table-striped table-hover mb-0">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Nama</th>
                                            <th>Departemen</th>
                                            <th>Skor</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($employees as $employee)
                                            <tr>
                                                <td>{{ $employee->fullname }}</td>
                                                <td>{{ $employee->division->name }}</td>
                                                <td>Score</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="col-md-4 d-flex">
                <div class="card shadow-sm flex-fill">
                    <div class="card-header text-white" style="background-color: #aa2c36;"><i
                            class="fa-solid fa-chart-pie"></i> Karyawan Berdasarkan
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
                <div class="card-header text-white d-flex justify-content-between align-items-center" style="background-color: #aa2c36;">
                    <span><i class="fa-solid fa-bullhorn"></i>Informasi</span>
                    <small class="text-light">PT. Megajaya Sarana Nusantara</small>
                </div>
                <div class="card-body p-0">
                    <div id="carouselIklanFull" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            <div class="carousel-item active"><a href="#"><img
                                        src="{{ asset('build/assets/img/struktur.png') }}"
                                        class="d-block w-100 carousel-img-full" alt="Iklan 1"></a></div>
                            <div class="carousel-item"><a href="#"><img
                                        src="{{ asset('build/assets/img/partner.png') }}"
                                        class="d-block w-100 carousel-img-full" alt="Iklan 2"></a></div>
                            <div class="carousel-item"><a href="#"><img
                                        src="{{ asset('build/assets/img/iso.PNG') }}"
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

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const genderChart = document.querySelector("#genderChart");
                if (genderChart) {
                    const male = @json($maleEmployee);
                    const female = @json($femaleEmployee);
                    const options = {
                        chart: {
                            type: 'pie',
                            height: 300
                        },
                        // Dummy data (ubah nanti sesuai data backend)
                        series: [male, female],
                        labels: ['Laki-laki', 'Perempuan'],
                        colors: ['#120588', '#e83e8c'],
                        legend: {
                            position: 'bottom'
                        },
                        responsive: [{
                            breakpoint: 768,
                            options: {
                                chart: {
                                    height: 250
                                }
                            }
                        }]
                    };

                    const chart = new ApexCharts(genderChart, options);
                    chart.render();
                }

                // Update clock every 1 second
                setInterval(updateDigitalClock, 1000);
                updateDigitalClock(); // Initial call

                // Mini Calendar
                function generateMiniCalendar() {
                    const now = new Date();
                    const year = now.getFullYear();
                    const month = now.getMonth();
                    const firstDay = new Date(year, month, 1);
                    const lastDay = new Date(year, month + 1, 0);
                    const daysInMonth = lastDay.getDate();
                    const startingDayOfWeek = firstDay.getDay();

                    const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov',
                        'Des'
                    ];
                    const dayNames = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];

                    let calendarHTML =
                        `<div style="text-align: center; border: 1px solid #ddd; border-radius: 8px; padding: 10px; background: #f9f9f9;">`;
                    calendarHTML += `<h6 style="margin: 0 0 10px 0; color: #aa2c36;">${monthNames[month]} ${year}</h6>`;
                    calendarHTML += `<table style="width: 100%; border-collapse: collapse;">`;
                    calendarHTML += `<tr>`;

                    // Day headers
                    dayNames.forEach(day => {
                        calendarHTML +=
                            `<th style="padding: 5px; font-weight: bold; font-size: 0.75rem;">${day}</th>`;
                    });
                    calendarHTML += `</tr>`;

                    // Empty cells for days before month starts
                    calendarHTML += `<tr>`;
                    for (let i = 0; i < startingDayOfWeek; i++) {
                        calendarHTML += `<td style="padding: 5px; text-align: center; font-size: 0.75rem;"></td>`;
                    }

                    // Days of month
                    let dayOfWeek = startingDayOfWeek;
                    for (let day = 1; day <= daysInMonth; day++) {
                        const isToday = day === now.getDate();
                        const bgColor = isToday ? '#aa2c36' : '#f0f0f0';
                        const textColor = isToday ? '#fff' : '#333';

                        calendarHTML +=
                            `<td style="padding: 5px; text-align: center; border: 1px solid #eee; background-color: ${bgColor}; color: ${textColor}; font-size: 0.75rem; font-weight: ${isToday ? 'bold' : 'normal'}; border-radius: 4px;">${day}</td>`;

                        dayOfWeek++;
                        if (dayOfWeek > 6) {
                            calendarHTML += `</tr><tr>`;
                            dayOfWeek = 0;
                        }
                    }

                    // Fill remaining cells
                    while (dayOfWeek > 0 && dayOfWeek < 7) {
                        calendarHTML += `<td style="padding: 5px; text-align: center; font-size: 0.75rem;"></td>`;
                        dayOfWeek++;
                    }

                    calendarHTML += `</tr></table></div>`;

                    document.getElementById('miniCalendar').innerHTML = calendarHTML;
                }

                generateMiniCalendar();
            });

            // DataTabele
            let table = new DataTable('#kpiTable', {
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                    paginate: {
                        previous: "Sebelumnya",
                        next: "Berikutnya",
                    },
                },
                columnDefs: [{
                    targets: 6,
                    orderable: false,
                    searchable: false
                }]
            });

            // Digital Clock WIB
            function updateDigitalClock() {
                const now = new Date();
                const wibTime = new Date(now.toLocaleString('en-US', {
                    timeZone: 'Asia/Jakarta'
                }));

                const hours = String(wibTime.getHours()).padStart(2, '0');
                const minutes = String(wibTime.getMinutes()).padStart(2, '0');
                const seconds = String(wibTime.getSeconds()).padStart(2, '0');

                document.getElementById('digitalClock').textContent = `${hours}:${minutes}:${seconds}`;
            }
        </script>
    @endsection
