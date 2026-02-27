@extends('layouts.admin')

@section('title', 'Edit Pengguna')
@section('page_title', 'Manajemen Pengguna')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm border-0 mb-4" style="border-radius: 15px; overflow: hidden;">
                <div class="card-header bg-white py-4 border-0 d-flex align-items-center justify-content-between">
                    <h3 class="card-title mb-0 font-weight-bold">
                        <i class="fas fa-user-edit mr-2 text-primary"></i> Edit Profil: {{ $edituser->name }}
                    </h3>
                    @if($edituser->attendance_code)
                        <span class="badge badge-primary px-3 py-2" style="font-size: 0.8rem;">
                            <i class="fas fa-key mr-1"></i> CODE: {{ $edituser->attendance_code }}
                        </span>
                    @endif
                </div>
                <div class="bg-primary" style="height: 3px; width: 100%;"></div>

                <form action="{{ route('user.update', $edituser->nip) }}" method="POST">
                    @csrf
                    @method('PUT')
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

                        @if(session('success'))
                            <div class="alert alert-success border-0 shadow-sm mb-4">
                                <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                            </div>
                        @endif

                        <h5 class="text-primary font-weight-bold mb-3"><i class="fas fa-id-card mr-2"></i>Informasi Akun Utama</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name">Nama Lengkap</label>
                                <input type="text" name="name" id="name" class="form-control" placeholder="Nama lengkap pengguna" value="{{ old('name', $edituser->name) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email">Email Address</label>
                                <input type="email" name="email" id="email" class="form-control" placeholder="example@mail.com" value="{{ old('email', $edituser->email) }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password">Password Baru (Kosongkan jika tidak diubah)</label>
                                <input type="password" name="password" id="password" class="form-control" placeholder="Minimal 8 karakter">
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
                                                // Regular Admin can ONLY select HRD and Karyawan (and cannot see/edit Admin/Superadmin roles)
                                                if ($rName !== 'superadmin' && $rName !== 'admin') {
                                                    $canSelect = true;
                                                }
                                            }
                                        @endphp

                                        @if($canSelect)
                                        <option value="{{ $role->roles_id }}" {{ old('roles_id', $edituser->roles_id) == $role->roles_id ? 'selected' : '' }}>
                                            {{ strtoupper($role->name) }}
                                        </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <hr class="my-4">
                        <h5 class="text-primary font-weight-bold mb-3"><i class="fas fa-briefcase mr-2"></i>Informasi Karyawan</h5>
                        <p class="text-muted small mb-4">Ubah detail karyawan di bawah ini.</p>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="nip">NIP (Nomor Induk Pegawai)</label>
                                <input type="text" name="nip" id="nip" class="form-control" placeholder="Contoh: 123456" value="{{ old('nip', $edituser->nip) }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="phone">Nomor Telepon</label>
                                <input type="text" name="phone" id="phone" class="form-control" placeholder="08xxxxxxxx" value="{{ old('phone', $edituser->phone) }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="gender">Jenis Kelamin</label>
                                <select name="gender" id="gender" class="form-control custom-select">
                                    <option value="">-- Pilih --</option>
                                    <option value="Male" {{ old('gender', $edituser->gender) == 'Male' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="Female" {{ old('gender', $edituser->gender) == 'Female' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="departement_id">Departemen</label>
                                <select name="departement_id" id="departement_id" class="form-control custom-select">
                                    <option value="">-- Pilih Departemen --</option>
                                    @foreach($departements as $dept)
                                        <option value="{{ $dept->departement_id }}" {{ old('departement_id', $edituser->departement_id) == $dept->departement_id ? 'selected' : '' }}>
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
                                        <option value="{{ $div->division_id }}" {{ old('division_id', $edituser->division_id) == $div->division_id ? 'selected' : '' }}>
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
                                        <option value="{{ $pos->position_id }}" {{ old('position_id', $edituser->position_id) == $pos->position_id ? 'selected' : '' }}>
                                            {{ $pos->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="date_of_birth">Tanggal Lahir</label>
                                <input type="date" name="date_of_birth" id="date_of_birth" class="form-control" value="{{ old('date_of_birth', $edituser->date_of_birth) }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="basic_salary">Gaji Pokok (Rp)</label>
                                <input type="number" name="basic_salary" id="basic_salary" class="form-control" placeholder="Contoh: 5000000" value="{{ old('basic_salary', $edituser->basic_salary) }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="address">Alamat Lengkap</label>
                                <textarea name="address" id="address" class="form-control" rows="1" placeholder="Alamat sesuai KTP">{{ old('address', $edituser->address) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-light p-4 d-flex justify-content-between">
                        <a href="{{ route('user.index') }}" class="btn btn-outline-secondary px-4">
                            <i class="fas fa-arrow-left mr-1"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary px-5 shadow-sm">
                            <i class="fas fa-save mr-1"></i> Simpan Perubahan
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
