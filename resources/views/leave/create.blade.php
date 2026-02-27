@extends(in_array(strtolower(Auth::user()->role->name ?? ''), ['superadmin', 'hrd', 'admin']) ? 'layouts.admin' : 'layouts.karyawan')

@section('title', 'Ajukan Cuti')
@section('page_title', 'Form Pengajuan Cuti')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white">
                <h3 class="card-title"><i class="fas fa-paper-plane mr-2 text-primary"></i> Isi Data Pengajuan</h3>
            </div>
            <form action="{{ route('leave.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="start_date">Tanggal Mulai <span class="text-danger">*</span></label>
                                <input type="date" name="start_date" id="start_date" class="form-control @error('start_date') is-invalid @enderror" value="{{ old('start_date') }}" required>
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="end_date">Tanggal Selesai <span class="text-danger">*</span></label>
                                <input type="date" name="end_date" id="end_date" class="form-control @error('end_date') is-invalid @enderror" value="{{ old('end_date') }}" required>
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info border-0 shadow-none py-2">
                        <small><i class="fas fa-info-circle mr-1"></i> Durasi cuti otomatis dihitung termasuk hari libur (jika ada). Pastikan tanggal yang dipilih benar.</small>
                    </div>

                    <div class="form-group">
                        <label for="reason">Alasan Cuti <span class="text-danger">*</span></label>
                        <textarea name="reason" id="reason" class="form-control @error('reason') is-invalid @enderror" rows="4" placeholder="Contoh: Acara keluarga di luar kota" required>{{ old('reason') }}</textarea>
                        @error('reason')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="card bg-light border-0 mt-4">
                        <div class="card-body py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="text-muted d-block small">Sisa Cuti Saat Ini</span>
                                    <span class="font-weight-bold h5 mb-0">{{ Auth::user()->sisa_cuti }} Hari</span>
                                </div>
                                <div class="text-right">
                                    <span class="text-muted d-block small">Status Pengajuan</span>
                                    <span class="badge badge-warning">PENDING (Menunggu)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white text-right">
                    <a href="{{ route('leave.index') }}" class="btn btn-light px-4 mr-2">Batal</a>
                    <button type="submit" class="btn btn-primary px-4">Kirim Pengajuan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const startDate = document.getElementById('start_date');
        const endDate = document.getElementById('end_date');

        startDate.addEventListener('change', function() {
            if (endDate.value && endDate.value < startDate.value) {
                endDate.value = startDate.value;
            }
            endDate.setAttribute('min', startDate.value);
        });
    });
</script>
@endpush
