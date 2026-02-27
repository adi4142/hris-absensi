@extends('layouts.admin')

@section('title', 'Monitoring Absensi')
@section('page_title', 'Real-time Monitoring Absensi')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-desktop mr-2 text-primary"></i> Status Kehadiran Karyawan
                        <span class="text-muted small ml-2">{{ \Carbon\Carbon::parse($today)->translatedFormat('l, d F Y') }}</span>
                    </h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="monitoringTable">
                            <thead class="bg-light text-muted">
                                <tr>
                                    <th class="text-center" width="50">No</th>
                                    <th>Karyawan</th>
                                    <th>Pekerjaan (Div/Jab)</th>
                                    <th class="text-center">Waktu Masuk</th>
                                    <th class="text-center">Waktu Keluar</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center" width="120">Tindakan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($employees as $employee)
                                    @php
                                        $att = $employee->attendances->first();
                                        $status = $att ? $att->status : '-';
                                        $timeIn = $att && $att->time_in ? \Carbon\Carbon::parse($att->time_in)->format('H:i') : '-';
                                        $timeOut = $att && $att->time_out ? \Carbon\Carbon::parse($att->time_out)->format('H:i') : '-';
                                        
                                        switch ($status) {
                                            case 'Present':
                                                $statusConfig = ['label' => 'HADIR', 'class' => 'badge-soft-success'];
                                                break;

                                            case 'Late':
                                                $statusConfig = ['label' => 'TERLAMBAT', 'class' => 'badge-soft-warning'];
                                                break;

                                            case 'Permission':
                                                $statusConfig = ['label' => 'IZIN/SAKIT', 'class' => 'badge-soft-info'];
                                                break;

                                            case 'Alpha':
                                                $statusConfig = ['label' => 'ALPHA', 'class' => 'badge-soft-danger'];
                                                break;

                                            default:
                                                $statusConfig = ['label' => 'BELUM ABSEN', 'class' => 'badge-soft-secondary'];
                                                break;
                                        }
                                    @endphp

                                    <tr>
                                        <td class="text-center font-weight-bold text-muted">{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="font-weight-bold text-dark">{{ $employee->name }}</div>
                                            <small class="text-muted text-uppercase" style="font-size: 0.7rem;">NIP: {{ $employee->nip }}</small>
                                        </td>
                                        <td>
                                            <div class="small mb-1 text-dark"><i class="fas fa-building mr-1 text-muted"></i> {{ $employee->division->name ?? '-' }}</div>
                                            <div class="small text-muted"><i class="fas fa-user-tag mr-1 text-muted"></i> {{ $employee->position->name ?? '-' }}</div>
                                        </td>
                                        <td class="text-center">
                                            <span class="font-weight-bold {{ $timeIn != '-' ? 'text-success' : 'text-muted' }}">{{ $timeIn }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="font-weight-bold {{ $timeOut != '-' ? 'text-primary' : 'text-muted' }}">{{ $timeOut }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge {{ $statusConfig['class'] }} px-3 py-2">
                                                {{ $statusConfig['label'] }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('attendance.employeeHistory', $employee->nip) }}" class="btn btn-sm btn-outline-primary shadow-sm rounded-pill px-3">
                                                <i class="fas fa-history mr-1"></i> Log
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .badge-soft-success { background-color: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
    .badge-soft-warning { background-color: #fffbeb; color: #d97706; border: 1px solid #fde68a; }
    .badge-soft-info { background-color: #f0f9ff; color: #0369a1; border: 1px solid #bae6fd; }
    .badge-soft-danger { background-color: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
    .badge-soft-secondary { background-color: #f9fafb; color: #4b5563; border: 1px solid #e5e7eb; }
    .table thead th { font-weight: 600; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; }
</style>
@endsection

