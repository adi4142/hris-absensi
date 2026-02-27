@extends('layouts.admin')

@section('title', 'Pengaturan Sistem')
@section('page_title', 'Pengaturan Sistem')

@section('content')
<div class="row">
    <div class="col-md-8">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">Konfigurasi Global</h3>
            </div>
            <form action="{{ route('superadmin.settings.update') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="work_start_time">Jam Masuk Kerja</label>
                                <input type="time" name="work_start_time" id="work_start_time" class="form-control" value="{{ $settings['work_start_time'] ?? '08:00' }}" required>
                                <small class="text-muted">Format 24 jam</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="work_end_time">Jam Pulang Kerja</label>
                                <input type="time" name="work_end_time" id="work_end_time" class="form-control" value="{{ $settings['work_end_time'] ?? '17:00' }}" required>
                                <small class="text-muted">Format 24 jam</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="attendance_radius">Radius Absensi (Meter)</label>
                        <div class="input-group">
                            <input type="number" name="attendance_radius" id="attendance_radius" class="form-control" value="{{ $settings['attendance_radius'] ?? '100' }}" required>
                            <div class="input-group-append">
                                <span class="input-group-text">Meter</span>
                            </div>
                        </div>
                        <small class="text-muted">Jarak maksimal karyawan bisa melakukan absensi dari titik koordinat kantor.</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="office_latitude">Latitude Kantor</label>
                                <input type="text" name="office_latitude" id="office_latitude" class="form-control" value="{{ $settings['office_latitude'] ?? '-6.200000' }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="office_longitude">Longitude Kantor</label>
                                <input type="text" name="office_longitude" id="office_longitude" class="form-control" value="{{ $settings['office_longitude'] ?? '106.816666' }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="late_deduction_amount">Besaran Potongan Keterlambatan</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Rp</span>
                            </div>
                            <input type="number" name="late_deduction_amount" id="late_deduction_amount" class="form-control" value="{{ $settings['late_deduction_amount'] ?? '5000' }}" required>
                        </div>
                        <small class="text-muted">Jumlah potongan yang akan dikurangi dari payroll untuk setiap keterlambatan.</small>
                    </div>
                </div>
                <div class="card-footer text-right">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card bg-gradient-info border-0">
            <div class="card-body">
                <h5><i class="fas fa-info-circle mr-2"></i> Catatan Superadmin</h5>
                <hr class="bg-white opacity-25">
                <p>Pengaturan ini bersifat global dan akan langsung berdampak pada perhitungan:</p>
                <ul>
                    <li>Status kehadiran (Terlambat/Tepat Waktu)</li>
                    <li>Geotagging saat absensi mobile</li>
                    <li>Perhitungan potongan di slip gaji (Payroll)</li>
                </ul>
                <p class="mb-0">Pastikan titik koordinat kantor sesuai dengan lokasi di Google Maps untuk akurasi presensi.</p>
            </div>
        </div>
    </div>
</div>
@endsection
