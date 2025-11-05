<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Dashboard PT. Swat Service Indonesia</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <script src="{{ asset('js/app.js') }}"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])


    <!-- Bootstrap 5 -->
    <link href="{{ asset('bootstrap-5.3.8-dist/css/bootstrap.min.css') }}" rel="stylesheet">
    <script src="{{ asset('bootstrap-5.3.8-dist/js/bootstrap.bundle.js') }}"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('fontawesome-free-7.1.0-web/css/all.min.css') }}" crossorigin="anonymous"
        referrerpolicy="no-referrer" />

    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('DataTables/datatables.min.css') }}">

    <!-- ApexCharts -->
    <script src="{{ asset('js/apexcharts.js') }}"></script>

    <style>
        /* ===== BODY ===== */
        body {
            background-color: #f4f6f9;
            color: #222;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            transition: background 0.5s, color 0.5s;
        }

        body.bg-dark {
            background-color: #1b1b2f;
            color: #e0e0e0;
        }

        /* ===== SIDEBAR ===== */
        .sidebar {
            width: 250px;
            min-height: 100vh;
            background: linear-gradient(180deg, #204070ff 0%, #1565c0 60%, #1e88e5 100%);
            color: #fff;
            position: fixed;
            top: 0;
            left: 0;
            padding-top: 25px;
            box-shadow: 3px 0 10px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease, background 0.5s;
        }

        body.bg-dark .sidebar {
            background: linear-gradient(180deg, #0f1b3a 0%, #1a2a55 60%, #243b7c 100%);
        }

        .sidebar .logo img {
            max-height: 45px;
            margin-right: 10px;
            transition: transform 0.3s ease;
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
            margin-bottom: 6px;
        }

        .sidebar ul li a {
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

        .sidebar ul li a i {
            margin-right: 12px;
            font-size: 16px;
            width: 22px;
            text-align: center;
        }

        .sidebar ul li a:hover,
        .sidebar ul li a.active {
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
            transform: translateX(5px);
            box-shadow: inset 2px 0 0 #fff;
        }

        /* ===== MAIN CONTAINER ===== */
        .main-container {
            margin-left: 260px;
            padding: 25px;
            transition: all 0.3s ease;
        }

        /* ===== HEADER ===== */
        header h1 {
            color: #1a237e;
            font-weight: 700;
            transition: color 0.5s;
        }

        body.bg-dark header h1 {
            color: #ffffffff;
        }

        .profile img {
            border-radius: 50%;
            transition: transform 0.3s ease;
        }

        .profile img:hover {
            transform: scale(1.1);
        }

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

        body.bg-dark .notice-board {
            background: #2f2f3d;
            color: #f1f1f1;
            border-color: #444464;
        }

        /* ===== CARDS ===== */
        .cards .card {
            text-align: center;
            border-radius: 12px;
            transition: transform 0.3s ease, box-shadow 0.3s ease, background 0.5s, color 0.5s;
            cursor: pointer;
        }

        body.bg-dark .cards .card {
            background: #2a2a3d;
            color: #f1f1f1;
        }

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
            background: linear-gradient(to right, #0d47a1, #1976d2);
            color: #fff;
            transition: background 0.5s;
        }

        body.bg-dark .table thead {
            background: linear-gradient(to right, #1a2b5f, #243b7c);
        }

        .table tbody tr:hover {
            background: #f1f3f5;
            transform: translateX(3px);
            transition: all 0.3s ease;
        }

        body.bg-dark .table tbody tr:hover {
            background: #3a3a4d;
        }

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

        body.bg-dark .carousel-caption {
            background: rgba(0, 0, 0, 0.5);
        }

        /* ===== Dark Mode Icons ===== */
        #moon-icon,
        #sun-icon {
            transition: all 0.3s ease;
        }

        body.bg-dark #moon-icon {
            color: #f1c40f;
        }

        body.bg-dark #sun-icon {
            color: #f39c12;
        }

        @media (max-width:768px) {
            .carousel-img-full {
                height: 300px;
            }
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="d-flex align-items-center justify-content-center mb-4">
            <div class="logo d-flex align-items-center">
                <img src="{{ asset('build/assets/img/logo.png') }}" alt="Logo">
                <span class="fw-bold">PT. Swat Service Indonesia</span>
            </div>
        </div>
        <ul>
            <li><a href="{{ route('dashboard.index') }}" class="{{ request()->is('dashboard') ? 'active' : '' }}"><i
                        class="fa-solid fa-house"></i> Dashboard</a></li>
            <li><a href="{{ route('employee.index') }}"><i class="fa-solid fa-id-card"></i> Data Karyawan</a></li>
            <li><a href="{{ route('task.index') }}" class="{{ request()->is('task') ? 'active' : '' }}"><i
                        class="fa-solid fa-tasks"></i> Tugas</a></li>
            <li><a href="{{ route('payroll.index') }}" class="{{ request()->is('payroll') ? 'active' : '' }}"><i
                        class="fa-solid fa-money-bill"></i> Slip Gaji</a></li>
            <li><a href="cuti.php"><i class="fa-solid fa-plane"></i> Pengajuan Cuti</a></li>
            <li><a href="kehadiran.php"><i class="fa-solid fa-user-check"></i> Kehadiran</a></li>
            <li><a href="#"><i class="fa-solid fa-chart-line"></i> Laporan</a></li>
            <li><a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
        </ul>
    </aside>

    <div class="main-container">

        <header class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">@yield('header')</h1>
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-outline-secondary btn-sm" id="toggleModeBtn">
                    <i id="moon-icon" class="fa-solid fa-moon"></i>
                    <i id="sun-icon" class="fa-solid fa-sun d-none"></i>
                </button>
                <div class="dropdown profile">
                    <a class="d-flex align-items-center text-decoration-none dropdown-toggle" href="#"
                        role="button" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="https://i.pravatar.cc/40" alt="User" class="me-2 rounded-circle">
                        <div><strong>USername</strong><br><small class="text-muted">Role</small></div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="profileDropdown">
                        <li>
                            <a class="dropdown-item" href="profile.php"><i class="fa-solid fa-user me-2"></i> Edit
                                Profile</a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item text-danger" href="logout.php"><i
                                    class="fa-solid fa-right-from-bracket me-2"></i> Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </header>
        @yield('content')
    </div>

    <!-- jQuery + DataTables -->
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('DataTables/datatables.min.js') }}"></script>
    <script src="{{ asset('js/dashboard.js') }}"></script>
    <script src="{{ asset('js/app.js') }}"></script>


</body>

</html>
