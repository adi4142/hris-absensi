@extends('layouts.app')

@section('title', 'Payroll')
@section('page_title', 'Manajemen Penggajian')

@section('content')
<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title">Daftar Periode Gaji</h3>
        <div class="card-tools">
            @if(auth()->user()->role->name == 'admin')
            <a href="{{ route('payroll.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Tambah Periode
            </a>
            @endif
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>No</th>
                        <th>Bulan</th>
                        <th>Tahun</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payrolls as $v)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ date("F", mktime(0, 0, 0, $v->period_month, 10)) }}</td>
                        <td>{{ $v->period_year }}</td>
                        <td>
                            @if($v->status == 'calculated')
                                <span class="badge badge-info">Dihitung</span>
                            @elseif($v->status == 'approved')
                                <span class="badge badge-warning">Disetujui</span>
                            @elseif($v->status == 'paid')
                                <span class="badge badge-success">Dibayar</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group">
                                <a href="{{ route('payroll.show', $v->payroll_id) }}" class="btn btn-info btn-sm" title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if(auth()->user()->role->name == 'admin')
                                <a href="{{ route('payroll.edit', $v->payroll_id) }}" class="btn btn-warning btn-sm" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('payroll.destroy', $v->payroll_id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Hapus periode ini?')" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
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
@endsection