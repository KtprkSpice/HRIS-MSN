@extends('layout.dashboard')

@section('header', 'QR Presensi')

@section('content')
    <div class="container">

        <h3 class="text-center mb-4">
            QR Presensi - {{ $task->name }}
        </h3>

        <p class="text-center text-muted mb-4">
            QR ini berlaku untuk <b>SEMUA SHIFT</b> Pada <b>{{ Carbon\Carbon::parse($qr->date)->format('d F Y') }}</b>
        </p>

        <div class="row justify-content-center">

            {{-- CHECK IN --}}
            <div class="col-md-5">
                <div class="card mb-3">
                    <div class="card-header text-center">
                        Check-in
                    </div>

                    <div class="card-body text-center">
                        @if (isset($qrCodes['check_in']))
                            <p>
                                <b>Expires At :</b>
                                {{ $qrCodes['check_in']->expires_at }}
                            </p>

                            <img src="data:image/png;base64,{{ base64_encode(
                                new \Endroid\QrCode\Writer\PngWriter()->write(new \Endroid\QrCode\QrCode($qrCodes['check_in']->token))->getString(),
                            ) }}"
                                width="200">
                        @else
                            <span class="text-danger">QR Check-in belum dibuat</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- CHECK OUT --}}
            <div class="col-md-5">
                <div class="card mb-3">
                    <div class="card-header text-center">
                        Check-out
                    </div>

                    <div class="card-body text-center">
                        @if (isset($qrCodes['check_out']))
                            <p>
                                <b>Expires At :</b>
                                {{ $qrCodes['check_out']->expires_at }}
                            </p>

                            <img src="data:image/png;base64,{{ base64_encode(
                                new \Endroid\QrCode\Writer\PngWriter()->write(new \Endroid\QrCode\QrCode($qrCodes['check_out']->token))->getString(),
                            ) }}"
                                width="200">
                        @else
                            <span class="text-danger">QR Check-out belum dibuat</span>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
