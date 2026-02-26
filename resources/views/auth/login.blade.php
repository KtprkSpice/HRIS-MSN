<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Dashboard</title>

    {{-- Bootstrap --}}
    <link href="{{ asset('bootstrap-5.3.8-dist/css/bootstrap.min.css') }}" rel="stylesheet">

    {{-- Font --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(rgba(0, 0, 0, .6), rgba(0, 0, 0, .6)),
                url('{{ asset('build/assets/img/bg login.png') }}') center/cover no-repeat;
        }

        /* Password Toggle Icon */
        .password-wrapper {
            position: relative;
        }

        .btn-toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #666;
            cursor: pointer;
            padding: 5px 8px;
            font-size: 18px;
            transition: color 0.3s ease;
        }

        .btn-toggle-password:hover {
            color: #333;
        }

        .btn-toggle-password:focus {
            outline: none;
        }

        .password-wrapper .form-control {
            padding-right: 40px;
        }

        .password-wrapper .form-control.is-invalid {
            padding-right: 40px;
        }
        .card {
            background: rgba(255, 255, 255, 0.60); /* transparan */
            backdrop-filter: blur(10px);           /* efek kaca (glassmorphism) */
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
</head>

<body class="d-flex align-items-center justify-content-center min-vh-100">

    <div class="card shadow-lg border-0 rounded-4" style="width: 380px;">
        <div class="card-body p-4 p-md-5">

            {{-- Logo --}}
            <div class="text-center mb-3">
                <img src="{{ asset('build/assets/img/logo.png') }}" alt="Logo" class="img-fluid"
                    style="width: 80px;">
            </div>

           <h4 class="text-center fw-semibold mb-1">Login Dashboard</h4>
            <p class="text-center text-muted small mb-4">PT. Megajaya Sarana Nusantara</p>

            {{-- GLOBAL ERROR --}}
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Login gagal!</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- STATUS MESSAGE (Fortify) --}}
            @if (session('status'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('status') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                {{-- Email --}}
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                        value="{{ old('email') }}" required autofocus>

                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="mb-3">
                    <label class="form-label">Password</label>

                    <div class="position-relative password-wrapper">
                        <input type="password" id="passwordInput" name="password" class="form-control @error('password') is-invalid @enderror"
                            required>
                        <button type="button" class="btn-toggle-password" id="togglePasswordBtn">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>

                    @error('password')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Lupa Password + Remember --}}
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <a href="{{ route('password.request') }}" class="small">
                        Lupa Password?
                    </a>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                        <label class="form-check-label" for="remember">
                            Ingat saya
                        </label>
                    </div>
                </div>



                <button type="submit" class="btn btn-success w-100 py-2">
                    Login
                </button>
            </form>

        </div>
    </div>

</body>

<script src="{{ asset('bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js') }}"></script>

{{-- Font Awesome --}}
<link rel="stylesheet" href="{{ asset('fontawesome-free-7.1.0-web/css/all.min.css') }}" crossorigin="anonymous" referrerpolicy="no-referrer">

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleBtn = document.getElementById('togglePasswordBtn');
        const passwordInput = document.getElementById('passwordInput');

        if (toggleBtn && passwordInput) {
            toggleBtn.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Toggle input type antara password dan text
                const isPassword = passwordInput.type === 'password';
                passwordInput.type = isPassword ? 'text' : 'password';
                
                // Ubah icon
                const icon = toggleBtn.querySelector('i');
                if (isPassword) {
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });
        }
    });
</script>

</html>
