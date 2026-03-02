<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password | Modern System</title>

    {{-- Bootstrap --}}
    <link href="{{ asset('bootstrap-5.3.8-dist/css/bootstrap.min.css') }}" rel="stylesheet">
    {{-- Font Awesome --}}
    <link rel="stylesheet" href="{{ asset('fontawesome-free-7.1.0-web/css/all.min.css') }}">
    {{-- Font --}}
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            /* Background selaras dengan halaman ganti password */
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),
                        url('{{ asset('build/assets/img/bg login.png') }}') center/cover no-repeat fixed;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }

        /* Glassmorphism Card */
        .glass-card {
            background: rgba(255, 255, 255, 0.48);
            backdrop-filter: blur(15px) saturate(180%);
            -webkit-backdrop-filter: blur(15px) saturate(180%);
            border-radius: 28px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 400px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            overflow: hidden;
        }

        .card-body {
            padding: 2.5rem !important;
        }

        .logo-img {
            width: 80px;
            filter: drop-shadow(0 4px 6px rgba(0,0,0,0.2));
            margin-bottom: 20px;
        }

        .page-title {
            color: #ffffff;
            font-weight: 700;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        .text-muted-light {
            color: rgba(255, 255, 255, 0.8) !important;
        }

        /* Form Styling */
        .form-label {
            font-weight: 600;
            font-size: 0.85rem;
            color: #ffffff;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
        }

        .form-label i {
            margin-right: 8px;
            color: #ff8e9e;
            width: 15px;
            text-align: center;
        }

        .form-control {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 12px;
            padding: 12px 16px;
            border: none;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(255, 255, 255, 0.2);
        }

        /* Button Styling - Gradasi Maroon */
        .btn-submit {
            background: linear-gradient(135deg, #bc5e6b 0%, #8e444f 100%);
            border: none;
            border-radius: 12px;
            padding: 14px;
            font-weight: 700;
            color: white;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.85rem;
            margin-top: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 10px 20px rgba(188, 94, 107, 0.3);
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            filter: brightness(1.1);
            color: white;
        }

        .btn-back {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.85rem;
            text-decoration: none;
            transition: color 0.2s;
        }

        .btn-back:hover {
            color: #ffffff;
        }

        .alert {
            border-radius: 14px;
            border: none;
            font-size: 0.85rem;
        }
    </style>
</head>

<body>

    <div class="glass-card">
        <div class="card-body">

            {{-- Logo --}}
            <div class="text-center">
                <img src="{{ asset('build/assets/img/logo.png') }}" alt="Logo" class="logo-img">
            </div>

            <div class="text-center mb-4">
                <h4 class="page-title mb-1">Lupa Password</h4>
                <p class="text-muted-light small">Masukkan email Anda untuk menerima link reset password</p>
            </div>

            {{-- STATUS SUCCESS --}}
            @if (session('status'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                    <i class="fa-solid fa-circle-check me-2"></i> {{ session('status') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="mb-4">
                    <label class="form-label">
                        <i class="fa-solid fa-envelope"></i> Alamat Email
                    </label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                        value="{{ old('email') }}" placeholder="contoh@email.com" required autofocus>
                    @error('email')
                        <div class="invalid-feedback text-white-50 small mt-2">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-submit w-100">
                    Kirim Link Reset <i class="fa-solid fa-paper-plane ms-2"></i>
                </button>
            </form>

            <div class="text-center mt-4">
                <a href="{{ route('login') }}" class="btn-back">
                    <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Login
                </a>
            </div>

        </div>
    </div>

    <script src="{{ asset('bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js') }}"></script>
</body>

</html>