@extends('layout.dashboard')
@section('header', 'Tambah Divisi')

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
            transition: all 0.3s;
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
            transition: all 0.3s;
        }

        .btn-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(188, 94, 107, 0.3);
            color: white;
        }

        .btn-back {
            background-color: #f8f9fa;
            color: #6c757d;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 0.6rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-back:hover {
            background-color: #e2e6ea;
            color: #495057;
        }

        .accent-icon {
            color: #bc5e6b;
        }

        .btn-add-position {
            background: linear-gradient(90deg, #bc5e6b 0%, #a34a57 100%);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 0.35rem 0.75rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-add-position:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(188, 94, 107, 0.3);
            color: white;
        }

        .position-item {
            margin-bottom: 1rem;
        }

        .btn-delete-position {
            width: 45px;
            height: 45px;
            border: none;
            border-radius: 10px;
            background: linear-gradient(135deg, #bc5e6b 0%, #a34a57 100%);
            color: white;
            transition: all .3s ease;
            box-shadow: 0 4px 12px rgba(188, 94, 107, .25);
        }

        .btn-delete-position:hover {
            background: linear-gradient(135deg, #c96d7a 0%, #bc5e6b 100%);
            transform: translateY(-3px);
            box-shadow: 0 8px 18px rgba(188, 94, 107, .35);
            color: white;
        }

        .btn-delete-position i {
            font-size: 14px;
        }
    </style>

    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm border-0" style="border-radius: 12px;">
                <div class="card-header card-custom-header">
                    <h5 class="mb-0">
                        <i class="fa-solid fa-circle-plus me-2"></i>
                        Form Tambah Divisi Baru
                    </h5>
                </div>

                <div class="card-body p-4">

                    <form class="row g-4" action="{{ route('division.store') }}" method="post">
                        @csrf

                        <!-- DIVISI -->
                        <div class="col-md-6">
                            <label class="form-label">
                                <i class="fa-solid fa-sitemap me-2 accent-icon"></i>
                                Nama Divisi
                            </label>
                            <input type="text" class="form-control" name="name" value="{{ old('name') }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">
                                <i class="fa-solid fa-align-left me-2 accent-icon"></i>
                                Deskripsi
                            </label>
                            <input type="text" class="form-control" name="description" value="{{ old('description') }}">
                        </div>

                        <div class="col-12 mt-4">

                            <hr class="opacity-50">

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">Data Posisi</h6>

                                <button type="button" class="btn btn-sm btn-add-position" id="add-position">
                                    <i class="fa fa-plus"></i> Tambah
                                </button>
                            </div>

                            <div id="position-wrapper">

                                <div class="row g-4 position-item align-items-end">

                                    <div class="col-md-4">
                                        <label class="form-label">
                                            <i class="fa-solid fa-briefcase me-2 accent-icon"></i>
                                            Posisi
                                        </label>
                                        <input type="text" class="form-control" name="position[]" required>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">
                                            <i class="fa-solid fa-money-bill me-2 accent-icon"></i>
                                            Gaji Pokok
                                        </label>

                                        <div class="input-group shadow-sm">
                                            <span class="input-group-text">Rp</span>

                                            <input type="text" class="form-control money-input" name="base_salary[]"
                                                required>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">
                                            <i class="fa-solid fa-clock me-2 accent-icon"></i>
                                            Potongan / Menit
                                        </label>

                                        <div class="input-group shadow-sm">
                                            <span class="input-group-text">Rp</span>

                                            <input type="text" class="form-control money-input"
                                                name="deduction_per_minute[]" required>
                                        </div>
                                    </div>

                                    <div class="col-md-1">
                                        <button type="button" class="btn-delete-position delete-position"
                                            title="Hapus Posisi">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </div>

                                </div>

                            </div>

                            <hr class="opacity-50">

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ url()->previous() }}" class="btn btn-back shadow-sm">
                                    <i class="fa-solid fa-arrow-left me-2"></i>
                                    Batal
                                </a>

                                <button type="submit" class="btn btn-primary btn-save text-white shadow-sm">
                                    <i class="fa-solid fa-save me-2"></i>
                                    Simpan Data
                                </button>
                            </div>

                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('add-position').addEventListener('click', function() {

            let wrapper = document.getElementById('position-wrapper');
            let firstItem = wrapper.querySelector('.position-item');

            let clone = firstItem.cloneNode(true);

            clone.querySelectorAll('input').forEach(input => {
                input.value = '';
            });

            wrapper.appendChild(clone);
        });

        document.addEventListener('click', function(e) {

            let deleteButton = e.target.closest('.delete-position');

            if (!deleteButton) return;

            let items = document.querySelectorAll('.position-item');

            if (items.length <= 1) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Tidak Bisa Dihapus',
                    text: 'Minimal harus ada 1 data posisi.',
                    confirmButtonColor: '#bc5e6b'
                });
                return;
            }

            Swal.fire({
                title: 'Hapus Posisi?',
                text: 'Data posisi ini akan dihapus dari form.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {

                if (result.isConfirmed) {

                    deleteButton.closest('.position-item').remove();

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Data posisi berhasil dihapus.',
                        timer: 1500,
                        showConfirmButton: false
                    });

                }

            });

        });

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
    </script>
@endsection
