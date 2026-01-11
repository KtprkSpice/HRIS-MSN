@extends('layout.dashboard')

@section('header', 'QR Presensi')

@section('content')

    <div class="container text-center">
        <h2 class="mb-4">QR Presensi - {{ $task->name }}</h2>

        <div class="row justify-content-center">

            {{-- CHECK-IN --}}
            <div class="col-md-5 mb-4">
                <div class="card p-4 shadow">
                    <h4 class="text-primary mb-3">
                        <i class="fa-solid fa-right-to-bracket"></i> QR Check-in
                    </h4>

                    @if ($qrCheckin)
                        <img src="data:image/png;base64,{{ base64_encode(
                            new \Endroid\QrCode\Writer\PngWriter()->write(new \Endroid\QrCode\QrCode($qrCheckin->token))->getString(),
                        ) }}"
                            class="img-fluid mb-3" style="max-width:260px">

                        <p class="text-muted">
                            Berlaku sampai: {{ $qrCheckin->expires_at }}
                        </p>
                    @else
                        <p class="text-danger">QR Check-in belum tersedia</p>
                    @endif
                </div>
            </div>

            {{-- CHECK-OUT --}}
            <div class="col-md-5 mb-4">
                <div class="card p-4 shadow">
                    <h4 class="text-success mb-3">
                        <i class="fa-solid fa-right-from-bracket"></i> QR Check-out
                    </h4>

                    @if ($qrCheckout)
                        <img src="data:image/png;base64,{{ base64_encode(
                            new \Endroid\QrCode\Writer\PngWriter()->write(new \Endroid\QrCode\QrCode($qrCheckout->token))->getString(),
                        ) }}"
                            class="img-fluid mb-3" style="max-width:260px">

                        <p class="text-muted">
                            Berlaku sampai: {{ $qrCheckout->expires_at }}
                        </p>
                    @else
                        <p class="text-danger">QR Check-out belum tersedia</p>
                    @endif
                </div>
            </div>

        </div>
    </div>

@endsection
