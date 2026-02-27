@extends('layouts.admin')

@section('title', 'Riwayat Absensi Karyawan')
@section('page_title', 'Arsip Kehadiran Individu')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Profile Overview -->
        <div class="col-12 mb-4">
            <div class="card shadow-sm border-0 bg-gradient-light">
                <div class="card-body py-4 d-flex align-items-center">
                    <div class="rounded-circle bg-white shadow-sm d-flex align-items-center justify-content-center p-3 mr-4" style="width: 70px; height: 70px;">
                        <i class="fas fa-user-clock fa-2x text-primary"></i>
                    </div>
                    <div>
                        <h4 class="font-weight-bold mb-1 text-dark">{{ $employee->name }}</h4>
                        <div class="text-muted small">
                            <span class="mr-3"><i class="fas fa-id-card mr-1"></i> NIP: {{ $employee->nip }}</span>
                            <span><i class="fas fa-building mr-1"></i> {{ $employee->division->name ?? 'N/A' }} / {{ $employee->position->name ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="card-title font-weight-bold mb-0 text-primary">
                        <i class="fas fa-filter mr-2"></i> Filter Periode
                    </h6>
                </div>
                <form method="GET" action="{{ route('attendance.employeeHistory', $employee->nip) }}">
                    <div class="card-body">
                        <div class="form-group mb-4">
                            <label class="small font-weight-bold text-muted text-uppercase mb-2">Pilih Bulan</label>
                            <select name="month" class="form-control custom-select">
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ $m == $month ? 'selected' : '' }}>
                                        {{ date("F", mktime(0, 0, 0, $m, 10)) }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="form-group mb-4">
                            <label class="small font-weight-bold text-muted text-uppercase mb-2">Pilih Tahun</label>
                            <select name="year" class="form-control custom-select">
                                @for($y = date('Y'); $y >= 2020; $y--)
                                    <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block shadow-sm">
                            Tampilkan Data <i class="fas fa-search ml-1"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Data Card -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                    <h6 class="card-title font-weight-bold mb-0 text-dark">
                        <i class="fas fa-table mr-2 text-muted"></i> Log Absensi
                    </h6>
                    <a href="{{ route('attendance.monitoring') }}" class="btn btn-sm btn-light border text-muted px-3">
                        <i class="fas fa-arrow-left mr-1"></i> Monitoring
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-muted small text-uppercase letter-spacing-1">
                                <tr>
                                    <th class="px-4">Tanggal</th>
                                    <th class="text-center">Jam</th>
                                    <th class="text-center">Status</th>
                                    <th class="px-4">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($attendances as $attendance)
                                    @php
                                        switch ($attendance->status) {
                                            case 'Present':
                                                $statusConfig = ['label' => 'HADIR', 'class' => 'badge-soft-success'];
                                                break;

                                            case 'Late':
                                                $statusConfig = ['label' => 'TERLAMBAT', 'class' => 'badge-soft-warning'];
                                                break;

                                            case 'Permission':
                                                $statusConfig = ['label' => 'IZIN', 'class' => 'badge-soft-info'];
                                                break;

                                            case 'Alpha':
                                                $statusConfig = ['label' => 'ALPHA', 'class' => 'badge-soft-danger'];
                                                break;

                                            default:
                                                $statusConfig = [
                                                    'label' => strtoupper($attendance->status),
                                                    'class' => 'badge-soft-secondary'
                                                ];
                                                break;
                                        }
                                    @endphp

                                    <tr>
                                        <td class="px-4">
                                            <div class="font-weight-500 text-dark">{{ \Carbon\Carbon::parse($attendance->date)->translatedFormat('d M Y') }}</div>
                                            <small class="text-muted">{{ \Carbon\Carbon::parse($attendance->date)->translatedFormat('l') }}</small>
                                        </td>
                                        <td class="text-center">
                                            <div class="small">
                                                <span class="text-success"><i class="fas fa-sign-in-alt mr-1"></i> {{ $attendance->time_in ? \Carbon\Carbon::parse($attendance->time_in)->format('H:i') : '--:--' }}</span><br>
                                                <span class="text-secondary"><i class="fas fa-sign-out-alt mr-1"></i> {{ $attendance->time_out ? \Carbon\Carbon::parse($attendance->time_out)->format('H:i') : '--:--' }}</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge {{ $statusConfig['class'] }} px-3 py-2" style="font-size: 0.65rem;">
                                                {{ $statusConfig['label'] }}
                                            </span>
                                        </td>
                                        <td class="px-4">
                                            <small class="text-muted d-block text-truncate" style="max-width: 150px;" title="{{ $attendance->description }}">
                                                {{ $attendance->description ?? '-' }}
                                            </small>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5">
                                            <i class="fas fa-folder-open fa-3x text-light mb-3"></i>
                                            <p class="text-muted mb-0 small uppercase letter-spacing-1">Tidak ada catatan untuk periode ini</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-gradient-light { background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); }
    .badge-soft-success { background-color: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
    .badge-soft-warning { background-color: #fffbeb; color: #d97706; border: 1px solid #fde68a; }
    .badge-soft-info { background-color: #f0f9ff; color: #0369a1; border: 1px solid #bae6fd; }
    .badge-soft-danger { background-color: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
    .badge-soft-secondary { background-color: #f9fafb; color: #4b5563; border: 1px solid #e5e7eb; }
    .font-weight-500 { font-weight: 500; }
    .letter-spacing-1 { letter-spacing: 0.5px; }
    .custom-select { border-radius: 8px; border: 1px solid #e5e7eb; }
    .custom-select:focus { border-color: #4f46e5; box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1); }
</style>
@endsection

