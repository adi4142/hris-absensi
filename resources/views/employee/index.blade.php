@extends('layouts.admin')

@section('title', 'Karyawan')
@section('page_title', 'Data Karyawan')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-users-cog mr-2 text-primary"></i> Manajemen Data Karyawan
                    </h3>
                    <div class="card-tools">
                        @php $role = Auth::user()->role ? strtolower(Auth::user()->role->name) : ''; @endphp
                        @if($role == 'hrd' || $role == 'admin' || $role == 'superadmin')
                        <a href="{{ route('employee.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus-circle mr-1"></i> Tambah Karyawan Baru
                        </a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                            <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th class="text-center" width="50">No</th>
                                    <th>Informasi Karyawan</th>
                                    <th>Akun Sistem</th>
                                    <th>Penempatan</th>
                                    <th>Jabatan</th>
                                    @if($role == 'hrd' || $role == 'admin' || $role == 'superadmin')
                                    <th class="text-center">Aksi</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($employee as $v)
                                <tr>
                                    <td class="text-center font-weight-bold text-muted">{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="mr-3">
                                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px;">
                                                    <i class="fas fa-user text-primary"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="font-weight-bold text-dark">{{ $v->name }}</div>
                                                <small class="text-muted"><i class="fas fa-id-card mr-1"></i> {{ $v->nip }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-dark">{{ $v->user->name ?? '-' }}</div>
                                        <small class="text-primary">{{ $v->email }}</small>
                                    </td>
                                    <td>
                                        <span class="badge badge-light border text-dark">
                                            <i class="fas fa-building mr-1 text-info"></i> {{ $v->departement->name ?? '-' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-info-soft">
                                            {{ $v->position->name ?? '-' }}
                                        </span>
                                    </td>
                                    @if($role == 'hrd' || $role == 'admin' || $role == 'superadmin')
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <a href="{{ route('employee.edit', $v->nip) }}" class="btn btn-sm btn-outline-warning mr-1" title="Ubah Data">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('employee.destroy', $v->nip) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')" title="Hapus Data">
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
    .badge-info-soft {
        background-color: #e0f2fe;
        color: #0369a1;
        border: 1px solid #bae6fd;
    }
    .table thead th {
        border-bottom: 2px solid #f1f5f9;
        background-color: #f8fafc;
    }
    .table td {
        padding: 1rem 0.75rem;
        border-bottom: 1px solid #f1f5f9;
    }
    .transition-hover:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    .btn-outline-warning {
        border-color: #fbbf24;
        color: #d97706;
    }
    .btn-outline-warning:hover {
        background-color: #fbbf24;
        color: white;
    }
    .btn-outline-danger {
        border-color: #f87171;
        color: #dc2626;
    }
    .btn-outline-danger:hover {
        background-color: #f87171;
        color: white;
    }
</style>
@endsection
