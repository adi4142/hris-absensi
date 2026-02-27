@extends('layouts.admin')

@section('title', 'Departemen')
@section('page_title', 'Manajemen Departemen')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-building mr-2 text-info"></i> Daftar Departemen Organisasi
                    </h3>
                    <div class="card-tools">
                        @php $role = Auth::user()->role ? strtolower(Auth::user()->role->name) : ''; @endphp
                        @if($role == 'superadmin')
                        <a href="{{ route('departement.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus-circle mr-1"></i> Tambah Departemen
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
                                    <th>Nama Departemen</th>
                                    <th>Deskripsi / Keterangan</th>
                                    @if($role == 'superadmin')
                                    <th class="text-center" width="150">Aksi</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($departement as $v)
                                <tr>
                                    <td class="text-center font-weight-bold text-muted">{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-info-soft rounded p-2 mr-3 border border-info shadow-sm" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-hotel text-info"></i>
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
                                            <a href="{{ route('departement.edit', $v->departement_id) }}" class="btn btn-sm btn-outline-warning" title="Edit Departemen">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('departement.destroy', $v->departement_id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')" title="Hapus Departemen">
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
    .bg-info-soft { background-color: #ecfeff; }
    .btn-outline-warning { color: #d97706; border-color: #fbbf24; }
    .btn-outline-warning:hover { background-color: #fbbf24; color: white; }
    .btn-outline-danger { color: #dc2626; border-color: #f87171; }
    .btn-outline-danger:hover { background-color: #f87171; color: white; }
</style>
@endsection
