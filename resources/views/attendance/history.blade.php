@extends('layouts.karyawan')

@section('title', 'Riwayat Absensi')
@section('page_title', 'Ringkasan Kehadiran')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-history mr-2 text-primary"></i> Data Riwayat Absensi 
                        @if(request('status'))
                            @php
                                $statusFilters = [
                                    'Late' => ['text' => 'Terlambat', 'class' => 'badge-warning'],
                                    'Permission' => ['text' => 'Izin/Sakit', 'class' => 'badge-info'],
                                    'Alpha' => ['text' => 'Alpha', 'class' => 'badge-danger'],
                                ];
                                $filterLabel = $statusFilters[request('status')] ?? ['text' => request('status'), 'class' => 'badge-secondary'];
                            @endphp
                            <span class="badge {{ $filterLabel['class'] }} px-2 py-1 ml-1 small">{{ $filterLabel['text'] }}</span>
                        @endif
                    </h3>
                    <div class="card-tools">
                        <div class="btn-group shadow-sm rounded overflow-hidden">
                            @if(request('status'))
                                <a href="{{ route('attendance.history') }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-list-ul"></i> Semua
                                </a>
                            @endif
                            <a href="{{ route('attendance.dashboard') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-chevron-left"></i> Dashboard
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-muted">
                                <tr>
                                    <th class="text-center" width="60">No</th>
                                    <th>Hari & Tanggal</th>
                                    <th>Waktu Absensi</th>
                                    <th class="text-center">Lampiran / Foto</th>
                                    <th class="text-center">Status</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($attendances as $attendance)
                                    @php
                                        $status = $attendance->status;
                                        $configs = [
                                            'Present' => ['label' => 'HADIR', 'class' => 'badge-soft-success', 'icon' => 'fa-check-circle'],
                                            'Late' => ['label' => 'TERLAMBAT', 'class' => 'badge-soft-warning', 'icon' => 'fa-clock'],
                                            'Permission' => ['label' => 'IZIN/SAKIT', 'class' => 'badge-soft-info', 'icon' => 'fa-envelope-open-text'],
                                            'Alpha' => ['label' => 'ALPHA', 'class' => 'badge-soft-danger', 'icon' => 'fa-times-circle'],
                                        ];
                                        $statusConfig = $configs[$status] ?? ['label' => strtoupper($status), 'class' => 'badge-soft-secondary', 'icon' => 'fa-info-circle'];
                                    @endphp
                                    <tr>
                                        <td class="text-center font-weight-bold text-muted">{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="font-weight-bold text-dark">{{ \Carbon\Carbon::parse($attendance->date)->translatedFormat('l, d F Y') }}</div>
                                            @if($status == 'Late')
                                                <small class="text-danger"><i class="fas fa-exclamation-triangle mr-1"></i> Terlambat masuk</small>
                                            @endif
                                        </td>
                                        
                                        @if($status == 'Permission')
                                            <td><span class="text-muted italic small"><i class="fas fa-envelope mr-1"></i> Pengajuan Izin</span></td>
                                            <td class="text-center">
                                                @if($attendance->proof_file)
                                                    <a href="{{ Storage::url($attendance->proof_file) }}" target="_blank" class="btn btn-xs btn-outline-primary rounded-pill px-3">
                                                        <i class="fas fa-file-image"></i> Bukti
                                                    </a>
                                                @else
                                                    <span class="text-muted italic small">N/A</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span class="badge {{ $statusConfig['class'] }} px-3 py-2">
                                                    <i class="fas {{ $statusConfig['icon'] }} mr-1"></i> {{ $statusConfig['label'] }}
                                                </span>
                                            </td>
                                            <td><small class="text-muted">{{ $attendance->description }}</small></td>
                                        @elseif($status == 'Alpha')
                                            <td><span class="text-muted italic small"><i class="fas fa-user-slash mr-1"></i> Tidak Hadir</span></td>
                                            <td class="text-center"><span class="text-muted small">-</span></td>
                                            <td class="text-center">
                                                <span class="badge {{ $statusConfig['class'] }} px-3 py-2">
                                                    <i class="fas {{ $statusConfig['icon'] }} mr-1"></i> {{ $statusConfig['label'] }}
                                                </span>
                                            </td>
                                            <td><small class="text-danger italic">Tanpa Keterangan</small></td>
                                        @else
                                            <td>
                                                <div class="d-flex flex-column h6 mb-0">
                                                    <span class="text-success small mb-1"><i class="fas fa-sign-in-alt fw-bold mr-1"></i> {{ \Carbon\Carbon::parse($attendance->time_in)->format('H:i') }}</span>
                                                    <span class="text-secondary small"><i class="fas fa-sign-out-alt fw-bold mr-1"></i> {{ $attendance->time_out ? \Carbon\Carbon::parse($attendance->time_out)->format('H:i') : '--:--' }}</span>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                @if($attendance->photo_in || $attendance->photo_out)
                                                    <div class="d-icon-group d-flex justify-content-center">
                                                        @if($attendance->photo_in)
                                                            <div class="position-relative mr-n2 hover-lift">
                                                                <img src="{{ asset('storage/attendance/' . $attendance->photo_in) }}" class="rounded-circle border border-white shadow-sm" style="width: 32px; height: 32px; object-fit: cover; z-index: 10;" title="Foto Masuk">
                                                            </div>
                                                        @endif
                                                        @if($attendance->photo_out)
                                                            <div class="position-relative hover-lift">
                                                                <img src="{{ asset('storage/attendance/' . $attendance->photo_out) }}" class="rounded-circle border border-white shadow-sm" style="width: 32px; height: 32px; object-fit: cover;" title="Foto Keluar">
                                                            </div>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="text-muted small">N/A</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span class="badge {{ $statusConfig['class'] }} px-3 py-2">
                                                    <i class="fas {{ $statusConfig['icon'] }} mr-1"></i> {{ $statusConfig['label'] }}
                                                </span>
                                            </td>
                                            <td><small class="text-muted">{{ $attendance->description ?: 'Tepat Waktu' }}</small></td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <div class="my-3">
                                                <i class="fas fa-clipboard-list fa-3x text-light mb-3"></i>
                                                <p class="text-muted mb-0">Belum ada riwayat absensi yang tercatat.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($attendances instanceof \Illuminate\Pagination\LengthAwarePaginator && $attendances->hasPages())
                    <div class="card-footer bg-white py-3">
                        <div class="d-flex justify-content-center">
                            {{ $attendances->links() }}
                        </div>
                    </div>
                @endif
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
    .italic { font-style: italic; }
    .table thead th { font-weight: 600; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; }
    .hover-lift { transition: transform 0.2s; cursor: pointer; }
    .hover-lift:hover { transform: translateY(-2px) scale(1.1); z-index: 50; }
    .btn-xs { padding: 0.125rem 0.5rem; font-size: 0.75rem; }
</style>
@endsection


