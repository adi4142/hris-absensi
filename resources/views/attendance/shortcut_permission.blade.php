@extends('layouts.shortcut')

@section('title', 'Ajukan Izin')
@section('page_title', 'Administrasi Kehadiran')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card shadow-sm border-0 mb-4 overflow-hidden" style="border-radius: 12px;">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="card-title font-weight-bold mb-0 text-dark">
                        <i class="fas fa-file-signature mr-2 text-primary"></i> Formulir Pengajuan Izin / Sakit
                    </h5>
                </div>
                <div class="bg-primary" style="height: 3px; width: 100%;"></div>
                
                <form id="permissionForm" action="{{ route('attendance.permission.store') }}" method="POST" enctype="multipart/form-data" class="needs-validation">
                    @csrf
                    <div class="card-body p-4">
                        <div class="alert bg-soft-primary border-0 mb-4">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-info-circle fa-2x mr-3 text-primary opacity-50"></i>
                                <span class="small text-dark font-weight-500" id="formInfoText">Silakan lengkapi formulir di bawah ini dengan informasi yang sebenar-benarnya. Pengajuan akan segera diproses oleh tim HR.</span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="small font-weight-bold text-muted text-uppercase mb-2" id="dateLabel">Tanggal Berhenti/Izin</label>
                                <div class="input-group input-group-alternative border rounded-lg overflow-hidden">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light border-0"><i class="fas fa-calendar-alt text-muted"></i></span>
                                    </div>
                                    <input type="date" name="date" id="mainDate" class="form-control border-0" value="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="small font-weight-bold text-muted text-uppercase mb-2">Kategori</label>
                                <div class="input-group input-group-alternative border rounded-lg overflow-hidden">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light border-0"><i class="fas fa-tag text-muted"></i></span>
                                    </div>
                                    <select name="status" id="categorySelect" class="form-control border-0 bg-white">
                                        <option value="Permission">Izin atau Sakit</option>
                                        <option value="Cuti">Ajukan Cuti (Tahunan)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row" id="leaveExtraFields" style="display: none;">
                            <div class="col-md-6 mb-3">
                                <label class="small font-weight-bold text-muted text-uppercase mb-2">Tanggal Berakhir Cuti</label>
                                <div class="input-group input-group-alternative border rounded-lg overflow-hidden">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light border-0"><i class="fas fa-calendar-check text-muted"></i></span>
                                    </div>
                                    <input type="date" name="end_date" id="endDate" class="form-control border-0">
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="p-3 bg-light rounded-lg border">
                                    <small class="text-muted d-block">Sisa Jatah Cuti Anda:</small>
                                    <span class="h6 font-weight-bold text-primary mb-0">{{ Auth::user()->sisa_cuti }} Hari</span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-4">
                            <label class="small font-weight-bold text-muted text-uppercase mb-2" id="descriptionLabel">Alasan & Keterangan Lengkap</label>
                            <textarea name="description" id="descriptionField" class="form-control border rounded-lg p-3" rows="4" placeholder="Contoh: Sakit demam dan butuh istirahat sesuai anjuran dokter..." required style="resize: none;"></textarea>
                        </div>

                        <div class="form-group mb-0">
                            <label class="small font-weight-bold text-muted text-uppercase mb-2">Dokumen Pendukung <span class="text-lowercase font-weight-normal">(Opsional)</span></label>
                            <div class="custom-file-modern border rounded-lg p-3 bg-light d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div class="bg-white rounded-circle p-2 shadow-sm mr-3">
                                        <i class="fas fa-cloud-upload-alt text-primary"></i>
                                    </div>
                                    <div>
                                        <div class="small font-weight-bold text-dark" id="file-name-label">Belum ada file dipilih</div>
                                        <div class="text-muted" style="font-size: 0.65rem;">JPG, PNG, PDF (Maks. 2MB)</div>
                                    </div>
                                </div>
                                <label for="proof_file" class="btn btn-sm btn-white border shadow-sm mb-0 px-3 font-weight-bold">Pilih Berkas</label>
                                <input type="file" name="proof_file" class="d-none" id="proof_file">
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-light border-0 py-3 px-4 d-flex justify-content-between align-items-center">
                        <a href="{{ route('attendance.scan') }}" class="btn btn-link text-muted font-weight-bold">
                            <i class="fas fa-times mr-1"></i> Batalkan
                        </a>
                        <button type="submit" class="btn btn-primary px-5 shadow-sm font-weight-bold">
                            Kirim Pengajuan <i class="fas fa-paper-plane ml-2 small"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-4 d-none d-lg-block">
            <div class="card shadow-sm border-0 mb-4 bg-gradient-info text-white" style="border-radius: 12px;">
                <div class="card-body p-4">
                    <h5 class="font-weight-bold mb-3">Informasi Izin</h5>
                    <ul class="list-unstyled small mb-0">
                        <li class="mb-3 d-flex align-items-start">
                            <i class="fas fa-check-circle mr-2 mt-1 opacity-75"></i>
                            <span>Pastikan tanggal yang dipilih sudah benar.</span>
                        </li>
                        <li class="mb-3 d-flex align-items-start">
                            <i class="fas fa-check-circle mr-2 mt-1 opacity-75"></i>
                            <span>Sertakan bukti foto atau surat dokter untuk mempercepat proses verifikasi.</span>
                        </li>
                        <li class="d-flex align-items-start">
                            <i class="fas fa-check-circle mr-2 mt-1 opacity-75"></i>
                            <span>Status kehadiran akan diperbarui secara otomatis setelah disetujui HRD.</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-soft-primary { background-color: #f0f9ff; color: #0369a1; }
    .bg-gradient-info { background: linear-gradient(135deg, #0ea5e9 0%, #0369a1 100%); }
    .input-group-alternative { transition: all 0.2s; }
    .input-group-alternative:focus-within { border-color: #4f46e5 !important; box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1); }
    .font-weight-500 { font-weight: 500; }
    .custom-file-modern { transition: all 0.2s; border: 1px dashed #cbd5e1 !important; }
    .custom-file-modern:hover { background-color: #f8fafc !important; border-color: #4f46e5 !important; }
    .btn-white { background-color: #fff; color: #475569; }
    .letter-spacing-1 { letter-spacing: 0.5px; }
</style>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        const categorySelect = $('#categorySelect');
        const leaveExtraFields = $('#leaveExtraFields');
        const permissionForm = $('#permissionForm');
        const dateLabel = $('#dateLabel');
        const descriptionLabel = $('#descriptionLabel');
        const mainDateInput = $('#mainDate');
        const endDateInput = $('#endDate');
        const descriptionField = $('#descriptionField');
        const formInfoText = $('#formInfoText');

        const ROUTE_PERMISSION = "{{ route('attendance.permission.store') }}";
        const ROUTE_LEAVE = "{{ route('leave.store') }}";

        categorySelect.on('change', function() {
            if ($(this).val() === 'Cuti') {
                // Switch to Leave Mode
                permissionForm.attr('action', ROUTE_LEAVE);
                leaveExtraFields.fadeIn();
                dateLabel.text('Tanggal Mulai Cuti');
                descriptionLabel.text('Alasan Cuti');
                formInfoText.text('Anda sedang mengajukan cuti tahunan. Pastikan sisa jatah cuti mencukupi.');
                
                // Change input names to match LeaveController@store
                mainDateInput.attr('name', 'start_date');
                endDateInput.attr('name', 'end_date').attr('required', true);
                descriptionField.attr('name', 'reason');
                
                // Set min date for end date
                endDateInput.attr('min', mainDateInput.val());
            } else {
                // Switch to Permission Mode
                permissionForm.attr('action', ROUTE_PERMISSION);
                leaveExtraFields.fadeOut();
                dateLabel.text('Tanggal Berhenti/Izin');
                descriptionLabel.text('Alasan & Keterangan Lengkap');
                formInfoText.text('Silakan lengkapi formulir di bawah ini dengan informasi yang sebenar-benarnya. Pengajuan akan segera diproses oleh tim HR.');

                // Restore input names for AttendanceController@storePermission
                mainDateInput.attr('name', 'date');
                endDateInput.attr('name', 'end_date').attr('required', false);
                descriptionField.attr('name', 'description');
            }
        });

        mainDateInput.on('change', function() {
            if (categorySelect.val() === 'Cuti') {
                endDateInput.attr('min', $(this).val());
                if (endDateInput.val() && endDateInput.val() < $(this).val()) {
                    endDateInput.val($(this).val());
                }
            }
        });

        $('#proof_file').on('change', function() {
            var fileName = $(this).val().split('\\').pop();
            if (fileName) {
                $('#file-name-label').text(fileName).addClass('text-primary');
            } else {
                $('#file-name-label').text('Belum ada file dipilih').removeClass('text-primary');
            }
        });
    });
</script>
@endpush