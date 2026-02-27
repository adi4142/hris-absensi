@extends('layouts.admin')

@section('title', 'Pengaturan Cuti')
@section('page_title', 'Konfigurasi Sistem Cuti')

@section('content')
<div class="row">
    <div class="col-md-6">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white">
                <h3 class="card-title"><i class="fas fa-cog mr-2 text-primary"></i> Aturan Cuti Tahunan</h3>
            </div>
            <form action="{{ route('superadmin.leave_settings.update') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label for="annual_allowance">Jatah Tahunan Default (Hari)</label>
                        <div class="input-group">
                            <input type="number" name="annual_allowance" id="annual_allowance" class="form-control" value="{{ $setting->annual_allowance }}" required min="0">
                            <div class="input-group-append">
                                <span class="input-group-text">Hari / Tahun</span>
                            </div>
                        </div>
                        <small class="form-text text-muted">Jumlah hari cuti yang diberikan kepada karyawan setiap tahunnya.</small>
                    </div>

                    <div class="form-group">
                        <label for="max_days_per_request">Maksimal Hari Per Pengajuan</label>
                        <div class="input-group">
                            <input type="number" name="max_days_per_request" id="max_days_per_request" class="form-control" value="{{ $setting->max_days_per_request }}" required min="1">
                            <div class="input-group-append">
                                <span class="input-group-text">Hari</span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mt-4">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="can_carry_over" name="can_carry_over" {{ $setting->can_carry_over ? 'checked' : '' }}>
                            <label class="custom-control-label" for="can_carry_over">Izinkan Carry Over (Sisa cuti pindah ke tahun depan)</label>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="alert alert-warning border-0">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="update_employees" name="update_employees">
                            <label class="custom-control-label font-weight-bold" for="update_employees">Perbarui Jatah Cuti Masal</label>
                        </div>
                        <small class="d-block mt-2">Centang ini jika ingin mereset/memperbarui `total_jatah_cuti` semua karyawan aktif sesuai angka di atas.</small>
                    </div>
                </div>
                <div class="card-footer bg-white text-right">
                    <button type="submit" class="btn btn-primary px-4 shadow-sm">Simpan Pengaturan</button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card shadow-sm border-0 border-left-primary">
            <div class="card-header bg-white">
                <h3 class="card-title text-info"><i class="fas fa-info-circle mr-2"></i> Informasi Sistem Cuti</h3>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item px-0">
                        <strong>Konsep Jalur Cuti:</strong><br>
                        <small class="text-muted">Karyawan Mengajukan &rarr; Sistem Validasi Sisa &rarr; HRD/Admin Approve/Reject.</small>
                    </li>
                    <li class="list-group-item px-0">
                        <strong>Kapan Jatah Berkurang?</strong><br>
                        <small class="text-muted">Hanya saat pengajuan mendapatkan status <strong>APPROVED</strong> oleh HRD.</small>
                    </li>
                    <li class="list-group-item px-0">
                        <strong>Otomatisasi:</strong><br>
                        <small class="text-muted">Sistem akan secara otomatis menolak pengajuan jika sisa cuti (Sisa Cuti = Total Jatah - Terpakai) tidak mencukupi.</small>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
