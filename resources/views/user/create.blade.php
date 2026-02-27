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

                <form action="{{ route('user.store') }}" method="POST">
                    @csrf
                    <div class="card-body p-4">
                        @if ($errors->any())
                            <div class="alert alert-danger border-0 shadow-sm mb-4">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <h5 class="text-primary font-weight-bold mb-3"><i class="fas fa-id-card mr-2"></i>Informasi Akun Utma</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name">Nama Lengkap</label>
                                <input type="text" name="name" id="name" class="form-control" placeholder="Nama lengkap pengguna" value="{{ old('name') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email">Email Address</label>
                                <input type="email" name="email" id="email" class="form-control" placeholder="example@mail.com" value="{{ old('email') }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password">Password</label>
                                <input type="password" name="password" id="password" class="form-control" placeholder="Minimal 8 karakter" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="roles_id">Peran / Role</label>
                                <select name="roles_id" id="roles_id" class="form-control custom-select" required>
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
                                        <option value="{{ $role->roles_id }}" {{ old('roles_id') == $role->roles_id ? 'selected' : '' }}>
                                            {{ strtoupper($role->name) }}
                                        </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <hr class="my-4">
                        <h5 class="text-primary font-weight-bold mb-3"><i class="fas fa-briefcase mr-2"></i>Informasi Karyawan (Opsional)</h5>
                        <p class="text-muted small mb-4">Isi bagian ini jika pengguna adalah karyawan yang akan melakukan absensi.</p>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="nip">NIP (Nomor Induk Pegawai)</label>
                                <input type="text" name="nip" id="nip" class="form-control" placeholder="Contoh: 123456" value="{{ old('nip') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="phone">Nomor Telepon</label>
                                <input type="text" name="phone" id="phone" class="form-control" placeholder="08xxxxxxxx" value="{{ old('phone') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="gender">Jenis Kelamin</label>
                                <select name="gender" id="gender" class="form-control custom-select">
                                    <option value="">-- Pilih --</option>
                                    <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="departement_id">Departemen</label>
                                <select name="departement_id" id="departement_id" class="form-control custom-select">
                                    <option value="">-- Pilih Departemen --</option>
                                    @foreach($departements as $dept)
                                        <option value="{{ $dept->departement_id }}" {{ old('departement_id') == $dept->departement_id ? 'selected' : '' }}>
                                            {{ $dept->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="division_id">Divisi</label>
                                <select name="division_id" id="division_id" class="form-control custom-select">
                                    <option value="">-- Pilih Divisi --</option>
                                    @foreach($divisions as $div)
                                        <option value="{{ $div->division_id }}" {{ old('division_id') == $div->division_id ? 'selected' : '' }}>
                                            {{ $div->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="position_id">Jabatan</label>
                                <select name="position_id" id="position_id" class="form-control custom-select">
                                    <option value="">-- Pilih Jabatan --</option>
                                    @foreach($positions as $pos)
                                        <option value="{{ $pos->position_id }}" {{ old('position_id') == $pos->position_id ? 'selected' : '' }}>
                                            {{ $pos->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="date_of_birth">Tanggal Lahir</label>
                                <input type="date" name="date_of_birth" id="date_of_birth" class="form-control" value="{{ old('date_of_birth') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="basic_salary">Gaji Pokok (Rp)</label>
                                <input type="number" name="basic_salary" id="basic_salary" class="form-control" placeholder="Contoh: 5000000" value="{{ old('basic_salary') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="address">Alamat Lengkap</label>
                                <textarea name="address" id="address" class="form-control" rows="1" placeholder="Alamat sesuai KTP">{{ old('address') }}</textarea>
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
@endsection