@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('page_title', 'Dashboard Overview')

@section('content')
<div class="container-fluid">
    <!-- Welcome Message -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-primary text-white shadow border-0">
                <div class="card-body py-4">
                    <div class="d-flex align-items-center">
                        <div class="mr-4">
                            <i class="fas fa-chart-pie fa-4x opacity-50"></i>
                        </div>
                        <div>
                            <h2 class="font-weight-bold mb-1">Selamat Datang, {{ Auth::user()->name }}!</h2>
                            <p class="mb-0 opacity-75">Hari ini adalah {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}. Berikut adalah ringkasan operasional SDM Anda.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info elevation-2">
                <div class="inner">
                    <h3>{{ $totalEmployees }}</h3>
                    <p>Total Karyawan</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
                <a href="{{ route('employee.index') }}" class="small-box-footer">Kelola Karyawan <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success elevation-2">
                <div class="inner">
                    <h3>{{ $attendancePercentage }}<sup style="font-size: 20px">%</sup></h3>
                    <p>Kehadiran Hari Ini ({{ $totalPresent }})</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <a href="{{ route('attendance.index') }}" class="small-box-footer">Cek Absensi <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning elevation-2">
                <div class="inner">
                    <h3>{{ $totalVacancies }}</h3>
                    <p>Lowongan Aktif</p>
                </div>
                <div class="icon">
                    <i class="fas fa-briefcase"></i>
                </div>
                <a href="{{ route('jobvacancie.index') }}" class="small-box-footer">Kelola Lowongan <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger elevation-2">
                <div class="inner">
                    <h3>{{ $totalApplicants }}</h3>
                    <p>Total Pelamar</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-tie"></i>
                </div>
                <a href="{{ route('jobapplicant.index') }}" class="small-box-footer">Lihat Pelamar <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Employees -->
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <h3 class="card-title text-primary font-weight-bold">
                        <i class="fas fa-user-plus mr-1"></i> Karyawan Terbaru
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('employee.index') }}" class="btn btn-tool btn-sm text-primary">Lihat Semua</a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-items-center mb-0">
                            <tbody>
                                @forelse($recentEmployees as $employee)
                                <tr>
                                    <td width="60">
                                        <img src="{{ $employee->photo ? asset('storage/employees/'.$employee->photo) : asset('dist/img/user-default.png') }}" alt="User Image" class="img-size-50 img-circle shadow-sm">
                                    </td>
                                    <td>
                                        <div class="font-weight-bold">{{ $employee->name }}</div>
                                        <small class="text-muted">{{ $employee->nip }}</small>
                                    </td>
                                    <td>
                                        <div class="badge badge-light p-2">{{ $employee->position->name ?? 'N/A' }}</div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">Tidak ada data karyawan terbaru.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Applicants -->
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <h3 class="card-title text-warning font-weight-bold">
                        <i class="fas fa-file-alt mr-1"></i> Pelamar Terbaru
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('jobapplicant.index') }}" class="btn btn-tool btn-sm text-warning">Lihat Semua</a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <ul class="products-list product-list-in-card pl-2 pr-2">
                        @forelse($recentApplicants as $applicant)
                        <li class="item py-3 px-3">
                            <div class="product-img">
                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center border" style="width: 45px; height: 45px;">
                                    <i class="fas fa-user text-muted"></i>
                                </div>
                            </div>
                            <div class="product-info">
                                <a href="javascript:void(0)" class="product-title font-weight-bold">{{ $applicant->name }}
                                    <span class="badge badge-warning float-right font-weight-normal">{{ $applicant->created_at->diffForHumans() }}</span>
                                </a>
                                <span class="product-description text-muted">
                                    {{ $applicant->email }} | {{ $applicant->phone }}
                                </span>
                            </div>
                        </li>
                        @empty
                        <li class="item text-center py-4 text-muted">Tidak ada pelamar baru hari ini.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Access Module -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light border-bottom">
                    <h3 class="card-title font-weight-bold text-dark">Akses Cepat Modul</h3>
                </div>
                <div class="card-body py-4">
                    <div class="row text-center">
                        @php
                            $modules = [
                                ['route' => 'payroll.index', 'icon' => 'fas fa-money-bill-wave', 'color' => 'text-success', 'label' => 'Payroll'],
                                ['route' => 'division.index', 'icon' => 'fas fa-sitemap', 'color' => 'text-primary', 'label' => 'Divisi'],
                                ['route' => 'departement.index', 'icon' => 'fas fa-building', 'color' => 'text-info', 'label' => 'Departemen'],
                                ['route' => 'position.index', 'icon' => 'fas fa-id-badge', 'color' => 'text-indigo', 'label' => 'Jabatan'],
                                ['route' => 'user.index', 'icon' => 'fas fa-user-shield', 'color' => 'text-warning', 'label' => 'User'],
                                ['route' => 'profile.index', 'icon' => 'fas fa-user-cog', 'color' => 'text-secondary', 'label' => 'Profil']
                            ];
                        @endphp
                        @foreach($modules as $mod)
                        <div class="col-md-2 col-4 mb-3">
                            <a href="{{ route($mod['route']) }}" class="text-decoration-none transition-hover d-block p-3 rounded bg-light border">
                                <i class="{{ $mod['icon'] }} fa-2x mb-3 {{ $mod['color'] }}"></i>
                                <h6 class="mb-0 font-weight-bold text-dark small text-uppercase">{{ $mod['label'] }}</h6>
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .transition-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        background-color: #fff !important;
        transition: all 0.3s ease;
    }
    .bg-gradient-primary {
        background: linear-gradient(135deg, #4e73df 0%, #2e59d9 100%);
    }
    .opacity-50 { opacity: 0.5; }
    .opacity-75 { opacity: 0.75; }
    .text-indigo { color: #6610f2; }
    .img-size-50 { width: 45px; height: 45px; object-fit: cover; }
    .card { border-radius: 12px; overflow: hidden; }
    .small-box { border-radius: 12px; }
</style>
@endsection
