@extends('layout.dashboard')
@section('header', 'Edit Divisi')

@section('content')
    <style>
        .card-custom-header {
            background: linear-gradient(90deg, #bc5e6b 0%, #a34a57 100%);
            color: white;
            font-weight: bold;
            padding: 1.2rem;
            border-radius: 12px 12px 0 0 !important;
            border: none;
        }

        .form-label {
            font-weight: 600;
            color: #495057;
            font-size: 0.9rem;
        }

        .form-control {
            border-radius: 8px;
            padding: 0.6rem 1rem;
            border: 1px solid #dee2e6;
        }

        .form-control:focus {
            border-color: #bc5e6b;
            box-shadow: 0 0 0 0.25rem rgba(188, 94, 107, 0.15);
        }

        .btn-save {
            background: linear-gradient(90deg, #bc5e6b 0%, #a34a57 100%);
            border: none;
            border-radius: 8px;
            padding: 0.6rem 1.5rem;
            font-weight: 600;
        }

        .btn-back {
            background-color: #f8f9fa;
            color: #6c757d;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 0.6rem 1.5rem;
            font-weight: 600;
        }

        .btn-add-position {
            background: linear-gradient(90deg, #bc5e6b 0%, #a34a57 100%);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 0.35rem 0.75rem;
            font-weight: 600;
        }

        .accent-icon {
            color: #bc5e6b;
        }

        .position-item {
            margin-bottom: 1rem;
        }

        .input-group {
            border-radius: 8px;
            overflow: hidden;
        }

        .btn-delete-position {
            background: linear-gradient(90deg, #bc5e6b 0%, #a34a57 100%);
            color: white;
            border: none;
            border-radius: 8px;
            width: 42px;
            height: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-delete-position:hover {
            opacity: .9;
            color: white;
        }
    </style>

    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm border-0" style="border-radius: 12px;">
                <div class="card-header card-custom-header">
                    <h5 class="mb-0">
                        <i class="fa-solid fa-pen-to-square me-2"></i> Form Edit Divisi
                    </h5>
                </div>

                <div class="card-body p-4">

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0 small">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form class="row g-4" action="{{ route('division.update', $division->id) }}" method="post">
                        @csrf
                        @method('PUT')

                        {{-- DIVISI --}}
                        <div class="col-md-6">
                            <label class="form-label">
                                <i class="fa-solid fa-sitemap me-2 accent-icon"></i> Nama Divisi
                            </label>
                            <input type="text" class="form-control" name="name"
                                value="{{ old('name', $division->name) }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">
                                <i class="fa-solid fa-align-left me-2 accent-icon"></i> Deskripsi
                            </label>
                            <input type="text" class="form-control" name="description"
                                value="{{ old('description', $division->description) }}">
                        </div>

                        {{-- POSISI --}}
                        <div class="col-12 mt-4">
                            <hr class="opacity-50">

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">Data Posisi</h6>

                                <button type="button" class="btn btn-sm btn-add-position" id="add-position">
                                    <i class="fa fa-plus"></i> Tambah
                                </button>
                            </div>

                            <div id="position-wrapper">

                                {{-- FIX ERROR: kalau null jadi [] --}}
                                @forelse(($division->positions ?? []) as $pos)
                                    <div class="row g-4 position-item align-items-end">
                                        <input type="hidden" name="position_ids[]" value="{{ $pos->id }}">

                                        <div class="col-md-4">
                                            <label class="form-label">Posisi</label>
                                            <input type="text" class="form-control" name="position[]"
                                                value="{{ $pos->name }}" required>
                                        </div>

                                        <div class="col-md-3">
                                            <label class="form-label">Gaji Pokok</label>
                                            <div class="input-group">
                                                <span class="input-group-text">Rp</span>
                                                <input type="text" class="form-control money-input" name="base_salary[]"
                                                    value="{{ number_format($pos->base_salary, 0, ',', '.') }}" required>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label">Potongan / Menit</label>
                                            <div class="input-group">
                                                <span class="input-group-text">Rp</span>
                                                <input type="text" class="form-control money-input"
                                                    name="deduction_per_minute[]"
                                                    value="{{ number_format($pos->cut_per_minute ?? ($pos->deduction_per_minute ?? 0), 0, ',', '.') }}"
                                                    required>
                                            </div>
                                        </div>

                                        <div class="col-md-1 d-flex align-items-end justify-content-end">
                                            <button type="button" class="btn-delete-position delete-position"
                                                title="Hapus Posisi">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                @empty
                                    {{-- kalau kosong tetap tampil 1 form --}}
                                    <div class="row g-4 position-item align-items-end">
                                        <div class="col-md-4">
                                            <label class="form-label">Posisi</label>
                                            <input type="text" class="form-control" name="position[]" required>
                                        </div>

                                        <div class="col-md-3">
                                            <label class="form-label">Gaji Pokok</label>
                                            <div class="input-group">
                                                <span class="input-group-text">Rp</span>
                                                <input type="text" class="form-control money-input" name="base_salary[]"
                                                    required>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label">Potongan / Menit</label>
                                            <div class="input-group">
                                                <span class="input-group-text">Rp</span>
                                                <input type="text" class="form-control money-input"
                                                    name="deduction_per_minute[]" required>
                                            </div>
                                        </div>

                                        <div class="col-md-1 d-flex align-items-end justify-content-end">
                                            <button type="button" class="btn-delete-position delete-position"
                                                title="Hapus Posisi">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforelse

                            </div>

                            <hr class="opacity-50">

                            {{-- BUTTON --}}
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ url()->previous() }}" class="btn btn-back">
                                    <i class="fa-solid fa-arrow-left me-2"></i> Batal
                                </a>
                                <button type="submit" class="btn btn-primary btn-save text-white">
                                    <i class="fa-solid fa-save me-2"></i> Simpan Data
                                </button>
                            </div>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>

    {{-- SWEETALERT2 CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- SCRIPT --}}
    <script>
        // Fitur Tambah Baris
        document.getElementById('add-position').addEventListener('click', function() {
            let wrapper = document.getElementById('position-wrapper');
            let first = wrapper.querySelector('.position-item');

            let clone = first.cloneNode(true);

            // Reset semua value input di baris baru
            clone.querySelectorAll('input').forEach(i => i.value = '');

            wrapper.appendChild(clone);
        });

        // Fitur Hapus Baris dengan SweetAlert2 (Event Delegation)
        document.getElementById('position-wrapper').addEventListener('click', function(e) {
            // Memastikan target click adalah tombol delete atau icon di dalamnya
            let deleteBtn = e.target.closest('.delete-position');

            if (deleteBtn) {
                let wrapper = document.getElementById('position-wrapper');
                let totalItems = wrapper.querySelectorAll('.position-item').length;

                // Proteksi: Jangan biarkan user menghapus jika hanya sisa 1 baris
                if (totalItems <= 1) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Oops...',
                        text: 'Minimal harus ada 1 data posisi!',
                        confirmButtonColor: '#a34a57'
                    });
                    return;
                }

                // Jalankan SweetAlert2 untuk konfirmasi hapus
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Baris posisi ini akan dihapus dari form!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#a34a57',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        let row = deleteBtn.closest('.position-item');
                        row.remove();
                    }
                });
            }
        });

        // Format Rupiah
        function formatRupiah(value) {
            let number_string = value.replace(/[^,\d]/g, '').toString();
            let split = number_string.split(',');
            let sisa = split[0].length % 3;
            let rupiah = split[0].substr(0, sisa);
            let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                let separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            return rupiah;
        }

        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('money-input')) {
                e.target.value = formatRupiah(e.target.value);
            }
        });


        @if (session('error_from_controller'))
            Swal.fire({
                icon: 'error',
                title: 'Posisi Tidak Bisa Dihapus',
                html: `{!! session('error_from_controller') !!}`,
                confirmButtonText: 'Mengerti'
            });
        @endif

        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                timer: 2000,
                showConfirmButton: false
            });
        @endif
    </script>


@endsection
