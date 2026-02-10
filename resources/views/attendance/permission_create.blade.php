@extends('layouts.karyawan')

@section('title', 'Ajukan Izin')
@section('page_title', 'Form Pengajuan Izin')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Formulir Izin / Sakit</h3>
            </div>
            <form action="{{ route('attendance.permission.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label>Tanggal Izin</label>
                        <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Jenis Pengajuan</label>
                        <select name="status" class="form-control" readonly>
                            <option value="Permission">Izin / Sakit</option>
                        </select>
                        <small class="text-muted">Untuk saat ini hanya mendukung pengajuan Izin atau Sakit.</small>
                    </div>

                    <div class="form-group">
                        <label>Keterangan / Alasan</label>
                        <textarea name="description" class="form-control" rows="4" placeholder="Jelaskan alasan izin anda..." required></textarea>
                    </div>

                    <div class="form-group">
                        <label>Bukti (Surat Dokter / Foto)</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" name="proof_file" class="custom-file-input" id="proof_file">
                                <label class="custom-file-label" for="proof_file">Pilih file</label>
                            </div>
                        </div>
                        <small class="text-muted">Format: JPG, PNG, PDF. Maks: 2MB.</small>
                    </div>
                </div>

                <div class="card-footer text-right">
                    <a href="{{ route('attendance.dashboard') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Kirim Pengajuan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Custom file input label script
    $(".custom-file-input").on("change", function() {
        var fileName = $(this).val().split("\\").pop();
        $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
    });
</script>
@endpush
