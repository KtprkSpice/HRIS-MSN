<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password</title>

    {{-- Bootstrap --}}
    <link href="{{ asset('bootstrap-5.3.8-dist/css/bootstrap.min.css') }}" rel="stylesheet">

    {{-- Font --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(rgba(0, 0, 0, .6), rgba(0, 0, 0, .6)),
                url('{{ asset('build/assets/img/bg.jpg') }}') center/cover no-repeat;
        }
    </style>
</head>

<body class="d-flex align-items-center justify-content-center min-vh-100">

    <div class="card shadow-lg border-0 rounded-4" style="width: 380px;">
        <div class="card-body p-4 p-md-5">

            {{-- Logo --}}
            <div class="text-center mb-3">
                <img src="{{ asset('build/assets/img/logo.png') }}" alt="Logo" style="width: 80px;">
            </div>

            <h4 class="text-center fw-semibold mb-4">Lupa Password</h4>

            {{-- STATUS SUCCESS --}}
            @if (session('status'))
                <div class="alert alert-success small">
                    {{ session('status') }}
                </div>
            @endif


            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                        value="{{ old('email') }}" placeholder="contoh@email.com" required>
                </div>

                <button type="submit" class="btn btn-success w-100 py-2">
                    Kirim Link Reset
                </button>
            </form>

            <div class="text-center mt-3">
                <a href="{{ route('login') }}" class="text-decoration-none small">
                    ← Kembali ke Login
                </a>
            </div>

        </div>
    </div>

</body>

<script src="{{ asset('bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js') }}"></script>

</html>
