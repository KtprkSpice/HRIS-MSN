@extends('layout.dashboard')
@section('header', 'Pengajuan Cuti')

@section('content')

    <style>
        /* Mengoptimalkan ruang tabel agar 9 kolom tetap muat */
        .table {
            font-size: 0.88rem;
            vertical-align: middle;
        }

        #leaveTable thead th {
            background: linear-gradient(180deg, #bc5e6b 0%, #a34a57 100%);
            color: white;
            border: none;
            white-space: nowrap;
            padding: 12px 8px;
            text-align: center;
        }

        #leaveTable tbody td {
            padding: 10px 8px;
            white-space: nowrap;
        }

        /* Badge khusus untuk Jenis Cuti */
        .badge-jenis {
            padding: 4px 10px;
            border-radius: 50px;
            font-weight: 500;
            font-size: 0.75rem;
        }

        /* Penyesuaian khusus kolom Nama */
        .col-nama {
            min-width: 150px;
            white-space: normal !important;
        }

        /* Badge Status */
        .badge {
            padding: 5px 8px;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 4px;
            display: inline-block;
            min-width: 75px;
        }

        /* Desain Tombol Aksi agar seragam */
        .btn-action {
            width: 28px;
            height: 28px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            font-size: 0.75rem;
            border: none;
        }

        /* Custom Scrollbar */
        .table-responsive::-webkit-scrollbar {
            height: 6px;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background: #bc5e6b;
            border-radius: 10px;
        }

        @media (max-width: 1200px) {
            .table {
                font-size: 0.8rem;
            }

            .badge {
                min-width: 60px;
                padding: 4px 6px;
            }
        }
    </style>

    <div class="card shadow-sm border-0 rounded-4 mb-4">

    <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-3">

        <h5 class="mb-0 fw-semibold d-flex align-items-center">
            <i class="fa-solid fa-calendar-check me-2"></i>
            Manajemen Cuti
        </h5>

        @if (in_array($userRole, ['hr', 'employee']))
            <a href="{{ route('leave-request.create') }}" class="btn btn-primary rounded-pill px-4">
    <i class="fa-solid fa-plus me-2"></i> Ajukan Cuti
</a>
        @endif

    </div>

    @if (session('success'))
    <div class="card-body pt-0">
        <div class="alert alert-success alert-dismissible fade show rounded-3 mt-2" role="alert">
            <i class="fa-solid fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
    @endif

</div>

    <div class="card shadow border-0">
        <div class="card-body p-3">
            <h5 class="card-title fw-bold mb-3 text-secondary">
                <i class="fa-solid fa-list-ul me-2" style="color: #bc5e6b;"></i> Daftar Pengajuan
            </h5>

            <div class="table-responsive">
                <table id="leaveTable" class="table table-hover border">
                    <thead>
                        <tr>
                            <th class="text-start">Nama</th>
                            <th>Jenis Cuti</th>
                            <th>Mulai</th>
                            <th>Selesai</th>
                            <th>Status HR</th>
                            <th>Status Owner</th>
                            <th>Status Final</th>
                            <th>Dokumen</th>
                            @if (in_array($userRole, ['hr', 'owner']))
                                <th>Aksi</th>
                            @endif
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($leaveRequests as $leave)
                            @php
                                $hrApproval = $leave->approvals->where('approval_order', 1)->first();
                                $ownerApproval = $leave->approvals->where('approval_order', 2)->first();
                                $finalStatus = $leave->status;
                                $jenisCuti = ucwords($leave->types->name);
                            @endphp

                            <tr class="text-center">
                                <td class="text-start col-nama fw-bold text-dark">{{ ucwords($leave->employee->fullname) }}
                                </td>

                                <td>
                                    @if ($jenisCuti == 'Izin Pribadi')
                                        <span class="badge-jenis shadow-sm"
                                            style="background-color: #e3f2fd; color: #0d47a1; border: 1px solid #bbdefb;">
                                            {{ $jenisCuti }}
                                        </span>
                                    @else
                                        <span class="badge-jenis shadow-sm"
                                            style="background-color: #f5f5f5; color: #616161; border: 1px solid #e0e0e0;">
                                            {{ $jenisCuti }}
                                        </span>
                                    @endif
                                </td>

                                <td>{{ \Carbon\Carbon::parse($leave->start_date)->format('d M Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($leave->end_date)->format('d M Y') }}</td>

                                <td>
                                    @if ($userRole === 'hr' && $leave->employee->user_id === auth()->id())
                                        <span class="text-muted small">-</span>
                                    @else
                                        <span
                                            class="badge {{ $hrApproval?->status === 'approved' ? 'bg-success' : ($hrApproval?->status === 'rejected' ? 'bg-danger' : 'bg-warning text-dark') }}">
                                            {{ ucfirst($hrApproval?->status ?? 'pending') }}
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    <span
                                        class="badge {{ $ownerApproval?->status === 'approved' ? 'bg-success' : ($ownerApproval?->status === 'rejected' ? 'bg-danger' : 'bg-warning text-dark') }}">
                                        {{ ucfirst($ownerApproval?->status ?? 'pending') }}
                                    </span>
                                </td>

                                <td>
                                    <span
                                        class="badge {{ $finalStatus === 'approved' ? 'bg-info' : ($finalStatus === 'rejected' ? 'bg-danger' : 'bg-warning text-dark') }}">
                                        {{ ucfirst($finalStatus ?? 'pending') }}
                                    </span>
                                </td>

                                <td>
                                    @if ($leave->document_file)
                                        <a class="btn btn-sm p-1 px-2 text-white shadow-sm"
                                            style="background-color: #bc5e6b;"
                                            href="{{ asset('storage/' . $leave->document_file) }}" target="_blank">
                                            <i class="fa-solid fa-file-pdf"></i>
                                        </a>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>

                                @if (in_array($userRole, ['hr', 'owner']))
                                <td>
                                    @if ($leave->status === 'pending')
                                        @if (($userRole === 'hr' && $leave->current_step == 1 && $leave->employee->user_id !== auth()->id()) || ($userRole === 'owner' && $leave->current_step == 2))
                                            
                                            {{-- Tombol Approve --}}
                                            <form action="{{ route('leave-request.approve', $leave->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-action shadow-sm" title="Setujui">
                                                    <i class="fa-solid fa-check"></i>
                                                </button>
                                            </form>

                                            {{-- Tombol Reject (Visual) --}}
                                            <button type="button" class="btn btn-danger btn-action shadow-sm ms-1" title="Tolak (Fungsi Belum Aktif)">
                                                <i class="fa-solid fa-xmark"></i>
                                            </button>

                                            {{-- Tombol Delete --}}
                                            <form action="{{ route('leave-request.destroy', $leave->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-secondary btn-action shadow-sm ms-1" title="Hapus">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </form>

                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    @else
                                        <i class="fa-solid fa-lock text-muted small"></i>
                                    @endif
                                </td>
                            @endif
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
            $('#leaveTable').DataTable({
                responsive: false,
                language: {
                    search: "Cari:",
                    lengthMenu: "_MENU_",
                    info: "_START_-_END_ dari _TOTAL_",
                    paginate: {
                        previous: "<",
                        next: ">"
                    }
                }
            });
        });
    </script>

@endsection
