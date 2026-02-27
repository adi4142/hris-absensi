@extends('layouts.admin')

@section('title', 'Manajemen Pengguna & Karyawan')
@section('page_title', 'Database Anggota Sistem')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0" style="border-radius: 12px; overflow: hidden;">
                <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                    <h3 class="card-title mb-0 font-weight-bold">
                        <i class="fas fa-users-shield mr-2 text-primary"></i> Daftar Seluruh Anggota
                    </h3>
                    <div class="card-tools">
                        @php $role = Auth::user()->role ? strtolower(Auth::user()->role->name) : ''; @endphp
                        @if($role == 'superadmin')
                        <a href="{{ route('user.create') }}" class="btn btn-primary px-4 shadow-sm">
                            <i class="fas fa-user-plus mr-1"></i> Tambah Anggota Baru
                        </a>
                        @endif
                    </div>
                </div>
                <div class="card-body p-0">
                    @if(session('success'))
                        <div class="alert alert-success border-0 rounded-0 mb-0">
                            <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
                        </div>
                    @endif
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="bg-light text-muted small text-uppercase">
                                <tr>
                                    <th class="text-center py-3" width="60">No</th>
                                    <th class="py-3">Informasi Profil</th>
                                    <th class="py-3">Status Karyawan</th>
                                    <th class="py-3">Penempatan</th>
                                    <th class="py-3 text-center">Hak Akses</th>
                                    <th class="py-3 text-right">Gaji Pokok</th>
                                    @if($role == 'superadmin')
                                    <th class="py-3 text-center" width="150">Aksi</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $v)
                                <tr>
                                    <td class="text-center font-weight-bold text-muted">{{ $loop->iteration }}</td>
                                    <td class="py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary-soft rounded-circle p-2 mr-3 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                                <i class="fas fa-user {{ $v->nip ? 'text-primary' : 'text-secondary' }}"></i>
                                            </div>
                                            <div>
                                                <div class="font-weight-bold text-dark">{{ $v->name }}</div>
                                                <div class="small text-muted">{{ $v->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        @if($v->nip)
                                            <div class="font-weight-bold text-primary">{{ $v->nip }}</div>
                                            <small class="badge badge-light border"><i class="fas fa-fingerprint mr-1"></i> {{ $v->attendance_code }}</small>
                                        @else
                                            <span class="text-muted small italic">Bukan Karyawan</span>
                                        @endif
                                    </td>
                                    <td class="py-3">
                                        @if($v->nip)
                                            <div class="small text-dark font-weight-bold">{{ $v->departement->name ?? '-' }}</div>
                                            <div class="small text-muted text-truncate" style="max-width: 150px;">{{ $v->position->name ?? '-' }}</div>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-center py-3">
                                        @php
                                            $roleName = strtolower($v->role->name ?? '');
                                            $roleColor = 'badge-secondary';
                                            if($roleName == 'superadmin') $roleColor = 'badge-dark';
                                            elseif($roleName == 'admin') $roleColor = 'badge-danger';
                                            elseif($roleName == 'hrd') $roleColor = 'badge-primary';
                                            elseif($roleName == 'karyawan') $roleColor = 'badge-success';
                                        @endphp
                                        <span class="badge {{ $roleColor }} px-3 py-2 text-uppercase" style="letter-spacing: 0.5px;">
                                            {{ $v->role->name ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="text-right py-3 font-weight-bold">
                                        @if($v->nip)
                                            Rp {{ number_format($v->basic_salary, 0, ',', '.') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    @if($role == 'superadmin')
                                    <td class="text-center py-3">
                                        <div class="btn-group shadow-sm border rounded">
                                            <a href="{{ route('user.edit', $v->nip) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                            <form action="{{ route('user.destroy', $v->nip) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" onclick="return confirm('Hapus pengguna ini?')" class="btn btn-sm btn-outline-danger" title="Delete">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-primary-soft { background-color: #eff6ff; }
    .table td { vertical-align: middle; }
    .btn-outline-warning { color: #d97706; border: none; border-right: 1px solid #dee2e6 !important; }
    .btn-outline-warning:hover { background-color: #fffbeb; color: #d97706; }
    .btn-outline-danger { color: #dc2626; border: none; }
    .btn-outline-danger:hover { background-color: #fef2f2; color: #dc2626; }
</style>
@endsection
