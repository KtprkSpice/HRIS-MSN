@extends('layout.dashboard')

@section('header', 'Scan QR')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">

                <div class="card shadow">
                    <div class="card-body text-center">

                        <h3 class="mb-3">
                            <i class="fa-solid fa-qrcode"></i> Scan QR Presensi
                        </h3>

                        <p>Tugas: <strong>{{ $task->name }}</strong></p>

                        <div id="qr-reader" style="width: 100%;"></div>

                        <div id="result" class="alert alert-info mt-3 d-none"></div>

                    </div>
                </div>


            </div>
        </div>
    </div>

    {{-- HTML5 QR CODE SCANNER --}}
    <script src="{{ asset('js/html5-qrcode.min.js') }}"></script>

    <script>
        let isSubmitting = false;

        function onScanSuccess(decodedText) {
            if (isSubmitting) {
                return;
            }

            // tampilkan hasil
            let resultBox = document.getElementById('result');
            resultBox.classList.remove('d-none');
            resultBox.className = 'alert alert-info mt-3';
            resultBox.innerHTML = 'QR terbaca. Mengambil lokasi GPS...';

            if (!navigator.geolocation) {
                resultBox.className = 'alert alert-danger mt-3';
                resultBox.innerHTML = 'Browser ini tidak mendukung GPS.';
                return;
            }

            isSubmitting = true;

            navigator.geolocation.getCurrentPosition(function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;

                fetch("{{ route('presences.storeQr') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        task_id: "{{ $task->id }}",
                        qr_data: decodedText,
                        latitude: lat,
                        longitude: lng,
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === "success") {
                        resultBox.className = 'alert alert-success mt-3';
                        resultBox.innerHTML = data.message;

                        // Stop camera
                        html5QrcodeScanner.clear();
                    } else {
                        resultBox.className = 'alert alert-danger mt-3';
                        resultBox.innerHTML = data.message;
                        isSubmitting = false;
                    }
                })
                .catch(() => {
                    resultBox.className = 'alert alert-danger mt-3';
                    resultBox.innerHTML = 'Presensi gagal dikirim. Silakan coba lagi.';
                    isSubmitting = false;
                });
            }, function(error) {
                const messages = {
                    1: 'Izin lokasi ditolak. Aktifkan izin GPS di browser.',
                    2: 'Lokasi tidak tersedia. Pastikan GPS perangkat aktif.',
                    3: 'Pengambilan lokasi terlalu lama. Coba lagi.',
                };

                resultBox.className = 'alert alert-danger mt-3';
                resultBox.innerHTML = messages[error.code] || 'Lokasi GPS gagal diambil.';
                isSubmitting = false;
            }, {
                enableHighAccuracy: true,
                timeout: 15000,
                maximumAge: 0,
            });
        }

        let html5QrcodeScanner = new Html5QrcodeScanner(
            "qr-reader", {
                fps: 10,
                qrbox: 300
            },
            false
        );

        html5QrcodeScanner.render(onScanSuccess);
    </script>

@endsection
