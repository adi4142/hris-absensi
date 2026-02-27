@extends('layouts.admin')

@section('title', 'Dasbor HRD')
@section('page_title', 'Dasbor HRD')

@section('content')
<!-- Info boxes for Total Data -->
<div class="row">
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-info elevation-1"><i class="fas fa-users"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Karyawan</span>
                <span class="info-box-number">{{ $totalEmployees }}</span>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3">
            <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-building"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Departemen</span>
                <span class="info-box-number">{{ $totalDepartments }}</span>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3">
            <span class="info-box-icon bg-success elevation-1"><i class="fas fa-user-tag"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Jabatan</span>
                <span class="info-box-number">{{ $totalPositions }}</span>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3">
            <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-layer-group"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Divisi</span>
                <span class="info-box-number">{{ $totalDivisions }}</span>
            </div>
        </div>
    </div>
</div>

<!-- Attendance Stats -->
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $totalPresent }}</h3>
                <p>Hadir Hari Ini</p>
            </div>
            <div class="icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <a href="{{ route('attendance.monitoring') }}" class="small-box-footer">Lihat Absensi <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $totalPermission }}</h3>
                <p>Izin / Sakit</p>
            </div>
            <div class="icon">
                <i class="fas fa-file-alt"></i>
            </div>
            <a href="{{ route('attendance.monitoring') }}" class="small-box-footer">Lihat Detail <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $totalLate }}</h3>
                <p>Terlambat</p>
            </div>
            <div class="icon">
                <i class="fas fa-clock"></i>
            </div>
            <a href="{{ route('attendance.monitoring') }}" class="small-box-footer">Lihat Detail <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $totalAlpha }}</h3>
                <p>Alpha</p>
            </div>
            <div class="icon">
                <i class="fas fa-times-circle"></i>
            </div>
            <a href="{{ route('attendance.monitoring') }}" class="small-box-footer">Lihat Detail <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
</div>

<div class="row">
    <!-- Today's Attendance Table -->
    <div class="col-md-6">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">Absensi Hari Ini</h3>
                <div class="card-tools">
                    <a href="{{ route('attendance.monitoring') }}" class="btn btn-tool btn-sm">Lihat Semua</a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 340px;">
                    <table class="table table-striped table-valign-middle">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Jam Masuk</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($todayAttendances as $attendance)
                                @php
                                    $statusClass = 'badge-secondary';
                                    $statusLabel = 'Hadir';
                                    if($attendance->status == 'Present') { $statusClass = 'badge-success'; $statusLabel = 'Hadir'; }
                                    elseif($attendance->status == 'Late') { $statusClass = 'badge-warning'; $statusLabel = 'Terlambat'; }
                                    elseif($attendance->status == 'Permission') { $statusClass = 'badge-info'; $statusLabel = 'Izin'; }
                                    elseif($attendance->status == 'Alpha') { $statusClass = 'badge-danger'; $statusLabel = 'Alpha'; }
                                @endphp
                                <tr>
                                    <td>
                                        {{ $attendance->employee->name }}
                                        <div class="small text-muted">{{ $attendance->employee->position->name ?? '-' }}</div>
                                    </td>
                                    <td>{{ $attendance->time_in ? \Carbon\Carbon::parse($attendance->time_in)->format('H:i') : '-' }}</td>
                                    <td><span class="badge {{ $statusClass }}">{{ $statusLabel }}</span></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Belum ada data absensi hari ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Payroll Info -->
    <div class="col-md-6">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">
                    Info Penggajian: {{ $currentPayroll ? \Carbon\Carbon::create()->month($currentPayroll->period_month)->translatedFormat('F') . ' ' . $currentPayroll->period_year : 'Data Kosong' }}
                </h3>
            </div>
            <div class="card-body">
                @if($currentPayroll)
                    <div class="row">
                        <div class="col-4 border-right">
                            <div class="description-block">
                                <span class="description-percentage text-success"><i class="fas fa-caret-up"></i> Total Gaji</span>
                                <h5 class="description-header">Rp {{ number_format($totalSalaryPaid, 0, ',', '.') }}</h5>
                            </div>
                        </div>
                        <div class="col-4 border-right">
                            <div class="description-block">
                                <span class="description-percentage text-warning"><i class="fas fa-caret-left"></i> Tunjangan</span>
                                <h5 class="description-header">Rp {{ number_format($totalAllowances, 0, ',', '.') }}</h5>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="description-block">
                                <span class="description-percentage text-danger"><i class="fas fa-caret-down"></i> Potongan</span>
                                <h5 class="description-header">Rp {{ number_format($totalDeductions, 0, ',', '.') }}</h5>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-warning">Payroll belum dibuat untuk bulan ini.</div>
                @endif
            </div>
        </div>

        <!-- Payroll Table -->
        <div class="card">
             <div class="card-header border-0">
                <h3 class="card-title">Detail Payroll Terakhir</h3>
                <div class="card-tools">
                    <a href="{{ route('payroll.index') }}" class="btn btn-tool btn-sm">Lihat Semua</a>
                </div>
            </div>
            <div class="card-body p-0">
                 <div class="table-responsive" style="max-height: 200px;">
                    <table class="table table-striped table-valign-middle">
                        <thead>
                            <tr>
                                <th>Karyawan</th>
                                <th class="text-right">Gaji Bersih</th>
                            </tr>
                        </thead>
                        <tbody>
                             @forelse($payrollDetails as $detail)
                                <tr>
                                    <td>{{ $detail->employee->name ?? '-' }}</td>
                                    <td class="text-right font-weight-bold text-success">Rp {{ number_format($detail->total_salary, 0, ',', '.') }}</td>
                                </tr>
                             @empty
                                <tr>
                                    <td colspan="2" class="text-center text-muted">Data payroll belum tersedia.</td>
                                </tr>
                             @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Pengajuan Cuti Menunggu Persetujuan -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card card-warning card-outline">
            <div class="card-header">
                <h3 class="card-title">Cuti Menunggu Persetujuan</h3>
                <div class="card-tools">
                    <a href="{{ route('leave.index') }}" class="btn btn-tool btn-sm">Kelola Cuti</a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-valign-middle">
                        <thead>
                            <tr>
                                <th>Karyawan</th>
                                <th>Periode Cuti</th>
                                <th>Durasi</th>
                                <th>Alasan</th>
                                <th class="text-center">Aksi (Opsional Catatan)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendingLeaves as $leave)
                                <tr>
                                    <td>
                                        <div class="font-weight-bold">{{ $leave->user->name ?? 'N/A' }}</div>
                                        <div class="small text-muted">{{ $leave->nip }}</div>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($leave->start_date)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($leave->end_date)->format('d/m/Y') }}</td>
                                    <td><span class="badge badge-info">{{ $leave->days }} Hari</span></td>
                                    <td>{{ Str::limit($leave->reason, 30) }}</td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <!-- Tombol Approve Modal -->
                                            <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#approveModal{{ $leave->id }}" title="Setujui">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <!-- Tombol Reject Modal -->
                                            <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#rejectModal{{ $leave->id }}" title="Tolak">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>

                                        <!-- Modal Approve -->
                                        <div class="modal fade" id="approveModal{{ $leave->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content text-left">
                                                    <div class="modal-header bg-success text-white">
                                                        <h5 class="modal-title">Setujui Pengajuan Cuti</h5>
                                                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <form action="{{ route('leave.approve', $leave->id) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-body">
                                                            <p>Menyetujui cuti dari <strong>{{ $leave->user->name ?? 'N/A' }}</strong>.</p>
                                                            <div class="form-group">
                                                                <label>Catatan HRD (Opsional):</label>
                                                                <textarea name="hrd_note" class="form-control" rows="3" placeholder="Masukkan catatan jika ada..."></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn btn-success">Setujui Cuti</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Modal Reject -->
                                        <div class="modal fade" id="rejectModal{{ $leave->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content text-left">
                                                    <div class="modal-header bg-danger text-white">
                                                        <h5 class="modal-title">Tolak Pengajuan Cuti</h5>
                                                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <form action="{{ route('leave.reject', $leave->id) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-body">
                                                            <p>Menolak cuti dari <strong>{{ $leave->user->name ?? 'N/A' }}</strong>.</p>
                                                            <div class="form-group">
                                                                <label>Catatan HRD (Opsional):</label>
                                                                <textarea name="hrd_note" class="form-control" rows="3" placeholder="Masukkan alasan penolakan..."></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn btn-danger">Tolak Cuti</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">Tidak ada pengajuan cuti yang menunggu persetujuan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
