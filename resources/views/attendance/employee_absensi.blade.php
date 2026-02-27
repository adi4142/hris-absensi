@extends('layouts.karyawan')

@section('title', 'Absensi Karyawan')
@section('page_title', 'Ringkasan Kehadiran')

@section('content')
<div class="container-fluid">
    <!-- Today's Status Header -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card shadow-sm border-0 bg-gradient-primary text-white overflow-hidden" style="border-radius: 20px;">
                <div class="card-body p-4 p-md-5 position-relative">
                    <div style="position: absolute; right: -30px; top: -30px; opacity: 0.1;">
                        <i class="fas fa-fingerprint fa-12x"></i>
                    </div>
                    <div class="row align-items-center">
                        <div class="col-lg-8 mb-4 mb-lg-0">
                            <h2 class="font-weight-bold mb-2">Halo, {{ $employee->name ?? Auth::user()->name }}!</h2>
                            <p class="mb-4 opacity-75 font-weight-500 lead">
                                {{ $employee->position->name ?? 'Staf' }} — PT VNEU Teknologi Indonesia
                            </p>
                            
                            <div class="d-flex flex-wrap align-items-center gap-3">
                                @if(isset($activeLeave) && $activeLeave)
                                    <div class="badge badge-glass px-4 py-3 mr-3 mb-2" style="background: rgba(99, 102, 241, 0.3);">
                                        <i class="fas fa-calendar-minus mr-2 text-warning"></i> Sedang Cuti s/d {{ \Carbon\Carbon::parse($activeLeave->end_date)->translatedFormat('d F Y') }}
                                    </div>
                                    <a href="{{ route('leave.index') }}" class="btn btn-white text-primary font-weight-bold rounded-pill px-4 shadow-sm mb-2">
                                        Detail Cuti <i class="fas fa-arrow-right ml-2 small"></i>
                                    </a>
                                @elseif(!$todayAttendance)
                                    <div class="badge badge-glass px-4 py-3 mr-3 mb-2">
                                        <i class="fas fa-exclamation-circle mr-2"></i> Belum Absen Masuk
                                    </div>
                                    <a href="{{ route('attendance.scan') }}" class="btn btn-white text-primary font-weight-bold rounded-pill px-4 shadow-sm mb-2">
                                        Absen Sekarang <i class="fas fa-arrow-right ml-2 small"></i>
                                    </a>
                                @elseif(!$todayAttendance->time_out)
                                    <div class="badge badge-glass px-4 py-3 mr-3 mb-2">
                                        <i class="fas fa-check-circle mr-2 text-success"></i> Sudah Absen Masuk ({{ \Carbon\Carbon::parse($todayAttendance->time_in)->format('H:i') }})
                                    </div>
                                    <a href="{{ route('attendance.scan') }}" class="btn btn-danger font-weight-bold rounded-pill px-4 shadow-sm mb-2">
                                        ABSEN PULANG <i class="fas fa-sign-out-alt ml-2 small"></i>
                                    </a>
                                @else
                                    <div class="badge badge-glass px-4 py-3 mb-2">
                                        <i class="fas fa-calendar-check mr-2 text-success"></i> Absensi Hari Ini Selesai
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-4 text-center">
                            <div class="digital-clock p-3 px-4 rounded-lg bg-white-10 backdrop-blur shadow-inner inline-block">
                                <div class="small text-uppercase opacity-75 letter-spacing-1 mb-1">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</div>
                                <h1 class="display-4 font-weight-bold mb-0 clock-text" id="liveClock">00:00:00</h1>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats & History Grid -->
    <div class="row">
        <!-- History Table -->
        <div class="col-lg-12">
            <div class="card shadow-sm border-0 mb-4 overflow-hidden" style="border-radius: 15px;">
                <div class="card-header bg-white py-3 border-0 d-flex align-items-center justify-content-between">
                    <h5 class="card-title font-weight-bold mb-0 text-dark">
                        <i class="fas fa-history mr-2 text-primary"></i> Catatan Absensi Terbaru
                    </h5>
                    <div class="card-tools d-flex gap-2">
                        @if(request('status'))
                            <a href="{{ route('attendance.history') }}" class="btn btn-soft-secondary btn-sm px-3 rounded-pill">
                                <i class="fas fa-list mr-1"></i> Lihat Semua
                            </a>
                        @endif
                        <a href="{{ route('attendance.dashboard') }}" class="btn btn-soft-primary btn-sm px-3 rounded-pill ml-2">
                            <i class="fas fa-home mr-1"></i> Dashboard
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-muted small text-uppercase letter-spacing-1">
                                <tr>
                                    <th class="px-4 py-3" width="60">No</th>
                                    <th class="py-3">Hari & Tanggal</th>
                                    <th class="py-3">Status / Waktu</th>
                                    <th class="py-3 text-center">Dokumentasi</th>
                                    <th class="py-3">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($attendances as $att)
                                    @php
                                        switch ($att->status) {
                                            case 'Present':
                                                $badgeConfig = [
                                                    'class' => 'badge-soft-success',
                                                    'label' => 'Hadir'
                                                ];
                                                break;

                                            case 'Late':
                                                $badgeConfig = [
                                                    'class' => 'badge-soft-warning',
                                                    'label' => 'Terlambat'
                                                ];
                                                break;

                                            case 'Permission':
                                                $badgeConfig = [
                                                    'class' => 'badge-soft-info',
                                                    'label' => 'Izin / Sakit'
                                                ];
                                                break;

                                            case 'Alpha':
                                                $badgeConfig = [
                                                    'class' => 'badge-soft-danger',
                                                    'label' => 'Alpha'
                                                ];
                                                break;

                                            default:
                                                $badgeConfig = [
                                                    'class' => 'badge-soft-secondary',
                                                    'label' => $att->status
                                                ];
                                                break;
                                        }
                                    @endphp

                                    <tr>
                                        <td class="px-4 font-weight-bold text-muted small">{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="font-weight-bold text-dark mb-0">{{ \Carbon\Carbon::parse($att->date)->translatedFormat('d M Y') }}</div>
                                            <small class="text-muted">{{ \Carbon\Carbon::parse($att->date)->translatedFormat('l') }}</small>
                                        </td>
                                        <td>
                                            @if($att->status == 'Permission')
                                                <span class="badge {{ $badgeConfig['class'] }} px-3 py-2">IZIN / SAKIT</span>
                                            @elseif($att->status == 'Alpha')
                                                <span class="badge {{ $badgeConfig['class'] }} px-3 py-2">TANPA KETERANGAN</span>
                                            @else
                                                <div class="d-flex align-items-center">
                                                    <span class="badge {{ $badgeConfig['class'] }} px-3 py-2 mr-3" style="min-width: 80px;">{{ strtoupper($badgeConfig['label']) }}</span>
                                                    <div class="small font-weight-500">
                                                        <span class="text-dark">{{ \Carbon\Carbon::parse($att->time_in)->format('H:i') }}</span>
                                                        @if($att->time_out) <span class="mx-1 text-muted">→</span> <span class="text-primary">{{ \Carbon\Carbon::parse($att->time_out)->format('H:i') }}</span> @endif
                                                    </div>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($att->status == 'Permission')
                                                @if($att->proof_file)
                                                    <a href="{{ Storage::url($att->proof_file) }}" target="_blank" class="btn btn-xs btn-outline-info rounded-pill px-3 py-1">
                                                        <i class="fas fa-file-alt mr-1"></i> Bukti PDF/Doc
                                                    </a>
                                                @else
                                                    <small class="text-muted italic">Tidak ada lampiran</small>
                                                @endif
                                            @elseif($att->status == 'Alpha')
                                                <i class="fas fa-times text-danger opacity-25"></i>
                                            @else
                                                <div class="d-flex justify-content-center gap-1">
                                                    @if($att->photo_in)
                                                        <img src="{{ asset('storage/attendance/' . $att->photo_in) }}" class="rounded shadow-sm border border-white preview-img" style="width: 32px; height: 32px; object-fit: cover;" title="Foto Masuk">
                                                    @endif
                                                    @if($att->photo_out)
                                                        <img src="{{ asset('storage/attendance/' . $att->photo_out) }}" class="rounded shadow-sm border border-white preview-img" style="width: 32px; height: 32px; object-fit: cover;" title="Foto Pulang">
                                                    @endif
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="small text-muted text-truncate" style="max-width: 200px;" title="{{ $att->description ?? 'Kehadiran harian normal' }}">
                                                {{ $att->description ?? 'Terdaftar melalui sistem digital' }}
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <div class="py-4">
                                                <img src="https://illustrations.popsy.co/blue/no-messages.svg" class="mb-3 opacity-50" style="width: 140px;">
                                                <h6 class="text-muted font-weight-light">Belum ada riwayat absensi ditemukan</h6>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($attendances instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    <div class="card-footer bg-light border-0 py-3">
                        {{ $attendances->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    .bg-gradient-primary { background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); }
    .bg-white-10 { background-color: rgba(255, 255, 255, 0.1); }
    .backdrop-blur { backdrop-filter: blur(10px); }
    .badge-glass { background: rgba(255, 255, 255, 0.2); color: white; border: 1px solid rgba(255, 255, 255, 0.3); }
    .btn-white { background: white; color: #4f46e5 !important; border: 0; }
    
    .badge-soft-success { background-color: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
    .badge-soft-warning { background-color: #fffbeb; color: #d97706; border: 1px solid #fde68a; }
    .badge-soft-info { background-color: #f0f9ff; color: #0369a1; border: 1px solid #bae6fd; }
    .badge-soft-danger { background-color: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
    .badge-soft-secondary { background-color: #f3f4f6; color: #6b7280; border: 1px solid #e5e7eb; }

    .btn-soft-primary { background-color: #eef2ff; color: #4f46e5; border: 0; }
    .btn-soft-primary:hover { background-color: #e0e7ff; color: #3730a3; }
    .btn-soft-secondary { background-color: #f3f4f6; color: #4b5563; border: 0; }
    
    .preview-img { cursor: pointer; transition: transform 0.2s; }
    .preview-img:hover { transform: scale(3) translateY(-10px); z-index: 100 !important; }
    
    .clock-text { letter-spacing: 2px; text-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    .gap-3 { gap: 1rem; }
    .letter-spacing-1 { letter-spacing: 1px; }
    .font-weight-500 { font-weight: 500; }
</style>
@endsection

@push('scripts')
<script>
    function updateClock() {
        const now = new Date();
        const h = String(now.getHours()).padStart(2, '0');
        const m = String(now.getMinutes()).padStart(2, '0');
        const s = String(now.getSeconds()).padStart(2, '0');
        document.getElementById('liveClock').innerText = `${h}:${m}:${s}`;
    }
    setInterval(updateClock, 1000);
    updateClock();
</script>
@endpush
