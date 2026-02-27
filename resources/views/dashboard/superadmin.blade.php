@extends('layouts.admin')

@section('title', 'Dasbor Super Admin')
@section('page_title', 'Dasbor Super Admin')

@section('content')
<!-- Ringkasan Master Data -->
<h5 class="mb-3 font-weight-bold text-dark"><i class="fas fa-database text-primary mr-2"></i>Ringkasan Master Data</h5>
<div class="row mb-3">
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box border-0 shadow-sm rounded-lg" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: white;">
            <span class="info-box-icon"><i class="fas fa-users"></i></span>
            <div class="info-box-content">
                <span class="info-box-text text-uppercase font-weight-bold opacity-75">Total Karyawan</span>
                <span class="info-box-number" style="font-size: 1.5rem;">{{ $totalEmployees }}</span>
            </div>
            <a href="{{ route('user.index') }}" class="stretched-link"></a>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box border-0 shadow-sm rounded-lg" style="background: linear-gradient(135deg, #ff416c 0%, #ff4b2b 100%); color: white;">
            <span class="info-box-icon"><i class="fas fa-building"></i></span>
            <div class="info-box-content">
                <span class="info-box-text text-uppercase font-weight-bold opacity-75">Departemen</span>
                <span class="info-box-number" style="font-size: 1.5rem;">{{ $totalDepartments }}</span>
            </div>
            <a href="{{ route('departement.index') }}" class="stretched-link"></a>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box border-0 shadow-sm rounded-lg" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white;">
            <span class="info-box-icon"><i class="fas fa-user-tag"></i></span>
            <div class="info-box-content">
                <span class="info-box-text text-uppercase font-weight-bold opacity-75">Jabatan</span>
                <span class="info-box-number" style="font-size: 1.5rem;">{{ $totalPositions }}</span>
            </div>
            <a href="{{ route('position.index') }}" class="stretched-link"></a>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box border-0 shadow-sm rounded-lg" style="background: linear-gradient(135deg, #f7971e 0%, #ffd200 100%); color: white;">
            <span class="info-box-icon"><i class="fas fa-layer-group"></i></span>
            <div class="info-box-content">
                <span class="info-box-text text-uppercase font-weight-bold opacity-75">Divisi</span>
                <span class="info-box-number" style="font-size: 1.5rem;">{{ $totalDivisions }}</span>
            </div>
            <a href="{{ route('division.index') }}" class="stretched-link"></a>
        </div>
    </div>
</div>

<!-- Kehadiran Hari Ini -->
<h5 class="mb-3 font-weight-bold text-dark mt-4"><i class="far fa-calendar-alt text-primary mr-2"></i>Kehadiran Hari Ini</h5>
<div class="row mb-3">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success shadow-sm rounded-lg border-0">
            <div class="inner">
                <h3>{{ $totalPresent }}</h3>
                <p>Hadir Tepat Waktu</p>
            </div>
            <div class="icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <a href="{{ route('attendance.monitoring') }}" class="small-box-footer py-2">Lihat Monitor <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info shadow-sm rounded-lg border-0">
            <div class="inner">
                <h3>{{ $totalPermission }}</h3>
                <p>Izin / Sakit</p>
            </div>
            <div class="icon">
                <i class="fas fa-file-medical"></i>
            </div>
            <a href="{{ route('attendance.monitoring') }}" class="small-box-footer py-2">Lihat Detail <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning shadow-sm rounded-lg border-0">
            <div class="inner">
                <h3 class="text-white">{{ $totalLate }}</h3>
                <p class="text-white">Terlambat</p>
            </div>
            <div class="icon">
                <i class="fas fa-clock"></i>
            </div>
            <a href="{{ route('attendance.monitoring') }}" class="small-box-footer py-2" style="color: rgba(255,255,255,0.8) !important;">Lihat Detail <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger shadow-sm rounded-lg border-0">
            <div class="inner">
                <h3>{{ $totalAlpha }}</h3>
                <p>Alpha (Mangkir)</p>
            </div>
            <div class="icon">
                <i class="fas fa-times-circle"></i>
            </div>
            <a href="{{ route('attendance.monitoring') }}" class="small-box-footer py-2">Lihat Detail <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
</div>

<!-- Data Terbaru -->
<div class="row">
    <!-- Pengajuan Cuti -->
    <div class="col-md-6 mb-4">
        <div class="card shadow border-0 rounded-lg h-100">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="card-title font-weight-bold text-dark m-0"><i class="fas fa-envelope-open-text text-warning mr-2"></i> Cuti Menunggu Persetujuan</h3>
                    <a href="{{ route('leave.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">Lihat Semua</a>
                </div>
            </div>
            <div class="card-body p-0 mt-3">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-secondary">
                            <tr>
                                <th class="pl-4">Karyawan</th>
                                <th>Periode Cuti</th>
                                <th class="text-center">Aksi (Opsional Catatan)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendingLeaves as $leave)
                                <tr>
                                    <td class="pl-4">
                                        <div class="font-weight-bold text-dark">{{ $leave->user->name ?? 'N/A' }}</div>
                                        <small class="text-muted">{{ $leave->nip }}</small>
                                    </td>
                                    <td>
                                        <div class="text-dark">{{ \Carbon\Carbon::parse($leave->start_date)->format('d M y') }} - {{ \Carbon\Carbon::parse($leave->end_date)->format('d M y') }}</div>
                                        <small class="text-muted">{{ $leave->days }} Hari</small>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#approveModal{{ $leave->id }}" title="Setujui">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#rejectModal{{ $leave->id }}" title="Tolak">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>

                                        <!-- Modal Approve -->
                                        <div class="modal fade" id="approveModal{{ $leave->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content text-left">
                                                    <div class="modal-header bg-success text-white">
                                                        <h5 class="modal-title font-weight-bold">Setujui Pengajuan Cuti</h5>
                                                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <form action="{{ route('leave.approve', $leave->id) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-body">
                                                            <p>Menyetujui cuti dari <strong>{{ $leave->user->name ?? 'N/A' }}</strong>.</p>
                                                            <div class="form-group">
                                                                <label class="text-muted">Catatan HRD/Superadmin (Opsional):</label>
                                                                <textarea name="hrd_note" class="form-control" rows="3" placeholder="Masukkan catatan jika ada..."></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer bg-light">
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
                                                        <h5 class="modal-title font-weight-bold">Tolak Pengajuan Cuti</h5>
                                                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <form action="{{ route('leave.reject', $leave->id) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-body">
                                                            <p>Menolak cuti dari <strong>{{ $leave->user->name ?? 'N/A' }}</strong>.</p>
                                                            <div class="form-group">
                                                                <label class="text-muted">Catatan HRD/Superadmin (Opsional):</label>
                                                                <textarea name="hrd_note" class="form-control" rows="3" placeholder="Masukkan alasan penolakan..."></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer bg-light">
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
                                    <td colspan="3" class="text-center text-muted py-5">
                                        <i class="fas fa-check-circle fa-2x mb-3 text-success opacity-50"></i>
                                        <p class="mb-0">Tidak ada pengajuan cuti yang menunggu.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Karyawan Baru -->
    <div class="col-md-6 mb-4">
        <div class="card shadow border-0 rounded-lg h-100">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="card-title font-weight-bold text-dark m-0"><i class="fas fa-user-plus text-success mr-2"></i> Pendaftar / Karyawan Baru</h3>
                    <a href="{{ route('user.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">Kelola</a>
                </div>
            </div>
            <div class="card-body p-0 mt-3">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-secondary">
                            <tr>
                                <th class="pl-4">Nama</th>
                                <th>Jabatan</th>
                                <th>Waktu Bergabung</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentUsers as $emp)
                                <tr>
                                    <td class="pl-4">
                                        <div class="font-weight-bold text-dark">{{ $emp->name }}</div>
                                        <small class="text-muted">{{ $emp->nip }}</small>
                                    </td>
                                    <td>
                                        <div class="text-dark">{{ $emp->position->name ?? '-' }}</div>
                                        <small class="text-muted">{{ $emp->division->name ?? '-' }}</small>
                                    </td>
                                    <td><span class="text-secondary"><i class="far fa-clock mr-1"></i> {{ $emp->created_at->diffForHumans() }}</span></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-5">
                                        <i class="fas fa-users-slash fa-2x mb-3 text-secondary opacity-50"></i>
                                        <p class="mb-0">Belum ada karyawan baru.</p>
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

<!-- Minimal Styling Fixes -->
<style>
    .info-box-icon {
        width: 70px;
        font-size: 2rem;
        background: rgba(255,255,255,0.2) !important;
        border-radius: 10px;
        margin: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .info-box {
        position: relative;
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .info-box:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.15) !important;
    }
    .info-box .stretched-link {
        z-index: 2;
    }
    .small-box {
        transition: transform 0.3s ease;
    }
    .small-box:hover {
        transform: translateY(-5px);
    }
    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
        transition: background-color 0.2s;
    }
    .opacity-75 { opacity: 0.75; }
    .opacity-50 { opacity: 0.50; }
</style>
@endsection
