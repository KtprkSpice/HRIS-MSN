<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <!-- Bootstrap 5 -->
    <link href="{{ asset('bootstrap-5.3.8-dist/css/bootstrap.min.css') }}" rel="stylesheet">

    <title>Document</title>
</head>

<style>
    body {
        background: linear-gradient(135deg, #ff6f61, #d6a4a4);
        color: #fff;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
        text-align: center;
    }

    .error-container {
        background: rgba(0, 0, 0, 0.5);
        border-radius: 10px;
        padding: 3rem;
        max-width: 600px;
        margin: auto;
    }

    .error-title {
        font-size: 6rem;
        font-weight: bold;
        margin-bottom: 1rem;
    }

    .error-message {
        font-size: 1.5rem;
        margin-bottom: 2rem;
    }

    .btn-primary {
        background-color: #ff6f61;
        border-color: #ff6f61;
    }

    .btn-primary:hover {
        background-color: #d9534f;
        border-color: #d9534f;
    }
</style>

<body>
    <div class="error-container">
        <div class="error-title">{{ $exception->getStatusCode() }}</div>
        <div class="error-message">{{ $exception->getMessage() }}</div>
        <a href="{{ route('dashboard.index') }}" class="btn btn-primary btn-lg">Kembali Ke Dashboard</a>
    </div>

    {{-- Bootsrap --}}
    <script src="{{ asset('bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js') }}"></script>
</body>



</html>
