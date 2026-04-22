@extends('layout.dashboard')
@section('header', 'Edit Divisi')

@section('content')
    <style>
        /* Mengembalikan ke tema warna awal (Maroon/Pink Tua) */
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
    </style>
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm border-0" style="border-radius: 12px;">
                <div class="card-header card-custom-header">
                    <h5 class="mb-0"><i class="fa-solid fa-circle-plus me-2"></i> Form Tambah Divisi Baru</h5>
                </div>
                <div class="card-body p-4">

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert"
                            style="border-radius: 8px;">
                            <ul class="mb-0 small">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    {{-- Form --}}
                    <form class="row g-4" action="{{ route('division.update', $division->id) }}" method="post">
                        @csrf
                        @method('PUT')
                        <div class="col-md-6">
                            <label for="name" class="form-label">
                                <i class="fa-solid fa-sitemap me-2 accent-icon"></i> Nama Divisi
                            </label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" placeholder="Masukkan nama divisi..."
                                value="{{ old('name', $division->name) }}" required>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="description" class="form-label">
                                <i class="fa-solid fa-align-left me-2 accent-icon"></i> Deskripsi
                            </label>
                            <input type="text" class="form-control @error('description') is-invalid @enderror"
                                name="description" id="description" placeholder="Keterangan singkat..."
                                value="{{ old('description', $division->description) }}">
                            @error('description')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-12 mt-4">
                            <hr class="opacity-50">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ url()->previous() }}" class="btn btn-back shadow-sm">
                                    <i class="fa-solid fa-arrow-left me-2"></i> Batal
                                </a>
                                <button type="submit" class="btn btn-primary btn-save text-white shadow-sm">
                                    <i class="fa-solid fa-save me-2"></i> Simpan Data
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
