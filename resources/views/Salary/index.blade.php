@extends('layout.dashboard')
@section('header', 'Slip Gaji')
@section('content')
    <div class="card shadow mb-4">
        @if (in_array($userRole, ['hr', 'owner']))
            <div class="card-header bg-danger text-white">
                <i class="fa-solid fa-plus-circle me-2"></i> Tambah Slip Gaji
            </div>
            @if (session('success'))
                <span class="alert alert-success">{{ session('success') }}</span>
            @endif
            <div class="card-body">
                <div class="d-flex gap-2">
                    <div class="col-md-1 d-grid align-self-end">
                        <a href="{{ route('salary.create') }}" type="button" class="btn btn-primary">
                            <i class="fa-solid fa-plus"></i> Tambah
                        </a>
                    </div>
                    <div class="col-md-1 d-grid align-self-end">
                        <a href="{{ route('salary.generate') }}" type="button" class="btn btn-primary">
                            <i class="fa-solid fa-plus"></i> Generate Gaji Bulanan
                        </a>
                    </div>
                </div>
            </div>
    </div>
    @endif

    <!-- Tabel Tugas -->
    <div class="card shadow">
        <div class="card-body">
            <h5 class="card-title"><i class="fa-solid fa-list-check"></i> Daftar Tugas</h5>
            <div class="table-responsive">
                <table id="salaryTable" class="table table-bordered">
                    <thead>
                        @if (in_array($userRole, ['hr', 'owner']))
                            <tr>
                                <th>Nama Karyawan</th>
                                <th>Divisi</th>
                                <th>Gaji</th>
                                <th>Potongan</th>
                                {{-- <th>Bonus</th> --}}
                                <th>Total</th>
                                <th>Aksi</th>
                            </tr>
                        @else
                            <tr>
                                <th>Nama Karyawan</th>
                                <th>Divisi</th>
                                <th>Gaji</th>
                                <th>Potongan</th>
                                {{-- <th>Bonus</th> --}}
                                <th>Total</th>
                            </tr>
                        @endif
                    </thead>
                    <tbody>
                        @foreach ($salaries as $salary)
                            @if (in_array($userRole, ['hr', 'owner']))
                                <tr>
                                    <td>{{ ucwords($salary->employee->fullname) }}</td>
                                    <td>{{ strtoupper($salary->employee->division->name) }}</td>
                                    <td>Rp. {{ number_format($salary->net_salary) }}</td>
                                    <td>Rp. {{ number_format($salary->cuts) }}</td>
                                    {{-- <td>Rp. {{ number_format($salary->bonus) }}</td> --}}
                                    <td>Rp. {{ number_format($salary->total) }}</td>
                                    <td>
                                        <a href="{{ route('salary.edit', $salary->id) }}"
                                            class="btn btn-info btn-sm text-white"><i class="fa-solid fa-eye"></i></a>
                                        <a href="{{ route('salary.edit', $salary->id) }}"
                                            class="btn btn-warning btn-sm text-white"><i class="fa-solid fa-pen"></i></a>
                                        <form action="{{ route('salary.destroy', $salary->id) }}" method="POST"
                                            class="d-inline" id="deleteForm{{ $salary->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger btn-sm" type="button"
                                                onclick="confirmDelete({{ $salary->id }})"><i
                                                    class="fa-solid fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @else
                                <tr>
                                    <td>{{ ucwords($salary->employee->fullname) }}</td>
                                    <td>{{ strtoupper($salary->employee->division->name) }}</td>
                                    <td>Rp. {{ number_format($salary->net_salary) }}</td>
                                    <td>Rp. {{ number_format($salary->cuts) }}</td>
                                    {{-- <td>Rp. {{ number_format($salary->bonus) }}</td> --}}
                                    <td>Rp. {{ number_format($salary->total) }}</td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- jQuery + DataTables -->
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('DataTables/datatables.min.js') }}"></script>
    <script>
        let table = new DataTable('#salaryTable', {
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                paginate: {
                    previous: "Sebelumnya",
                    next: "Berikutnya",
                },
            },
            @if (in_array($userRole, ['hr', 'owner']))
                columnDefs: [{
                    targets: 5,
                    orderable: false,
                    searchable: false
                }]
            @endif
        });
    </script>
@endsection
