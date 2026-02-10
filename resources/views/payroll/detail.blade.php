@extends('layouts.app')

@section('title', 'Detail Komponen Gaji')
@section('page_title', 'Rincian Gaji Karyawan')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card card-info card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user mr-1"></i>
                        {{ $detail->employee->name ?? 'User Deleted' }} ({{ $detail->nip }})
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('payroll.show', $detail->payroll_id) }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali ke Daftar Karyawan
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            @if(auth()->user()->role->name == 'hrd')
                            <form action="{{ url('payroll/detail/'.$detail->payroll_detail_id.'/update-basic') }}" method="POST" class="form-group p-3 bg-light rounded">
                                @csrf
                                <label>Gaji Pokok</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input type="number" name="basic_salary" class="form-control" value="{{ $detail->basic_salary }}">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-primary">Update</button>
                                    </div>
                                </div>
                            </form>
                            @else
                                <div class="form-group p-3 bg-light rounded">
                                    <label>Gaji Pokok</label>
                                    <h3>Rp {{ number_format($detail->basic_salary, 0, ',', '.') }}</h3>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <div class="callout callout-info">
                                <h5>Ringkasan Gaji {{ $detail->employee->name ?? 'User Deleted' }}</h5>
                                <p>
                                    Total Tunjangan: <span class="text-success float-right">Rp {{ number_format($detail->total_allowance, 0, ',', '.') }}</span><br>
                                    Total Potongan: <span class="text-danger float-right">Rp {{ number_format($detail->total_deduction, 0, ',', '.') }}</span><br>
                                    <strong>Total Gaji Bersih: <span class="text-primary float-right">Rp {{ number_format($detail->total_salary, 0, ',', '.') }}</span></strong>
                                </p>
                            </div>
                        </div>
                    </div>

                    <hr>
                    
                    <h4>Daftar Komponen Gaji</h4>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>Nama Komponen</th>
                                    <th>Tipe</th>
                                    <th>Jumlah (Rp)</th>
                                    <th>Tanggal Dibuat</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($detail->components as $comp)
                                <tr>
                                    <td>{{ $comp->name }}</td>
                                    <td>
                                        @if($comp->type == 'allowance')
                                            <span class="badge badge-success">Tunjangan (+)</span>
                                        @else
                                            <span class="badge badge-danger">Potongan (-)</span>
                                        @endif
                                    </td>
                                    <td class="text-right font-weight-bold">
                                        Rp {{ number_format($comp->amount, 0, ',', '.') }}
                                    </td>
                                    <td>{{ $comp->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        @if(auth()->user()->role->name == 'hrd')
                                        <a href="{{ url('payroll/detail/'.$detail->payroll_detail_id.'/edit-component/'.$comp->payroll_component_id) }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <form action="{{ url('payroll/detail/'.$detail->payroll_detail_id.'/delete-component/'.$comp->payroll_component_id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus?')">
                                                <i class="fas fa-trash"></i> Hapus
                                            </button>
                                        </form>
                                        @else
                                        <span class="text-muted"><i class="fas fa-lock"></i> Read Only</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">Belum ada komponen tambahan.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if(auth()->user()->role->name == 'hrd')
                    <div class="card card-secondary collapsed-card">
                        <div class="card-header">
                            <h3 class="card-title">Tambah Komponen Manual</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
                            </div>
                        </div>
                        <div class="card-body">
                            <form action="{{ url('payroll/detail/'.$detail->payroll_detail_id.'/add-component') }}" method="POST">
                                @csrf
                                <div class="form-row">
                                    <div class="col-md-5">
                                        <label>Nama Komponen</label>
                                        <input type="text" name="name" class="form-control" placeholder="Contoh: Bonus Kinerja" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label>Tipe</label>
                                        <select name="type" class="form-control" required>
                                            <option value="allowance">Tunjangan (+)</option>
                                            <option value="deduction">Potongan (-)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label>Nominal (Rp)</label>
                                        <input type="number" name="amount" class="form-control" min="0" required>
                                    </div>
                                    <div class="col-md-1 d-flex align-items-end">
                                        <button type="submit" class="btn btn-success btn-block"><i class="fas fa-plus"></i> Tambah</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
