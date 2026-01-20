@extends('layout.dashboard')

@section('header', 'QR Presensi')

@section('content')
    <div class="container">

        <h3 class="text-center mb-4">
            QR Presensi - {{ $task->name }}
        </h3>

        @foreach ($shifts as $shift)
            @php
                $qr = $qrCodes[$shift->id] ?? collect();
                $checkin = $qr->where('type', 'check_in')->first();
                $checkout = $qr->where('type', 'check_out')->first();
            @endphp

            <div class="card mb-3">
                <div class="card-header">
                    Shift {{ $shift->name }}
                </div>

                <div class="card-body row text-center">
                    <div class="col-md-6">
                        <h6>Check-in</h6>
                        @if ($checkin)
                            <p><b>Expires At :</b> {{ $checkin->expires_at }}</p>
                            <img src="data:image/png;base64,{{ base64_encode(
                                new \Endroid\QrCode\Writer\PngWriter()->write(new \Endroid\QrCode\QrCode($checkin->token))->getString(),
                            ) }}"
                                width="180">
                        @else
                            <span class="text-danger">Belum ada QR</span>
                        @endif
                    </div>

                    <div class="col-md-6">
                        <h6>Check-out</h6>
                        @if ($checkout)
                            <p><b>Expires At :</b> {{ $checkin->expires_at }}</p>
                            <img src="data:image/png;base64,{{ base64_encode(
                                new \Endroid\QrCode\Writer\PngWriter()->write(new \Endroid\QrCode\QrCode($checkout->token))->getString(),
                            ) }}"
                                width="180">
                        @else
                            <span class="text-danger">Belum ada QR</span>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
