@extends('layouts.admin')

@section('title', 'Profil Saya')
@section('page_title', 'Profil Informasi')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-4">
            <!-- Profile Card -->
            <div class="card shadow-sm border-0 mb-4 overflow-hidden">
                <div class="bg-gradient-primary py-5 text-center position-relative">
                    <div class="position-absolute w-100 h-100 top-0 left-0 opacity-25" style="background: url('https://www.transparenttextures.com/patterns/cubes.png');"></div>
                    <img class="profile-user-img img-fluid rounded-circle border-4 border-white shadow-lg mb-3"
                         src="{{ $user->photo ? asset('storage/profiles/' . $user->photo) : asset('AdminLTE/dist/img/user2-160x160.jpg') }}"
                         alt="User profile picture" style="width: 120px; height: 120px; object-fit: cover;">
                    <h4 class="text-white font-weight-bold mb-0">{{ $user->name }}</h4>
                    <p class="text-white-50 small mb-0">{{ $user->position->name ?? ($user->role->name ?? 'User') }}</p>
                </div>
                <div class="card-body pt-4">
                    <ul class="list-group list-group-flush small">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 border-top-0">
                            <span class="text-muted"><i class="far fa-envelope mr-2"></i> Surel</span>
                            <span class="font-weight-bold text-dark">{{ $user->email }}</span>
                        </li>
                        @if($user->employee)
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted"><i class="fas fa-id-badge mr-2"></i> Nomor Induk</span>
                            <span class="font-weight-bold text-dark">{{ $user->nip }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted"><i class="fas fa-phone mr-2"></i> WhatsApp</span>
                            <span class="font-weight-bold text-dark">{{ $user->phone }}</span>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>

            <!-- About Card -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom-0 py-3">
                    <h6 class="card-title font-weight-bold text-primary mb-0">Atribut Kepegawaian</h6>
                </div>
                <div class="card-body pt-0 pb-4">
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Status Sistem</small>
                        <span class="badge badge-primary px-3 py-2 shadow-sm text-uppercase letter-spacing-1">
                            {{ $user->role->name ?? 'Non-Role' }}
                        </span>
                    </div>

                    @if($user->employee)
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Departemen & Divisi</small>
                        <p class="text-dark font-weight-500 mb-0">
                            {{ $user->employee->departement->name ?? '-' }} / {{ $user->employee->division->name ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <small class="text-muted d-block mb-1">Terdaftar Sejak</small>
                        <p class="text-dark font-weight-500 mb-0">
                            {{ $user->created_at ? $user->created_at->translatedFormat('d F Y') : '-' }}
                        </p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white p-2">
                    <ul class="nav nav-pills custom-pills">
                        <li class="nav-item">
                            <a class="nav-link active px-4 py-3" href="#details" data-toggle="tab">
                                <i class="fas fa-user-circle mr-2"></i> Biodata Lengkap
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-4 py-3" href="#edit_profile" data-toggle="tab">
                                <i class="fas fa-user-edit mr-2"></i> Edit Profil
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-4 py-3" href="#settings" data-toggle="tab">
                                <i class="fas fa-key mr-2"></i> Keamanan Akun
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content pt-3">
                        <div class="active tab-pane fade show" id="details">
                            <div class="row gx-5">
                                <div class="col-12 mb-4">
                                    <h6 class="text-uppercase text-primary font-weight-bold small letter-spacing-1 mb-4 border-bottom pb-2">Identitas Personal</h6>
                                    
                                    <div class="row mb-3">
                                        <div class="col-sm-4 text-muted">Nama Lengkap</div>
                                        <div class="col-sm-8 font-weight-bold text-dark">{{ $user->name }}</div>
                                    </div>
                                    
                                    <div class="row mb-3">
                                        <div class="col-sm-4 text-muted">ID Email Terdaftar</div>
                                        <div class="col-sm-8 font-weight-bold text-dark">{{ $user->email }}</div>
                                    </div>

                                    @if($user->employee)
                                        <div class="row mb-3">
                                            <div class="col-sm-4 text-muted">ID Karyawan (NIP)</div>
                                            <div class="col-sm-8 font-weight-bold text-dark">{{ $user->nip }}</div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-sm-4 text-muted">Tanggal Kelahiran</div>
                                            <div class="col-sm-8 font-weight-bold text-dark">{{ $user->date_of_birth ? \Carbon\Carbon::parse($user->date_of_birth)->translatedFormat('d F Y') : '-' }}</div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-sm-4 text-muted">Jenis Kelamin</div>
                                            <div class="col-sm-8 font-weight-bold text-dark">{{ $user->gender }}</div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-sm-4 text-muted">Alamat Domisili</div>
                                            <div class="col-sm-8 font-weight-bold text-dark">{{ $user->address ?? '-' }}</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="edit_profile">
                            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="form-group row mb-4">
                                    <label for="name" class="col-sm-3 col-form-label text-muted">Nama Lengkap</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                    </div>
                                </div>
                                <div class="form-group row mb-4">
                                    <label for="email" class="col-sm-3 col-form-label text-muted">Surel (Email)</label>
                                    <div class="col-sm-9">
                                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                    </div>
                                </div>
                                <div class="form-group row mb-4">
                                    <label for="photo" class="col-sm-3 col-form-label text-muted">Foto Profil</label>
                                    <div class="col-sm-9">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="photo" name="photo">
                                            <label class="custom-file-label" for="photo">Pilih foto...</label>
                                        </div>
                                        <small class="text-muted">Format: JPG, PNG. Max: 2MB</small>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="offset-sm-3 col-sm-9">
                                        <button type="submit" class="btn btn-primary px-4 shadow-sm">
                                            <i class="fas fa-save mr-2"></i> Simpan Perubahan
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="tab-pane fade" id="settings">
                            <div class="alert alert-soft-info border-0 mb-4">
                                <i class="fas fa-shield-alt mr-2 text-info"></i> Gunakan password yang kuat dengan minimal 8 karakter.
                            </div>

                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible border-0 shadow-sm fade show" role="alert">
                                    <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            <form class="form-horizontal px-md-3" action="{{ route('profile.password') }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="form-group row mb-4">
                                    <label for="current_password" class="col-sm-3 col-form-label text-muted">Password Lama</label>
                                    <div class="col-sm-9">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-light border-right-0"><i class="fas fa-lock"></i></span>
                                            </div>
                                            <input type="password" class="form-control border-left-0" id="current_password" name="current_password" placeholder="Masukkan password saat ini" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row mb-4">
                                    <label for="new_password" class="col-sm-3 col-form-label text-muted">Password Baru</label>
                                    <div class="col-sm-9">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-light border-right-0"><i class="fas fa-key"></i></span>
                                            </div>
                                            <input type="password" class="form-control border-left-0 @error('new_password') is-invalid @enderror" id="new_password" name="new_password" placeholder="Masukkan password baru" required>
                                        </div>
                                        @error('new_password') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                </div>
                                <div class="form-group row mb-4">
                                    <label for="new_password_confirmation" class="col-sm-3 col-form-label text-muted">Ulangi Password</label>
                                    <div class="col-sm-9">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-light border-right-0"><i class="fas fa-check-double"></i></span>
                                            </div>
                                            <input type="password" class="form-control border-left-0" id="new_password_confirmation" name="new_password_confirmation" placeholder="Konfirmasi password baru" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="offset-sm-3 col-sm-9">
                                        <button type="submit" class="btn btn-primary px-4 shadow-sm">
                                            <i class="fas fa-save mr-2"></i> Perbarui Password
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-gradient-primary { background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%); }
    .border-4 { border-width: 4px !important; }
    .font-weight-500 { font-weight: 500; }
    .letter-spacing-1 { letter-spacing: 0.5px; }
    .custom-pills .nav-link { border-radius: 0; color: #6b7280; font-weight: 600; border-bottom: 3px solid transparent; }
    .custom-pills .nav-link.active { background: transparent !important; color: #4f46e5 !important; border-bottom-color: #4f46e5; }
    .alert-soft-info { background-color: #f0f9ff; color: #0369a1; }
    .top-0 { top: 0; }
    .left-0 { left: 0; }
</style>
@push('scripts')
<script src="{{ asset('AdminLTE/plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>
<script>
$(function () {
  bsCustomFileInput.init();
});
</script>
@endpush
@endsection

