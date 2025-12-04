@extends('layout.dashboard')

@section('header', 'QR Presensi')

@section('content')

<div class="container text-center">
    <h2>QR Presensi - {{ $task->name }}</h2>

    <div class="card p-4 shadow flex items-center">
        <img 
            src="data:image/png;base64,{{ $qrBase64 }}" 
            alt="QR Code" 
            class="img-fluid"
            style="max-width:320px"
        >

        <p class="mt-3">
            Berlaku sampai: {{ $qr->expires_at }}
        </p>
    </div>
</div>

@endsection
