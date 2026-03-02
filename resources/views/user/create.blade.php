@extends('layouts.admin')

@section('title', 'Tambah Pengguna & Karyawan')
@section('page_title', 'Manajemen Pengguna')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm border-0 mb-4" style="border-radius: 15px; overflow: hidden;">
                <div class="card-header bg-white py-3 border-0 d-flex align-items-center">
                    <h3 class="card-title mb-0 font-weight-bold">
                        <i class="fas fa-user-plus mr-2 text-primary"></i> Tambah Pengguna Baru
                    </h3>
                </div>
                <div class="bg-primary" style="height: 3px; width: 100%;"></div>

                <form action="{{ route('user.store') }}" method="POST" novalidate>
                    @csrf
                    <div class="card-body p-4">

                        <h5 class="text-primary font-weight-bold mb-3"><i class="fas fa-id-card mr-2"></i>Informasi Akun Utma</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name">Nama Lengkap</label>
                                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" placeholder="Nama lengkap pengguna" value="{{ old('name') }}" required>
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email">Email Address</label>
                                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" placeholder="example@mail.com" value="{{ old('email') }}" required>
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password">Password</label>
                                <input type="text" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="Password otomatis 8 karakter" required readonly><br>
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                <button type="button" onclick="generatePassword()">
                                    Generate Password
                                </button>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="roles_id">Peran / Role</label>
                                <select name="roles_id" id="roles_id" class="form-control custom-select @error('roles_id') is-invalid @enderror" required>
                                    <option value="">-- Pilih Peran --</option>
                                    @foreach($roles as $role)
                                        @php 
                                            $rName = strtolower($role->name); 
                                            $canSelect = false;
                                            
                                            if (auth()->user()->role->name === 'superadmin') {
                                                $canSelect = true; // Superadmin can select everything
                                            } else {
                                                // Regular Admin can ONLY select HRD and Karyawan
                                                if ($rName !== 'superadmin' && $rName !== 'admin') {
                                                    $canSelect = true;
                                                }
                                            }
                                        @endphp

                                        @if($canSelect)
                                        <option value="{{ $role->roles_id }}" data-role="{{ strtolower($role->name) }}" {{ old('roles_id') == $role->roles_id ? 'selected' : '' }}>
                                            {{ strtoupper($role->name) }}
                                        </option>
                                        @endif
                                    @endforeach
                                </select>
                                @error('roles_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                    
                        <hr class="my-4">
                        <h5 class="text-primary font-weight-bold mb-3"><i class="fas fa-briefcase mr-2"></i>Informasi Karyawan (Opsional)</h5>
                        <p class="text-muted small mb-4">Isi bagian ini jika pengguna adalah karyawan yang akan melakukan absensi.</p>

                        <div class="row" >
                            <div class="col-md-4 mb-3">
                                <label for="nip">NIP (Nomor Induk Pegawai)</label>
                                <input type="text" name="nip" id="nip" class="form-control @error('nip') is-invalid @enderror" placeholder="Contoh: 123456" value="{{ old('nip') }}">
                                @error('nip')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="phone">Nomor Telepon</label>
                                <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" placeholder="08xxxxxxxx" value="{{ old('phone') }}">
                                @error('phone')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="gender">Jenis Kelamin</label>
                                <div class="d-flex">
                                    <div class="custom-control custom-radio mr-3">
                                        <input type="radio" id="genderMale" name="gender" class="custom-control-input @error('gender') is-invalid @enderror" value="Male" {{ old('gender') == 'Male' ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="genderMale">Laki-laki</label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" id="genderFemale" name="gender" class="custom-control-input @error('gender') is-invalid @enderror" value="Female" {{ old('gender') == 'Female' ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="genderFemale">Perempuan</label>
                                    </div>
                                    @error('gender')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="departement_id">Departemen</label>
                                <select name="departement_id" id="departement_id" class="form-control custom-select @error('departement_id') is-invalid @enderror">
                                    <option value="">-- Pilih Departemen --</option>
                                    @foreach($departements as $dept)
                                        <option value="{{ $dept->departement_id }}" {{ old('departement_id') == $dept->departement_id ? 'selected' : '' }}>
                                            {{ $dept->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('departement_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="division_id">Divisi</label>
                                <select name="division_id" id="division_id" class="form-control custom-select @error('division_id') is-invalid @enderror">
                                    <option value="">-- Pilih Divisi --</option>
                                    @foreach($divisions as $div)
                                        <option value="{{ $div->division_id }}" {{ old('division_id') == $div->division_id ? 'selected' : '' }}>
                                            {{ $div->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('division_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="position_id">Jabatan</label>
                                <select name="position_id" id="position_id" class="form-control custom-select @error('position_id') is-invalid @enderror">
                                    <option value="">-- Pilih Jabatan --</option>
                                    @foreach($positions as $pos)
                                        <option value="{{ $pos->position_id }}" {{ old('position_id') == $pos->position_id ? 'selected' : '' }}>
                                            {{ $pos->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('position_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="date_of_birth">Tanggal Lahir</label>
                                <input type="date" name="date_of_birth" id="date_of_birth" class="form-control @error('date_of_birth') is-invalid @enderror" value="{{ old('date_of_birth') }}">
                                @error('date_of_birth')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="address">Alamat Lengkap</label>
                                <textarea name="address" id="address" class="form-control @error('address') is-invalid @enderror" rows="1" placeholder="Alamat sesuai KTP">{{ old('address') }}</textarea>
                                @error('address')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    

                    <div class="card-footer bg-light p-4 d-flex justify-content-between">
                        <a href="{{ route('user.index') }}" class="btn btn-outline-secondary px-4">
                            <i class="fas fa-arrow-left mr-1"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary px-5 shadow-sm">
                            <i class="fas fa-save mr-1"></i> Simpan Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .form-control:focus { border-color: #3b82f6; box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.1); }
    label { color: #4b5563; font-weight: 600; font-size: 0.9rem; }
    
</style>
<script>
    function generatePassword() {
        let characters = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        let password = "";
        let length = 8;

        for (let i = 0; i < length; i++) {
            password += characters.charAt(
                Math.floor(Math.random() * characters.length)
            );
        }

        document.getElementById("password").value = password;
    }
</script>
@endsection