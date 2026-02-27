@extends('layouts.admin')

@section('title', 'Role')
@section('page_title', 'Manajemen Peran (Role)')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-user-tag mr-2 text-primary"></i> Daftar Peran Sistem
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('role.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus-circle mr-1"></i> Tambah Peran Baru
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-center" width="70">No</th>
                                    <th>Nama Peran</th>
                                    <th>Deskripsi</th>
                                    <th class="text-center" width="200">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($roles as $v)
                                <tr>
                                    <td class="text-center font-weight-bold text-muted">{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary-soft rounded p-2 mr-3">
                                                <i class="fas fa-shield-alt text-primary"></i>
                                            </div>
                                            <div class="font-weight-bold text-dark text-uppercase letter-spacing-1">
                                                {{ $v->name }}
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $v->description }}</td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <a href="{{ route('role.edit', $v->roles_id) }}" class="btn btn-sm btn-outline-warning mr-1">
                                                <i class="fas fa-edit mr-1"></i> Edit
                                            </a>
                                            <form action="{{ route('role.destroy', $v->roles_id) }}" method="POST" class="d-inline">
                                                {{ csrf_field() }}
                                                @method('DELETE')
                                                <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menghapus peran ini?')" class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-trash mr-1"></i> Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
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
    .bg-primary-soft { background-color: #eef2ff; }
    .letter-spacing-1 { letter-spacing: 0.5px; }
    .btn-outline-warning { border-color: #f59e0b; color: #d97706; }
    .btn-outline-warning:hover { background-color: #f59e0b; color: white; }
    .btn-outline-danger { border-color: #ef4444; color: #dc2626; }
    .btn-outline-danger:hover { background-color: #ef4444; color: white; }
</style>
@endsection
