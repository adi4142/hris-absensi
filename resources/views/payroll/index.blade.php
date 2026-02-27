@extends('layouts.admin')

@section('title', 'Penggajian')
@section('page_title', 'Manajemen Penggajian')

@section('content')
<div class="container-fluid">
    <div class="row text-center mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 bg-gradient-success text-white">
                <div class="card-body">
                    <h6 class="text-uppercase small font-weight-bold opacity-75">Periode Berjalan</h6>
                    <h3 class="mb-0 font-weight-bold">{{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <!-- Space for other stats -->
        </div>
        <div class="col-md-4 text-right d-flex align-items-center justify-content-end">
            @php $role = strtolower(auth()->user()->role->name); @endphp
            @if($role == 'superadmin')
            <a href="{{ route('payroll.create') }}" class="btn btn-primary shadow-sm px-4">
                <i class="fas fa-plus-circle mr-2"></i> Tambah Periode Gaji
            </a>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h3 class="card-title">
                        <i class="fas fa-money-check-alt mr-2 text-success"></i> Riwayat & Daftar Periode Gaji
                    </h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-center" width="60">No</th>
                                    <th>Periode Bulan</th>
                                    <th class="text-center">Tahun</th>
                                    <th class="text-center">Status Pembayaran</th>
                                    <th class="text-center">Kelola</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($payrolls as $v)
                                <tr>
                                    <td class="text-center font-weight-bold text-muted">{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light rounded p-2 mr-3 border shadow-sm">
                                                <i class="fas fa-calendar-check text-success fa-lg"></i>
                                            </div>
                                            <div class="font-weight-bold text-dark">
                                                {{ \Carbon\Carbon::create()->month($v->period_month)->translatedFormat('F') }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-light border px-3 py-2 text-dark font-weight-normal">
                                            {{ $v->period_year }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if($v->status == 'calculated')
                                            <span class="badge badge-info shadow-sm px-3 py-2">
                                                <i class="fas fa-calculator mr-1"></i> DIHITUNG
                                            </span>
                                        @elseif($v->status == 'approved')
                                            <span class="badge badge-warning shadow-sm px-3 py-2 text-white">
                                                <i class="fas fa-check-double mr-1 text-white"></i> DISETUJUI
                                            </span>
                                        @elseif($v->status == 'paid')
                                            <span class="badge badge-success shadow-sm px-3 py-2">
                                                <i class="fas fa-hand-holding-usd mr-1"></i> DIBAYAR
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group shadow-sm rounded overflow-hidden">
                                            @php $role = strtolower(auth()->user()->role->name); @endphp
                                            
                                            <a href="{{ route('payroll.show', $v->payroll_id) }}" class="btn btn-sm btn-info" title="Lihat Laporan">
                                                <i class="fas fa-eye"></i> Detail
                                            </a>

                                            @if($role == 'superadmin')
                                                @if(!$v->is_locked)
                                                <form action="{{ route('superadmin.payroll.lock', $v->payroll_id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-dark" title="Kunci Periode" onclick="return confirm('Kunci periode ini? HRD tidak akan bisa mengubah data lagi.')">
                                                        <i class="fas fa-lock"></i>
                                                    </button>
                                                </form>
                                                @else
                                                <form action="{{ route('superadmin.payroll.unlock', $v->payroll_id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success" title="Buka Kunci" onclick="return confirm('Buka kunci periode ini?')">
                                                        <i class="fas fa-unlock"></i>
                                                    </button>
                                                </form>
                                                @endif
                                            @endif

                                            @if($role == 'superadmin')
                                            <a href="{{ route('payroll.edit', $v->payroll_id) }}" class="btn btn-sm btn-warning" title="Edit Periode">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('payroll.destroy', $v->payroll_id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Hapus periode ini?')" title="Hapus Periode">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            @endif

                                            @if($v->is_locked)
                                                <span class="btn btn-sm btn-secondary disabled" title="Terkunci">
                                                    <i class="fas fa-lock text-white-50"></i>
                                                </span>
                                            @endif
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
    .bg-gradient-success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }
    .opacity-75 { opacity: 0.75; }
    .table td { vertical-align: middle; }
</style>
@endsection
