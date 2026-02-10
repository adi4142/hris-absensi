@extends('layouts.karyawan')

@section('title', 'Dashboard Karyawan')

@section('page_title', 'Dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="callout callout-info">
            <h5><i class="fas fa-info"></i> Selamat Datang, {{ $employee->name ?? Auth::user()->name }}!</h5>
            <p>{{ $employee->position->name ?? 'Posisi belum diatur' }} - PT VNEU Teknologi Indonesia</p>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $totalAttendance ?? 0 }}</h3>
                <p>Total Absensi</p>
            </div>
            <div class="icon">
                <i class="far fa-clock"></i>
            </div>
            <a href="{{ route('attendance.history') }}" class="small-box-footer">Lihat Riwayat <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $totalLate ?? 0 }}</h3>
                <p>Total Terlambat</p>
            </div>
            <div class="icon">
                <i class="fas fa-clipboard-list"></i>
            </div>
             <a href="{{ route('attendance.history', ['status' => 'Late']) }}" class="small-box-footer">Lihat Detail <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $totalPermission ?? 0 }}</h3>
                <p>Total Izin</p>
            </div>
            <div class="icon">
                <i class="far fa-calendar-alt"></i>
            </div>
             <a href="{{ route('attendance.history', ['status' => 'Permission']) }}" class="small-box-footer">Lihat Detail <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $totalAlpha ?? 0 }}</h3>
                <p>Total Alpha</p>
            </div>
            <div class="icon">
                <i class="fas fa-times-circle"></i>
            </div>
             <a href="{{ route('attendance.history', ['status' => 'Alpha']) }}" class="small-box-footer">Lihat Detail <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
</div>

<div class="row">
    <!-- Left Column: Action & Today's Status -->
    <div class="col-lg-5">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">Absensi Hari Ini</h3>
            </div>
            <div class="card-body box-profile d-flex flex-column justify-content-center text-center p-4">
                @if($todayAttendance)
                    @php
                        $statusColor = 'bg-secondary';
                        $statusIcon = 'far fa-clock';
                        $statusLabel = 'Belum Absen';

                        if($todayAttendance->status == 'Late') {
                            $statusColor = 'bg-warning';
                            $statusIcon = 'fas fa-exclamation-triangle';
                            $statusLabel = 'Terlambat';
                        } elseif($todayAttendance->status == 'Permission') {
                            $statusColor = 'bg-info';
                            $statusIcon = 'fas fa-file-alt';
                            $statusLabel = 'Izin / Sakit';
                        } elseif($todayAttendance->status == 'Present') {
                            $statusColor = 'bg-success';
                            $statusIcon = 'far fa-check-circle';
                            $statusLabel = 'Hadir';
                        }
                    @endphp

                    <div class="icon-circle {{ $statusColor }} mb-3 mx-auto" style="width: 100px; height: 100px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="{{ $statusIcon }} fa-4x text-white"></i>
                    </div>
                    
                    <h3 class="profile-username">{{ $statusLabel }}</h3>
                    <p class="text-muted mb-4">{{ \Carbon\Carbon::parse($todayAttendance->date)->translatedFormat('l, d F Y') }}</p>
                    
                    @if($todayAttendance->status == 'Permission')
                         <div class="alert alert-info">
                            Anda sedang izin hari ini.
                            <br>
                            <small>{{ $todayAttendance->description }}</small>
                         </div>
                    @else
                        <h1 class="text-primary font-weight-bold mb-1" style="font-size: 3.5rem;">
                            {{ $todayAttendance->time_in ? \Carbon\Carbon::parse($todayAttendance->time_in)->format('H:i') : '--:--' }}
                        </h1>
                        <p class="text-muted">Jam Masuk</p>

                        @if($todayAttendance->time_out)
                            <h3 class="text-success font-weight-bold mt-3">
                                {{ \Carbon\Carbon::parse($todayAttendance->time_out)->format('H:i') }}
                            </h3>
                            <p class="text-muted">Jam Pulang</p>
                        @elseif($todayAttendance->status != 'Permission' && $todayAttendance->status != 'Alpha')
                            <a href="{{ route('attendance.scan') }}" class="btn btn-danger btn-block mt-3">
                                <i class="fas fa-sign-out-alt mr-2"></i> Absen Pulang
                            </a>
                        @endif
                    @endif

                @else
                    <!-- Belum Absen -->
                    <div class="icon-circle bg-secondary mb-3 mx-auto" style="width: 100px; height: 100px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="far fa-clock fa-4x text-white"></i>
                    </div>
                    <h3 class="profile-username">Belum Absen</h3>
                    <p class="text-muted mb-4">{{ \Carbon\Carbon::today()->translatedFormat('l, d F Y') }}</p>
                    
                    <a href="{{ route('attendance.scan') }}" class="btn btn-primary btn-lg btn-block mb-3">
                        <i class="fas fa-fingerprint mr-2"></i> Klik untuk Absensi
                    </a>

                    <div class="text-center text-muted mb-2">- ATAU -</div>

                    <a href="{{ route('attendance.permission.create') }}" class="btn btn-outline-warning btn-block">
                        <i class="fas fa-file-alt mr-2"></i> Ajukan Izin / Sakit
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Right Column: Stats & History -->
    <div class="col-lg-7">
        <!-- Chart -->
        <div class="card card-info mb-4">
            <div class="card-header">
                <h3 class="card-title">Statistik Bulan Ini</h3>
            </div>
            <div class="card-body">
                <canvas id="attendanceChart" style="min-height: 200px; height: 200px; max-height: 200px; max-width: 100%;"></canvas>
            </div>
        </div>

        <!-- History -->
        <div class="card">
            <div class="card-header border-0">
                <h3 class="card-title">Riwayat Terakhir</h3>
                <div class="card-tools">
                    <a href="{{ route('attendance.history') }}" class="btn btn-tool btn-sm">Lihat Semua</a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-valign-middle">
                        <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th>Jam</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($attendanceHistory as $history)
                            @php
                                $statusClass = 'badge-secondary';
                                $statusLabel = 'Hadir';
                                if($history->status == 'Late') { $statusClass = 'badge-warning'; $statusLabel = 'Terlambat'; }
                                elseif($history->status == 'Permission') { $statusClass = 'badge-info'; $statusLabel = 'Izin'; }
                                elseif($history->status == 'Alpha') { $statusClass = 'badge-danger'; $statusLabel = 'Alpha'; }
                                elseif($history->status == 'Present') { $statusClass = 'badge-success'; $statusLabel = 'Hadir'; }
                            @endphp
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($history->date)->format('d/m/Y') }}</td>
                                <td><span class="badge {{ $statusClass }}">{{ $statusLabel }}</span></td>
                                <td>
                                    @if($history->status == 'Permission' || $history->status == 'Alpha')
                                        -
                                    @else
                                        {{ \Carbon\Carbon::parse($history->time_in)->format('H:i') }}
                                        @if($history->time_out) - {{ \Carbon\Carbon::parse($history->time_out)->format('H:i') }} @endif
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted">Belum ada data.</td>
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

@push('scripts')
<script src="{{ asset('AdminLTE/plugins/chart.js/Chart.min.js') }}"></script>
<script>
    $(function () {
        // Data from Controller
        var totalAttendance = {{ $totalAttendance ?? 0 }};
        var totalPermission = {{ $totalPermission ?? 0 }};
        var totalLate = {{ $totalLate ?? 0 }};
        var totalAlpha = {{ $totalAlpha ?? 0 }};
        
        // Calculate "On Time" (Approximate by subtracting known abnormalities from total)
        // assuming totalAttendance is Count of All Records.
        var presentOnTime = totalAttendance - totalLate - totalPermission - totalAlpha;
        if(presentOnTime < 0) presentOnTime = 0;

        var donutChartCanvas = $('#attendanceChart').get(0).getContext('2d')
        var donutData        = {
            labels: [
                'Hadir',
                'Izin',
                'Terlambat',
                'Alpha',
            ],
            datasets: [
                {
                    data: [presentOnTime, totalPermission, totalLate, totalAlpha],
                    backgroundColor : ['#007bff', '#28a745', '#ffc107', '#dc3545'],
                }
            ]
        }
        var donutOptions     = {
            maintainAspectRatio : false,
            responsive : true,
        }
        //Create pie chart
        new Chart(donutChartCanvas, {
            type: 'pie',
            data: donutData,
            options: donutOptions
        })
    })
</script>
@endpush
