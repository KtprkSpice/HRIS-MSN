<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Dashboard</title>
    {{-- Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    {{-- Hot Reload --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: url('build/assets/img/background.jpg') no-repeat center center/cover;
            position: relative;
            overflow: hidden;
        }


        /* Overlay semi-transparan */
        body::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            z-index: 0;
        }

        /* Animasi fade-in */
        @keyframes fadeInUp {
            0% {
                opacity: 0;
                transform: translateY(30px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-box {
            position: relative;
            z-index: 1;
            background: #fff;
            padding: 50px 30px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
            width: 380px;
            text-align: center;
            animation: fadeInUp 1s ease forwards;
        }

        /* Logo perusahaan */
        .login-box .logo {
            margin-bottom: 20px;
        }

        .login-box .logo img {
            width: 80px;
            height: auto;
            display: block;
            margin: 0 auto;
        }

        /* Judul */
        .login-box h2 {
            margin-bottom: 30px;
            color: #34495e;
            font-size: 1.9rem;
            font-weight: 600;
        }

        /* Input & Button */
        .login-box input {
            width: 100%;
            padding: 14px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            font-size: 1rem;
            transition: 0.3s;
        }

        .login-box input:focus {
            border-color: #1abc9c;
            box-shadow: 0 0 8px rgba(26, 188, 156, 0.3);
            outline: none;
        }

        .login-box button {
            width: 100%;
            padding: 14px;
            background: #1abc9c;
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            cursor: pointer;
            transition: 0.3s;
        }

        .login-box button:hover {
            background: #16a085;
            box-shadow: 0 5px 15px rgba(22, 160, 133, 0.3);
        }

        /* Error message */
        .login-box .error {
            margin-top: 15px;
            color: #e74c3c;
            font-weight: 600;
            font-size: 0.95rem;
        }

        /* Responsive */
        @media(max-width:420px) {
            .login-box {
                width: 90%;
                padding: 40px 20px;
            }

            .login-box h2 {
                font-size: 1.6rem;
            }
        }
    </style>
</head>

<body>
    <div class="login-box">
        <div class="logo">
            <img src="{{ asset('build/assets/img/logo.png') }}" alt="Logo Perusahaan">
        </div>
        <h2>Login Dashboard</h2>
        <form method="post" action="{{ route('login') }}">
            @csrf
            <input type="text" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
    </div>
</body>

</html>
