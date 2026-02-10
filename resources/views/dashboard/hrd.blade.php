@extends('layouts.admin')

@section('title', 'Dashboard HRD')
@section('page_title', 'Dashboard HRD')

@section('content')
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
                <div class="table-responsive" style="max-height: 300px;">
                    <table class="table table-striped table-valign-middle">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Jam Masuk</th>
                                <th>Pulang</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($todayAttendances as $attendance)
                                @php
                                    $statusClass = 'badge-secondary';
                                    if($attendance->status == 'Present') $statusClass = 'badge-success';
                                    elseif($attendance->status == 'Late') $statusClass = 'badge-warning';
                                    elseif($attendance->status == 'Permission') $statusClass = 'badge-info';
                                    elseif($attendance->status == 'Alpha') $statusClass = 'badge-danger';
                                @endphp
                                <tr>
                                    <td>
                                        {{ $attendance->employee->name }}
                                        <div class="small text-muted">{{ $attendance->employee->position->name ?? '-' }}</div>
                                    </td>
                                    <td>{{ $attendance->time_in ? \Carbon\Carbon::parse($attendance->time_in)->format('H:i') : '-' }}</td>
                                    <td>{{ $attendance->time_out ? \Carbon\Carbon::parse($attendance->time_out)->format('H:i') : '-' }}</td>
                                    <td><span class="badge {{ $statusClass }}">{{ $attendance->status }}</span></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Belum ada data absensi hari ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Payroll Stats & Summary -->
    <div class="col-md-6">
        <!-- Payroll Stats -->
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">
                    Ringkasan Payroll: {{ $currentPayroll ? date("F Y", mktime(0, 0, 0, $currentPayroll->period_month, 10, $currentPayroll->period_year)) : 'Data Kosong' }}
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
                                <th class="text-right">Gaji Pokok</th>
                                <th class="text-right">Gaji Bersih</th>
                            </tr>
                        </thead>
                        <tbody>
                             @forelse($payrollDetails as $detail)
                                <tr>
                                    <td>{{ $detail->employee->name ?? '-' }}</td>
                                    <td class="text-right">Rp {{ number_format($detail->basic_salary, 0, ',', '.') }}</td>
                                    <td class="text-right font-weight-bold text-success">Rp {{ number_format($detail->total_salary, 0, ',', '.') }}</td>
                                </tr>
                             @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Data payroll belum tersedia.</td>
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
