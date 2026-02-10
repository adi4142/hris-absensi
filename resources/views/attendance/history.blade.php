@extends('layouts.karyawan')

@section('title', 'Riwayat Absensi')

@section('page_title', 'Riwayat Absensi')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    Data Riwayat Absensi 
                    @if(request('status') == 'Late') <span class="badge badge-warning">Terlambat</span> @endif
                    @if(request('status') == 'Permission') <span class="badge badge-success">Izin/Sakit</span> @endif
                    @if(request('status') == 'Alpha') <span class="badge badge-danger">Alpha</span> @endif
                </h3>
                <div class="card-tools">
                    @if(request('status'))
                        <a href="{{ route('attendance.history') }}" class="btn btn-info btn-sm">
                            <i class="fas fa-list mr-1"></i> Lihat Semua
                        </a>
                    @endif
                    <a href="{{ route('attendance.dashboard') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead>
                            <tr>
                                <th style="width: 50px">No</th>
                                <th>Hari / Tanggal</th>
                                <th>Jam/Status</th>
                                <th class="text-center">Bukti / Foto</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($attendances as $attendance)
                                @php
                                    $status = $attendance->status;
                                    $isLate = $status == 'Late';
                                    $bgClass = 'badge-secondary';
                                    if($status == 'Present') $bgClass = 'badge-success';
                                    if($status == 'Late') $bgClass = 'badge-warning';
                                    if($status == 'Permission') $bgClass = 'badge-info';
                                    if($status == 'Alpha') $bgClass = 'badge-danger';
                                @endphp
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="font-weight-bold">{{ \Carbon\Carbon::parse($attendance->date)->translatedFormat('l, d F Y') }}</div>
                                    </td>
                                    
                                    @if($status == 'Permission')
                                        <td><span class="badge badge-info">Izin / Sakit</span></td>
                                        <td class="text-center">
                                            @if($attendance->proof_file)
                                                <a href="{{ Storage::url($attendance->proof_file) }}" target="_blank" class="btn btn-xs btn-primary">
                                                    <i class="fas fa-paperclip"></i> Lihat Bukti
                                                </a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $attendance->description }}</td>
                                    @elseif($status == 'Alpha')
                                        <td><span class="badge badge-danger">Alpha</span></td>
                                        <td class="text-center">-</td>
                                        <td>Tidak Hadir Tanpa Keterangan</td>
                                    @else
                                        <td>
                                            <div class="small">Masuk: {{ \Carbon\Carbon::parse($attendance->time_in)->format('H:i') }}</div>
                                            <div class="small">Keluar: {{ $attendance->time_out ? \Carbon\Carbon::parse($attendance->time_out)->format('H:i') : '--:--' }}</div>
                                        </td>
                                        <td class="text-center">
                                            @if($attendance->photo_in)
                                                <img src="{{ asset('storage/attendance/' . $attendance->photo_in) }}" class="img-circle" style="width: 30px; height: 30px; object-fit: cover;" title="Masuk">
                                            @endif
                                            @if($attendance->photo_out)
                                                <img src="{{ asset('storage/attendance/' . $attendance->photo_out) }}" class="img-circle" style="width: 30px; height: 30px; object-fit: cover;" title="Keluar">
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge {{ $bgClass }}">{{ $status == 'Late' ? 'Terlambat' : 'Hadir' }}</span>
                                            @if($isLate) <br><small class="text-danger">Telat</small> @endif
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <i class="fas fa-info-circle mr-1"></i> Data tidak ditemukan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($attendances instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <div class="card-footer clearfix">
                    {{ $attendances->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .img-thumbnail {
        transition: transform .2s;
    }
    .img-thumbnail:hover {
        transform: scale(1.1);
        z-index: 10;
        cursor: pointer;
    }
</style>
@endpush

