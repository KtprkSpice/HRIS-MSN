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
    function onScanSuccess(decodedText) {
        // tampilkan hasil
        let resultBox = document.getElementById('result');
        resultBox.classList.remove('d-none');
        resultBox.innerHTML = "QR terbaca: <strong>" + decodedText + "</strong>";

        // Kirim ke route presensi
        fetch("{{ route('presences.storeQr') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({
                task_id: "{{ $task->id }}",
                qr_data: decodedText,
            })
        })
        .then(response => response.json())
        .then(data => {
            if(data.status === "success"){
                resultBox.classList.remove('alert-info');
                resultBox.classList.add('alert-success');
                resultBox.innerHTML = data.message;

                // Stop camera
                html5QrcodeScanner.clear();
            } else {
                resultBox.classList.remove('alert-info');
                resultBox.classList.add('alert-danger');
                resultBox.innerHTML = data.message;
            }
        });
    }

    let html5QrcodeScanner = new Html5QrcodeScanner(
        "qr-reader",
        { fps: 10, qrbox: 250 },
        false
    );

    html5QrcodeScanner.render(onScanSuccess);
</script>

@endsection