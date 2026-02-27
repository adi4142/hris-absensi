@extends('layouts.admin')

@section('title', 'Jabatan')
@section('page_title', 'Manajemen Jabatan')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-id-badge mr-2 text-indigo"></i> Struktur Jabatan Karyawan
                    </h3>
                    <div class="card-tools">
                        @php $role = Auth::user()->role ? strtolower(Auth::user()->role->name) : ''; @endphp
                        @if($role == 'superadmin')
                        <a href="{{ route('position.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus-circle mr-1"></i> Tambah Jabatan Baru
                        </a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="bg-light text-muted">
                                <tr>
                                    <th class="text-center" width="70">No</th>
                                    <th>Nama Jabatan</th>
                                    <th>Deskripsi / Keterangan</th>
                                    @if($role == 'superadmin')
                                    <th class="text-center" width="150">Aksi</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($position as $v)
                                <tr>
                                    <td class="text-center font-weight-bold text-muted">{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-indigo-soft rounded p-2 mr-3 border border-indigo shadow-sm" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-user-tag text-indigo"></i>
                                            </div>
                                            <div class="font-weight-bold text-dark">
                                                {{ $v->name }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-muted italic small">{{ $v->description ?: 'Tidak ada deskripsi' }}</td>
                                    @if($role == 'superadmin')
                                    <td class="text-center">
                                        <div class="btn-group shadow-sm rounded overflow-hidden">
                                            <a href="{{ route('position.edit', $v->position_id) }}" class="btn btn-sm btn-outline-warning" title="Edit Jabatan">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('position.destroy', $v->position_id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')" title="Hapus Jabatan">
                                                    <i class="fas fa-trash"></i>
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
    .bg-indigo-soft { background-color: #f5f3ff; }
    .text-indigo { color: #818cf8; }
    .border-indigo { border-color: #e0e7ff !important; }
    .btn-outline-warning { color: #d97706; border-color: #fbbf24; }
    .btn-outline-warning:hover { background-color: #fbbf24; color: white; }
    .btn-outline-danger { color: #dc2626; border-color: #f87171; }
    .btn-outline-danger:hover { background-color: #f87171; color: white; }
</style>
@endsection
