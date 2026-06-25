<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('build/img/logo.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <script src="{{ asset('js/app.js') }}"></script>



    <!-- Bootstrap 5 -->
    <link href="{{ asset('bootstrap-5.3.8-dist/css/bootstrap.min.css') }}" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('fontawesome-free-7.1.0-web/css/all.min.css') }}" crossorigin="anonymous"
        referrerpolicy="no-referrer" />

    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('DataTables/datatables.min.css') }}">

    {{-- Sweet Alert --}}
    <link rel="stylesheet" href="{{ asset('css/sweetalert2.min.css') }}">

    <style>
        /* ===== BODY ===== */
        body {
            background-color: #f4f6f9;
            color: #222;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            transition: background 0.5s, color 0.5s;
        }

        /* body.bg-dark { */
        /* background-color: #1b1b2f; */
        /* color: #e0e0e0; */


        /* ===== SIDEBAR ===== */
        .sidebar {
            width: 250px;
            min-height: 100vh;
            background: linear-gradient(180deg, #7a1f2a 0%, #aa2c36 60%, #c73947 100%);
            color: #fff;
            position: fixed;
            top: 0;
            left: 0;
            padding-top: 25px;
            box-shadow: 3px 0 10px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease, background 0.5s;
        }

        /* body.bg-dark .sidebar {
            background: linear-gradient(180deg, #5a1820 0%, #7a1f2a 60%, #8b2435 100%);
        } */

        .sidebar .logo img {
            max-height: 45px;
            margin-right: 10px;
            transition: transform 0.3s ease;
            background-color: #ffffff;
            /* Warna background */
            padding: 8px;
            /* Jarak dalam */
            border-radius: 12px;
        }

        .sidebar .logo:hover img {
            transform: rotate(10deg) scale(1.08);
        }

        .sidebar ul {
            padding: 0;
            list-style: none;
            margin-top: 20px;
        }

        .sidebar ul li {
            margin-bottom: 3px;
        }

        .sidebar ul li a,
        .sidebar ul li form button {
            color: #e3f2fd;
            display: flex;
            align-items: center;
            font-weight: 500;
            padding: 12px 22px;
            text-decoration: none;
            border-radius: 10px;
            font-size: 15px;
            letter-spacing: 0.3px;
            transition: all 0.3s ease;
        }

        .sidebar ul li a i,
        .sidebar ul li form button i {
            margin-right: 12px;
            font-size: 16px;
            width: 22px;
            text-align: center;
        }

        .sidebar ul li a:hover,
        .sidebar ul li a.active,
        .sidebar ul li form button:hover {
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
            transform: translateX(5px);
            box-shadow: inset 2px 0 0 #fff;
        }

        .sidebar ul li form button {
            background: none;
            border: none;
            width: 100%;
            text-align: left;
            cursor: pointer;
        }

        /* ===== MAIN CONTAINER ===== */
        .main-container {
            margin-left: 260px;
            padding: 25px;
            transition: all 0.3s ease;
        }

        /* ===== HEADER ===== */
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            padding: 12px 0;
            margin-bottom: 24px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.08);
            transition: border-color 0.5s;
        }

        /* body.bg-dark header {
            border-bottom-color: rgba(255,255,255, 0.1);
        } */

        header .header-left {
            display: flex;
            align-items: center;
            gap: 12px;
            flex: 0 0 auto;
        }

        header .header-center {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            min-width: 0;
        }

        header .header-right {
            display: flex;
            align-items: center;
            gap: 12px;
            flex: 0 0 auto;
        }

        header h1 {
            color: #aa2c36;
            font-weight: 700;
            transition: color 0.5s;
            margin: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* body.bg-dark header h1 {
            color: #ff6b7a;
        } */

        header .btn,
        #toggleSidebar,
        #toggleModeBtn {
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        header .btn:hover,
        #toggleSidebar:hover,
        #toggleModeBtn:hover {
            transform: translateY(-1px);
        }

        .profile {
            display: flex;
            align-items: center;
        }

        .profile img {
            border-radius: 50%;
            transition: transform 0.3s ease;
            width: 36px;
            height: 36px;
            object-fit: cover;
        }

        .profile img:hover {
            transform: scale(1.1);
        }

        .profile a {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .profile div {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .profile strong {
            font-size: 14px;
            line-height: 1.2;
        }

        .profile small {
            font-size: 12px;
        }

        /* ===== DROPDOWN MENU ===== */
        .dropdown-menu {
            border-radius: 8px;
            border: 1px solid rgba(0, 0, 0, 0.1);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        /* body.bg-dark .dropdown-menu {
            background-color: #2a2a3d;
            border-color: rgba(255,255,255, 0.1);
            color: #f1f1f1;
        } */

        /* body.bg-dark .dropdown-item {
            color: #f1f1f1;
        }

        body.bg-dark .dropdown-item:hover,
        body.bg-dark .dropdown-item:focus {
            background-color: #3a3a4d;
            color: #fff;
        }

        body.bg-dark .dropdown-divider {
            border-color: rgba(255,255,255, 0.1);
        } */

        /* ===== NOTICE BOARD ===== */
        .notice-board {
            background: #fff3cd;
            border: 2px solid #ffecb5;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease, background 0.5s, color 0.5s;
        }

        /* body.bg-dark .notice-board { */
        /* background: #2f2f3d; */
        /* color: #f1f1f1; */
        /* border-color: #444464; */


        /* ===== CARDS ===== */
        .cards .card {
            text-align: center;
            border-radius: 12px;
            transition: transform 0.3s ease, box-shadow 0.3s ease, background 0.5s, color 0.5s;
            cursor: pointer;
        }

        /* body.bg-dark .cards .card { */
        /* background: #2a2a3d; */
        /* color: #f1f1f1; */


        .cards .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        .cards .card .fs-2 {
            transition: transform 0.5s ease, color 0.3s ease;
        }

        .cards .card:hover .fs-2 {
            transform: rotate(-10deg) scale(1.2);
        }

        /* ===== TABLE ===== */
        .table thead {
            background: linear-gradient(to right, #7a1f2a, #aa2c36);
            color: #fff;
            transition: background 0.5s;
        }

        /* body.bg-dark .table thead { */
        /* background: linear-gradient(to right, #5a1820, #7a1f2a); */


        /* .table tbody tr:hover {
            background: #f1f3f5;
            transform: translateX(3px);
            transition: all 0.3s ease;
        }

        body.bg-dark .table tbody tr:hover {
            background: #3a3a4d;
        } */

        /* ===== CAROUSEL ===== */
        .carousel-img-full {
            width: 100%;
            height: 450px;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .carousel-item img:hover {
            transform: scale(1.05);
        }

        /* body.bg-dark .carousel-caption { */
        /* background: rgba(0, 0, 0, 0.5); */


        /* ===== Dark Mode Icons ===== */
        #moon-icon,
        #sun-icon {
            transition: all 0.3s ease;
        }

        /* body.bg-dark #moon-icon { */
        /* color: #f1c40f; */


        /* body.bg-dark #sun-icon { */
        /* color: #f39c12; */


        /* Tabe leftg */
        table thead th,
        table tbody td {
            text-align: start !important;
        }

        @media (max-width:768px) {
            .carousel-img-full {
                height: 300px;
            }
        }

        /* Sidebar collapse */
        .sidebar.collapsed {
            margin-left: -250px;
        }

        .main-container.full {
            margin-left: 0;
        }

        /* ===== RESPONSIVE DESIGN ===== */
        /* Mobile devices: 576px and below */
        @media (max-width: 576px) {

            /* Sidebar: convert to full overlay on very small screens */
            .sidebar {
                width: 100%;
                position: fixed;
                left: 0;
                top: 0;
                height: 100vh;
                z-index: 1050;
                margin-left: 0;
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            /* Add dark overlay when sidebar is shown */
            .sidebar.show::before {
                content: '';
                position: fixed;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                z-index: -1;
            }

            .main-container {
                margin-left: 0;
                padding: 15px;
            }

            .main-container.full {
                margin-left: 0;
            }

            /* Header: optimize layout on small screens */
            header {
                flex-wrap: nowrap;
                gap: 8px;
                padding: 10px 0;
                margin-bottom: 16px;
            }

            header .header-left,
            header .header-center,
            header .header-right {
                flex-basis: auto;
                justify-content: center;
            }

            header .header-left {
                flex: 0 0 auto;
                justify-content: flex-start !important;
            }

            header .header-center {
                flex: 1;
                justify-content: center !important;
                min-width: 0;
            }

            header .header-right {
                flex: 0 0 auto;
                justify-content: flex-end !important;
            }

            header h1 {
                font-size: 1rem;
                word-break: break-word;
            }

            header .btn,
            #toggleSidebar,
            #toggleModeBtn {
                padding: 6px 10px;
                font-size: 12px;
            }

            .profile img {
                width: 32px;
                height: 32px;
            }

            .profile a {
                flex-direction: column;
                gap: 2px;
            }

            .profile strong {
                font-size: 11px;
            }

            .profile small {
                font-size: 9px;
            }

            /* Cards: full width on mobile */
            .cards .card {
                margin-bottom: 12px;
            }

            .card {
                font-size: 0.95rem;
            }

            .card-body {
                padding: 12px;
            }

            .card-header {
                padding: 10px 12px;
                font-size: 0.95rem;
            }

            /* Tables: add horizontal scroll on mobile */
            .table-responsive {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            table {
                font-size: 0.85rem;
            }

            table th,
            table td {
                padding: 8px 6px !important;
            }

            /* Sidebar logo and text: make compact */
            .sidebar .logo img {
                max-height: 35px;
            }

            .sidebar .logo span {
                font-size: 0.9rem;
            }

            .sidebar ul li a {
                padding: 10px 16px;
                font-size: 13px;
            }

            .sidebar ul li a i {
                margin-right: 8px;
                font-size: 14px;
            }

            /* Notice board: reduce padding */
            .notice-board {
                padding: 12px;
                font-size: 0.9rem;
            }

            .notice-board h3 {
                font-size: 1rem;
            }

            .notice-board p {
                font-size: 0.85rem;
            }

            /* Carousel: smaller height on mobile */
            .carousel-img-full {
                height: 200px;
            }

            /* Buttons: full width on mobile where needed */
            .btn {
                font-size: 0.9rem;
                padding: 6px 12px;
            }

            /* Row gaps: reduce on mobile */
            .row.g-3 {
                --bs-gutter-x: 0.75rem;
                --bs-gutter-y: 0.75rem;
            }

            .row.g-4 {
                --bs-gutter-x: 0.75rem;
                --bs-gutter-y: 0.75rem;
            }
        }

        /* Tablets: 577px to 992px */
        @media (max-width: 992px) and (min-width: 577px) {
            .sidebar {
                width: 220px;
                padding-top: 20px;
            }

            .main-container {
                margin-left: 230px;
                padding: 20px;
            }

            .sidebar .logo span {
                font-size: 0.95rem;
            }

            .sidebar ul li a {
                padding: 10px 18px;
                font-size: 14px;
            }

            .carousel-img-full {
                height: 350px;
            }

            header h1 {
                font-size: 1.5rem;
            }

            /* Col adjustments for tablets */
            .col-md-4 {
                flex: 0 0 calc(100% - 8px);
                max-width: calc(100% - 8px);
            }

            .col-md-3 {
                flex: 0 0 calc(50% - 8px);
                max-width: calc(50% - 8px);
            }
        }

        /* Large screens: 993px and up */
        @media (min-width: 993px) {
            .sidebar {
                width: 250px;
                padding-top: 25px;
            }

            .main-container {
                margin-left: 260px;
                padding: 25px;
            }
        }

        /* Extra small fixes for ultra-small devices (320px - 375px) */
        @media (max-width: 375px) {
            header h1 {
                font-size: 1rem;
            }

            .card-title {
                font-size: 0.9rem;
            }

            .card-text {
                font-size: 0.85rem;
            }

            #toggleSidebar {
                padding: 4px 8px;
                font-size: 0.9rem;
            }

            .btn-outline-secondary {
                padding: 4px 8px;
                font-size: 0.85rem;
            }
        }

        <>

        /* Hover tombol */
        .btn-hover-effect {
            transition: all 0.3s ease !important;
        }

        .btn-hover-effect:hover {
            transform: translateY(-2px) scale(1.03);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
        }

        /* Glow effect */
        .swal2-popup {
            border-radius: 18px !important;
            backdrop-filter: blur(10px);
        }

        /* Animasi halus */
        .animate__animated {
            animation-duration: 0.5s;
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="d-flex align-items-center justify-content-center mb-4">
            <div class="logo d-flex align-items-center">
                <img src="{{ asset('build/img/logo.png') }}" alt="Logo">
                <span class="fw-bold">PT. Megajaya Sarana Nusantara</span>
            </div>
        </div>
        <ul>
            @if ($userRole === 'owner')
                <li><a href="{{ route('dashboard.index') }}" class="{{ request()->is('dashboard') ? 'active' : '' }}"><i
                            class="fa-solid fa-house"></i> Dashboard</a></li>
                <li><a href="{{ route('employee.index') }}" class="{{ request()->is('employee') ? 'active' : '' }}"><i
                            class="fa-solid fa-id-card"></i> Data Karyawan</a></li>
                <li><a href="{{ route('task.index') }}" class="{{ request()->is('task') ? 'active' : '' }}"><i
                            class="fa-solid fa-tasks"></i> Tugas</a></li>
                <li><a href="{{ route('schedule.index') }}" class="{{ request()->is('schedule') ? 'active' : '' }}"><i
                            class="fa-solid fa-clipboard-list"></i> Jadwal</a></li>
                <li><a href="{{ route('salary.index') }}" class="{{ request()->is('salary') ? 'active' : '' }}"><i
                            class="fa-solid fa-money-bill" class="{{ request()->is('salary') ? 'active' : '' }}"></i>
                        Slip Gaji</a></li>
                <li><a href="{{ route('leave-request.index') }}"
                        class="{{ request()->is('leave-request') ? 'active' : '' }}"><i class="fa-solid fa-plane"></i>
                        Pengajuan Cuti</a>
                </li>
                <li><a href="{{ route('leave-type.index') }}"
                        class="{{ request()->is('leave-type') ? 'active' : '' }}"><i class="fa-solid fa-plane"></i>
                        Jenis Cuti</a></li>
                <li><a href="{{ route('presence.index') }}" class="{{ request()->is('presence') ? 'active' : '' }}"><i
                            class="fa-solid fa-user-check"></i> Kehadiran</a></li>
                <li><a href="{{ route('division.index') }}" class="{{ request()->is('division') ? 'active' : '' }}"><i
                            class="fa-solid fa-user-check"></i> Divisi</a></li>
                <li><a href="{{ route('report.index') }}" class="{{ request()->is('report') ? 'active' : '' }}"><i
                            class="fa-solid fa-chart-line"></i>
                        Laporan</a></li>
                <li>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="margin:0;">
                        @csrf
                        <button type="button" class="sidebar-link" onclick="confirmLogout()">
                            <i class="fa-solid fa-right-from-bracket"></i>
                            Logout
                        </button>
                    </form>
                </li>
            @endif
            @if ($userRole === 'hr')
                <li><a href="{{ route('dashboard.index') }}"
                        class="{{ request()->is('dashboard') ? 'active' : '' }}"><i class="fa-solid fa-house"></i>
                        Dashboard</a></li>
                <li><a href="{{ route('employee.index') }}" class="{{ request()->is('employee') ? 'active' : '' }}"><i
                            class="fa-solid fa-id-card"></i> Data Karyawan</a></li>
                <li><a href="{{ route('task.index') }}" class="{{ request()->is('task') ? 'active' : '' }}"><i
                            class="fa-solid fa-tasks"></i> Tugas</a></li>
                <li><a href="{{ route('schedule.index') }}" class="{{ request()->is('schedule') ? 'active' : '' }}"><i
                            class="fa-solid fa-clipboard-list"></i> Jadwal</a></li>
                <li><a href="{{ route('salary.index') }}" class="{{ request()->is('salary') ? 'active' : '' }}"><i
                            class="fa-solid fa-money-bill" class="{{ request()->is('salary') ? 'active' : '' }}"></i>
                        Slip Gaji</a></li>
                <li><a href="{{ route('leave-request.index') }}"
                        class="{{ request()->is('leave-request') ? 'active' : '' }}"><i class="fa-solid fa-plane"></i>
                        Pengajuan Cuti</a>
                </li>
                <li><a href="{{ route('leave-type.index') }}"
                        class="{{ request()->is('leave-type') ? 'active' : '' }}"><i class="fa-solid fa-plane"></i>
                        Jenis Cuti</a></li>
                <li><a href="{{ route('presence.index') }}" class="{{ request()->is('presence') ? 'active' : '' }}"><i
                            class="fa-solid fa-user-check"></i> Kehadiran</a></li>
                <li>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="margin:0;">
                        @csrf
                        <button type="button" class="sidebar-link" onclick="confirmLogout()">
                            <i class="fa-solid fa-right-from-bracket"></i>
                            Logout
                        </button>
                    </form>
                </li>
            @endif
            @if ($userRole === 'employee')
                <li><a href="{{ route('dashboard.index') }}"
                        class="{{ request()->is('dashboard') ? 'active' : '' }}"><i class="fa-solid fa-house"></i>
                        Dashboard</a></li>
                <li><a href="{{ route('task.index') }}" class="{{ request()->is('task') ? 'active' : '' }}"><i
                            class="fa-solid fa-tasks"></i> Tugas</a></li>
                <li><a href="{{ route('salary.index') }}" class="{{ request()->is('salary') ? 'active' : '' }}"><i
                            class="fa-solid fa-money-bill" class="{{ request()->is('salary') ? 'active' : '' }}"></i>
                        Slip Gaji</a></li>
                <li><a href="{{ route('leave-request.index') }}"
                        class="{{ request()->is('leave-request') ? 'active' : '' }}"><i
                            class="fa-solid fa-plane"></i>
                        Pengajuan Cuti</a>
                </li>
                <li><a href="{{ route('presence.index') }}"
                        class="{{ request()->is('presence') ? 'active' : '' }}"><i
                            class="fa-solid fa-user-check"></i> Kehadiran</a></li>
                <li><a href="{{ route('schedule.index') }}"
                        class="{{ request()->is('schedule') ? 'active' : '' }}"><i
                            class="fa-solid fa-clipboard-list"></i> Jadwal</a></li>
                <li>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="margin:0;">
                        @csrf
                        <button type="button" onclick="confirmLogout()" class="sidebar-link">
                            <i class="fa-solid fa-right-from-bracket"></i>
                            Logout
                        </button>
                    </form>
                </li>
            @endif
        </ul>
    </aside>

    <div class="main-container">

        <header>
            <!-- Header Left: Hamburger Button -->
            <div class="header-left">
                <button class="btn btn-outline-secondary" id="toggleSidebar" title="Toggle Menu">
                    <i class="fa-solid fa-bars"></i>
                </button>
            </div>

            <!-- Header Center: Title -->
            <div class="header-center">
                <h1 class="h3">@yield('header')</h1>
            </div>

            <!-- Header Right: Dark Mode + Profile -->
            <div class="header-right">
                <!-- <button class="btn btn-outline-secondary btn-sm" id="toggleModeBtn" title="Toggle Dark Mode">
                    <i id="moon-icon" class="fa-solid fa-moon"></i>
                    <i id="sun-icon" class="fa-solid fa-sun d-none"></i>
                </button> -->
                <div class="dropdown profile">
                    <a class="d-flex align-items-center text-decoration-none dropdown-toggle" href="#"
                        role="button" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="https://i.pravatar.cc/300?img={{ auth()->user()->employee->id }}" alt="User"
                            class="rounded-circle">
                        <div>
                            <strong>{{ ucwords(auth()->user()->employee->fullname) }}</strong>
                            <small class="text-muted">{{ ucwords(auth()->user()->role->name) }}</small>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="profileDropdown">
                        <li>
                            <a class="dropdown-item"
                                href="{{ route('profile.edit', auth()->user()->employee->id) }}"><i
                                    class="fa-solid fa-user me-2"></i> Edit
                                Profile</a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <form id="dropdown-logout-form" action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="button" onclick="confirmLogoutDropdown()"
                                    class="dropdown-item text-danger">
                                    <i class="fa-solid fa-right-from-bracket me-2"></i> Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>
        @yield('content')

        <script>
            function confirmLogoutDropdown() {
                Swal.fire({
                    title: 'Logout Sekarang?',
                    html: '<small>Sesi kamu akan berakhir dan harus login kembali.</small>',
                    icon: 'question',

                    // 🎨 Samakan style
                    background: 'rgba(255, 255, 255, 0.85)',
                    color: '#495057',

                    backdrop: `
            rgba(0,0,0,0.7)
            blur(8px)
        `,

                    showCancelButton: true,
                    confirmButtonText: 'Ya, Logout',
                    cancelButtonText: 'Batal',

                    confirmButtonColor: '#eb2549',
                    cancelButtonColor: '#6c757d',

                    // 🔥 Animasi SAMA
                    showClass: {
                        popup: `
                animate__animated
                animate__zoomIn
                animate__faster
            `
                    },
                    hideClass: {
                        popup: `
                animate__animated
                animate__zoomOut
                animate__faster
            `
                    },

                    // 🔥 Class SAMA
                    customClass: {
                        popup: 'rounded-4 shadow-lg',
                        confirmButton: 'btn-hover-effect',
                        cancelButton: 'btn-hover-effect'
                    }

                }).then((result) => {
                    if (result.isConfirmed) {

                        // 🔄 Loading SAMA
                        Swal.fire({
                            title: 'Logging out...',
                            text: 'Tunggu sebentar ya',
                            background: 'rgba(255, 255, 255, 0.9)',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        setTimeout(() => {
                            document.getElementById('dropdown-logout-form').submit();
                        }, 1200);
                    }
                });
            }
        </script>


        {{-- Footer --}}
        {{-- <footer></footer> --}}
    </div>


    {{-- Script --}}
    {{-- <script src="{{ asset('js/dashboard.js') }}"></script> --}}
    <script src="{{ asset('js/app.js') }}"></script>

    {{-- Swal --}}
    <script src="{{ asset('js/sweetalert2.all.min.js') }}"></script>

    {{-- Bootsrap --}}
    <script src="{{ asset('bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js') }}"></script>

    <!-- ApexCharts -->
    <script src="{{ asset('js/apexcharts.js') }}"></script>

    {{-- Data Table Logic --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const toggleBtn = document.getElementById("toggleSidebar");
            const sidebar = document.querySelector(".sidebar");
            const mainContainer = document.querySelector(".main-container");
            const body = document.body;

            // Create backdrop overlay for mobile
            const backdrop = document.createElement("div");
            backdrop.className = "sidebar-backdrop";
            backdrop.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1049;
            display: none;
            opacity: 0;
            transition: opacity 0.3s ease;
        `;
            body.appendChild(backdrop);

            // For mobile (< 576px): use overlay mode
            // For tablet/desktop (>= 576px): use collapse mode
            toggleBtn.addEventListener("click", function() {
                const screenWidth = window.innerWidth; // Check screen width on every click
                if (screenWidth < 576) {
                    sidebar.classList.toggle("show");
                    if (sidebar.classList.contains("show")) {
                        backdrop.style.display = "block";
                        setTimeout(() => backdrop.style.opacity = "1", 10);
                    } else {
                        backdrop.style.opacity = "0";
                        setTimeout(() => backdrop.style.display = "none", 300);
                    }
                } else {
                    sidebar.classList.toggle("collapsed");
                    mainContainer.classList.toggle("full");
                }
            });

            // Close sidebar when clicking on backdrop or outside on mobile
            backdrop.addEventListener("click", function() {
                sidebar.classList.remove("show");
                backdrop.style.opacity = "0";
                setTimeout(() => backdrop.style.display = "none", 300);
            });

            document.addEventListener("click", function(e) {
                const screenWidth = window.innerWidth; // Check screen width dynamically
                if (screenWidth < 576 && !sidebar.contains(e.target) && !toggleBtn.contains(e.target) && !
                    backdrop.contains(e.target)) {
                    if (sidebar.classList.contains("show")) {
                        sidebar.classList.remove("show");
                        backdrop.style.opacity = "0";
                        setTimeout(() => backdrop.style.display = "none", 300);
                    }
                }
            });

            // Handle window resize
            window.addEventListener("resize", function() {
                const newScreenWidth = window.innerWidth;
                if (newScreenWidth < 576) {
                    sidebar.classList.remove("collapsed");
                    mainContainer.classList.remove("full");
                    sidebar.classList.remove("show");
                    backdrop.style.display = "none";
                    backdrop.style.opacity = "0";
                } else {
                    sidebar.classList.remove("show");
                    backdrop.style.display = "none";
                    backdrop.style.opacity = "0";
                }
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

    <script>
        function confirmLogout() {
            Swal.fire({
                title: 'Logout Sekarang?',
                html: '<small>Sesi kamu akan berakhir dan harus login kembali.</small>',
                icon: 'question',
                background: 'rgba(255, 255, 255, 0.85)',
                color: '#495057',
                backdrop: `
            rgba(0,0,0,0.7)
            blur(8px)
        `,
                showCancelButton: true,
                confirmButtonText: 'Ya, Logout',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#eb2549',
                cancelButtonColor: '#6c757d',

                // 🔥 Animasi masuk
                showClass: {
                    popup: `
                animate__animated
                animate__zoomIn
                animate__faster
            `
                },

                // 🔥 Animasi keluar
                hideClass: {
                    popup: `
                animate__animated
                animate__zoomOut
                animate__faster
            `
                },

                // 🔥 Hover effect button
                customClass: {
                    popup: 'rounded-4 shadow-lg',
                    confirmButton: 'btn-hover-effect',
                    cancelButton: 'btn-hover-effect'
                }

            }).then((result) => {
                if (result.isConfirmed) {

                    // 🔥 Loading sebelum logout (biar smooth)
                    Swal.fire({
                        title: 'Logging out...',
                        text: 'Tunggu sebentar ya',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    setTimeout(() => {
                        document.getElementById('logout-form').submit();
                    }, 1200);
                }
            });
        }
    </script>
</body>

</html>
