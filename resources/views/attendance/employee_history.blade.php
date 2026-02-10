@extends('layouts.admin')

@section('title', 'Riwayat Absensi Karyawan')
@section('page_title', 'Riwayat Absensi: ' . $employee->name)

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Filter Riwayat</h3>
            </div>
             <form method="GET" action="{{ route('attendance.employeeHistory', $employee->nip) }}">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Bulan</label>
                                <select name="month" class="form-control">
                                    @for($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}" {{ $m == $month ? 'selected' : '' }}>
                                            {{ date("F", mktime(0, 0, 0, $m, 10)) }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Tahun</label>
                                <select name="year" class="form-control">
                                    @for($y = date('Y'); $y >= 2020; $y--)
                                        <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">Daftar Kehadiran</h3>
                <div class="card-tools">
                    <a href="{{ route('attendance.monitoring') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Kembali ke Monitoring
                    </a>
                </div>
            </div>
             <div class="card-body p-0">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Jam Masuk</th>
                            <th>Jam Keluar</th>
                            <th>Status</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendances as $attendance)
                            @php
                                $statusClass = 'badge-secondary';
                                if($attendance->status == 'Present') $statusClass = 'badge-success';
                                elseif($attendance->status == 'Late') $statusClass = 'badge-warning';
                                elseif($attendance->status == 'Permission') $statusClass = 'badge-info';
                                elseif($attendance->status == 'Alpha') $statusClass = 'badge-danger';
                            @endphp
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($attendance->date)->translatedFormat('l, d F Y') }}</td>
                                <td>{{ $attendance->time_in ? \Carbon\Carbon::parse($attendance->time_in)->format('H:i') : '-' }}</td>
                                <td>{{ $attendance->time_out ? \Carbon\Carbon::parse($attendance->time_out)->format('H:i') : '-' }}</td>
                                <td><span class="badge {{ $statusClass }}">{{ $attendance->status }}</span></td>
                                <td>{{ $attendance->description ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">Data tidak ditemukan untuk periode ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
