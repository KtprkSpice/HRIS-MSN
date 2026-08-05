<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>

    {{-- Bootstrap --}}
    <link href="{{ asset('bootstrap-5.3.8-dist/css/bootstrap.min.css') }}" rel="stylesheet">

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="{{ asset('fontawesome-free-7.1.0-web/css/all.min.css') }}">

    {{-- Font --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(rgba(0, 0, 0, .6), rgba(0, 0, 0, .6)),
                url('{{ asset('build/img/bg.jpg') }}') center/cover no-repeat;
        }
    </style>
</head>

<body class="d-flex align-items-center justify-content-center min-vh-100">

    <div class="card shadow-lg border-0 rounded-4" style="width: 380px;">
        <div class="card-body p-4 p-md-5">

            {{-- Logo --}}
            <div class="text-center mb-3">
                <img src="{{ asset('build/img/logo.png') }}" alt="Logo" class="img-fluid"
                    style="width: 80px;">
            </div>

            <h4 class="text-center fw-semibold mb-4">Reset Password</h4>

            {{-- STATUS MESSAGE (Fortify) --}}
            @if (session('status'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('status') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- VALIDATION ERRORS --}}
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif


            <form method="POST" action="{{ route('password.update') }}">
                @csrf

                {{-- Email --}}
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control"
                        value="{{ old('email', $request->email) }}" required>
                </div>

                {{-- Password --}}
                <div class="mb-3">
                    <label class="form-label">Password Baru</label>
                    <div class="input-group">
                        <input type="password" name="password" id="password" class="form-control" required>
                        <span class="input-group-text" role="button" onclick="togglePassword('password', this)">
                            <i class="fa-solid fa-eye"></i>
                        </span>
                    </div>
                </div>

                {{-- Confirm Password --}}
                <div class="mb-4">
                    <label class="form-label">Konfirmasi Password</label>
                    <div class="input-group">
                        <input type="password" name="password_confirmation" id="password_confirmation"
                            class="form-control" required>
                        <span class="input-group-text" role="button"
                            onclick="togglePassword('password_confirmation', this)">
                            <i class="fa-solid fa-eye"></i>
                        </span>
                    </div>
                </div>

                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <button type="submit" class="btn btn-success w-100 py-2">
                    Set New Password
                </button>
            </form>

        </div>
    </div>

</body>

<script src="{{ asset('bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js') }}"></script>

<script>
    function togglePassword(inputId, el) {
        const input = document.getElementById(inputId);
        const icon = el.querySelector('i');

        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }
</script>

</html>
