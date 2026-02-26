@extends('layout.dashboard')
@section('header', 'Pengajuan Cuti')

@section('content')

    <style>
        /* Responsive table adjustments */
        @media (max-width: 576px) {
            .card-header {
                padding: 10px 12px;
                font-size: 0.95rem;
            }

            .btn {
                padding: 6px 12px;
                font-size: 0.9rem;
            }

            table {
                font-size: 0.8rem;
            }

            table th,
            table td {
                padding: 6px 4px !important;
            }

            .table-responsive {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
        }

        @media (max-width: 992px) and (min-width: 577px) {
            table {
                font-size: 0.9rem;
            }

            table th,
            table td {
                padding: 8px 6px !important;
            }
        }
    </style>

    <div class="card shadow mb-4">
        <div class="card-header text-white" style="background-color: #aa2c36;">
            <i class="fa-solid fa-plus-circle me-2"></i> Tambah Cuti Manual
        </div>

        @if (session('success'))
            <span class="alert alert-success d-block m-3">{{ session('success') }}</span>
        @endif

        <div class="card-body">
            @if (in_array($userRole, ['hr', 'employee']))
                <a href="{{ route('leave-request.create') }}" class="btn text-white" style="background-color: #215cda;">
                    <i class="fa-solid fa-plus"></i> Ajukan Cuti
                </a>
            @endif
        </div>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <h5 class="card-title">
                <i class="fa-solid fa-list-check"></i> Daftar Pengajuan Cuti
            </h5>

            <div class="table-responsive">
                <table id="leaveTable" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Jenis Cuti</th>
                            <th>Mulai</th>
                            <th>Selesai</th>
                            <th>Status HR</th>
                            <th>Status Owner</th>
                            <th>Status Final</th>
                            @if (in_array($userRole, ['hr', 'owner']))
                                <th>Aksi</th>
                            @endif
                            <th>Dokumen</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($leaveRequests as $leave)

                            @php
                                $hrApproval = $leave->approvals->where('approval_order', 1)->first();
                                $ownerApproval = $leave->approvals->where('approval_order', 2)->first();
                            @endphp

                            <tr>
                                <td>{{ ucwords($leave->employee->fullname) }}</td>
                                <td>{{ ucwords($leave->types->name) }}</td>
                                <td>{{ \Carbon\Carbon::parse($leave->start_date)->format('d F Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($leave->end_date)->format('d F Y') }}</td>

                                {{-- STATUS HR --}}
                                <<td>
                                    @if ($userRole === 'hr' && $leave->employee->user_id === auth()->id())
                                        -
                                    @else
                                        <span
                                            class="badge 
                                            {{ $hrApproval?->status === 'approved'
                                                ? 'bg-success'
                                                : ($hrApproval?->status === 'rejected'
                                                    ? 'bg-danger'
                                                    : 'bg-warning') }}">
                                            {{ ucfirst($hrApproval?->status ?? 'pending') }}
                                        </span>
                                    @endif
                                    </td>

                                    {{-- STATUS OWNER --}}
                                    <td>
                                        <span
                                            class="badge 
                                    {{ $ownerApproval?->status === 'approved'
                                        ? 'bg-success'
                                        : ($ownerApproval?->status === 'rejected'
                                            ? 'bg-danger'
                                            : 'bg-warning') }}">
                                            {{ ucfirst($ownerApproval?->status ?? 'pending') }}
                                        </span>
                                    </td>

                                    {{-- STATUS FINAL --}}
                                    <td>
                                        @php $finalStatus = $leave->status; @endphp
                                        <span class="badge"
                                            style="{{ $finalStatus === 'confirmed' ? 'background-color: #aa2c36; color: #fff;' : ($finalStatus === 'rejected' ? 'background-color: #dc3545; color: #fff;' : 'background-color: #ffc107; color: #212529;') }}">
                                            {{ ucfirst($finalStatus) }}
                                        </span>
                                    </td>

                                    {{-- AKSI --}}
                                    @if (in_array($userRole, ['hr', 'owner']))
                                        <td>

                                            @if ($leave->status === 'pending')
                                                {{-- HR STEP --}}
                                                @if ($userRole === 'hr' && $leave->current_step == 1)
                                                    <form action="{{ route('leave-request.approve', $leave->id) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        <button class="btn btn-success btn-sm">Approve</button>
                                                    </form>

                                                    <form action="{{ route('leave-request.rejected', $leave->id) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        <button class="btn btn-danger btn-sm">Reject</button>
                                                    </form>
                                                @endif

                                                {{-- OWNER STEP --}}
                                                @if ($userRole === 'owner' && $leave->current_step == 2)
                                                    <form action="{{ route('leave-request.approve', $leave->id) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        <button class="btn btn-success btn-sm">Approve</button>
                                                    </form>

                                                    <form action="{{ route('leave-request.rejected', $leave->id) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        <button class="btn btn-danger btn-sm">Reject</button>
                                                    </form>
                                                @endif
                                            @endif

                                        </td>
                                    @endif

                                    {{-- DOKUMEN --}}
                                    <td>
                                        @if ($leave->document_file)
                                            <a class="btn btn-sm text-white" style="background-color: #aa2c36;"
                                                href="{{ Storage::url($leave->document_file) }}" target="_blank">
                                                <i class="fa-solid fa-file"></i>
                                            </a>
                                        @endif
                                    </td>

                            </tr>

                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('DataTables/datatables.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            new DataTable('#leaveTable', {
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                    paginate: {
                        previous: "Sebelumnya",
                        next: "Berikutnya",
                    },
                },
                @if ($userRole === 'employee')
                    columnDefs: [{
                        targets: 7,
                        orderable: false,
                        searchable: false
                    }]
                @endif
            });
        });
    </script>

@endsection
