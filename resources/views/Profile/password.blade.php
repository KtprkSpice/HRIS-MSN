<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ganti Password | Modern System</title>

    {{-- Bootstrap --}}
    <link href="{{ asset('bootstrap-5.3.8-dist/css/bootstrap.min.css') }}" rel="stylesheet">
    {{-- Font Awesome --}}
    <link rel="stylesheet" href="{{ asset('fontawesome-free-7.1.0-web/css/all.min.css') }}">
    {{-- Font --}}
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),
                        url('{{ asset('build/assets/img/bg login.png') }}') center/cover no-repeat fixed;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.48);
            backdrop-filter: blur(15px) saturate(180%);
            -webkit-backdrop-filter: blur(15px) saturate(180%);
            border-radius: 28px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 420px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            overflow: hidden;
        }

        .card-body { padding: 2.5rem !important; }

        .page-title {
            color: #ffffff;
            font-weight: 700;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

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
        }

        .input-group-text {
            background-color: rgba(255, 255, 255, 0.9);
            border: none;
            border-radius: 0 12px 12px 0;
            color: #6b7280;
            cursor: pointer;
        }

        .btn-submit {
            background: linear-gradient(135deg, #bc5e6b 0%, #8e444f 100%);
            border: none;
            border-radius: 12px;
            padding: 14px;
            font-weight: 700;
            color: white;
            text-transform: uppercase;
            box-shadow: 0 10px 20px rgba(188, 94, 107, 0.3);
            transition: all 0.3s ease;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(188, 94, 107, 0.4);
        }

        .btn-back {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.85rem;
            text-decoration: none;
            transition: color 0.2s;
            display: inline-block;
        }

        .btn-back:hover {
            color: #ffffff;
        }

        .glass-hr { border-top: 1px solid rgba(255, 255, 255, 0.2); opacity: 1; }
    </style>
</head>

<body>

    <div class="glass-card">
        <div class="card-body">

            <div class="text-center mb-4">
                <img src="{{ asset('build/assets/img/logo.png') }}" alt="Logo" style="width: 75px;">
            </div>

            <div class="text-center mb-4">
                <h4 class="page-title mb-1">Update Password</h4>
                <p class="text-white-50 small">Amankan akun Anda dengan password baru</p>
            </div>

            <form method="POST" action="{{ route('user-password.update') }}" autocomplete="off">
                @csrf
                @method('PUT')

                {{-- Password Saat Ini --}}
                <div class="mb-3">
                    <label class="form-label"><i class="fa-solid fa-key"></i> Password Saat Ini</label>
                    <div class="input-group">
                        <input type="password" name="current_password" id="current_password" 
                               class="form-control" placeholder="" required autocomplete="one-time-code">
                        <span class="input-group-text" onclick="togglePassword('current_password', this)">
                            <i class="fa-solid fa-eye"></i>
                        </span>
                    </div>
                </div>

                <hr class="my-4 glass-hr">

                {{-- Password Baru --}}
                <div class="mb-3">
                    <label class="form-label"><i class="fa-solid fa-lock"></i> Password Baru</label>
                    <div class="input-group">
                        <input type="password" name="password" id="password" 
                               class="form-control" placeholder="" required 
                               autocomplete="new-password">
                        <span class="input-group-text" onclick="togglePassword('password', this)">
                            <i class="fa-solid fa-eye"></i>
                        </span>
                    </div>
                </div>

                {{-- Konfirmasi Password --}}
                <div class="mb-4">
                    <label class="form-label"><i class="fa-solid fa-shield-check"></i> Konfirmasi Password</label>
                    <div class="input-group">
                        <input type="password" name="password_confirmation" id="password_confirmation"
                               class="form-control" placeholder="" required 
                               autocomplete="new-password">
                        <span class="input-group-text" onclick="togglePassword('password_confirmation', this)">
                            <i class="fa-solid fa-eye"></i>
                        </span>
                    </div>
                </div>

                <button type="submit" class="btn btn-submit w-100 mb-3">
                    Update Password <i class="fa-solid fa-paper-plane ms-2"></i>
                </button>
            </form>

            {{-- Button Kembali --}}
            <div class="text-center mt-3">
                <a href="{{ url()->previous() }}" class="btn-back">
                    <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Profil
                </a>
            </div>

        </div>
    </div>

    <script src="{{ asset('bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js') }}"></script>
    <script>
        function togglePassword(inputId, el) {
            const input = document.getElementById(inputId);
            const icon = el.querySelector('i');

            if (input && icon) {
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.replace('fa-eye', 'fa-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.replace('fa-eye-slash', 'fa-eye');
                }
            }
        }

        // Paksa semua kolom kosong saat halaman dimuat untuk menghilangkan ilusi titik sandi
        window.addEventListener('load', function() {
            setTimeout(function() {
                document.getElementById('current_password').value = '';
                document.getElementById('password').value = '';
                document.getElementById('password_confirmation').value = '';
            }, 10);
        });
    </script>
</body>
</html>