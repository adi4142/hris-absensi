@extends('layouts.admin')

@section('title', 'Detail Komponen Gaji')
@section('page_title', 'Rincian Gaji Karyawan')

@section('content')
@php $role = strtolower(auth()->user()->role->name); @endphp
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
                            <div class="form-group p-4 bg-light rounded border-left border-primary shadow-sm">
                                <label class="text-muted small text-uppercase font-weight-bold">
                                    Gaji Pokok 
                                    @if($detail->payroll->is_locked) 
                                        <span class="badge badge-secondary ml-1"><i class="fas fa-lock"></i> Terkunci</span> 
                                    @endif
                                </label>
                                <h2 class="text-primary font-weight-bold mb-0">Rp {{ number_format($detail->basic_salary, 0, ',', '.') }}</h2>
                                <small class="text-muted italic">* Data tersinkronisasi dengan master karyawan</small>
                            </div>
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
                                    <th>Metode</th>
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
                                    <td>
                                        @if($comp->calculation_type == 'percentage')
                                            <span class="text-info font-weight-bold">{{ number_format($comp->calculation_value, 1) }}%</span>
                                            <small class="text-muted d-block">dari Gaji Pokok</small>
                                        @else
                                            <span class="text-muted">Nominal Tetap</span>
                                        @endif
                                    </td>
                                    <td class="text-right font-weight-bold">
                                        Rp {{ number_format($comp->amount, 0, ',', '.') }}
                                    </td>
                                    <td>{{ $comp->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        @if($role == 'superadmin' && (!$detail->payroll->is_locked || $role == 'superadmin'))
                                        <a href="{{ url('payroll/detail/'.$detail->payroll_detail_id.'/edit-component/'.$comp->payroll_component_id) }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-edit"></i> Ubah
                                        </a>
                                        <form action="{{ url('payroll/detail/'.$detail->payroll_detail_id.'/delete-component/'.$comp->payroll_component_id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus?')">
                                                <i class="fas fa-trash"></i> Hapus
                                            </button>
                                        </form>
                                        @else
                                        <span class="text-muted"><i class="fas fa-lock"></i> Terkunci / Hanya Baca</span>
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

                    @if($role == 'superadmin' && (!$detail->payroll->is_locked || $role == 'superadmin'))
                    <div class="card card-secondary mt-4">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-plus-circle mr-1"></i> Tambah Komponen Manual</h3>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('payroll.addComponent', $detail->payroll_detail_id) }}" method="POST">
                                @csrf
                                <div class="form-row">
                                    <div class="col-md-3 mb-2">
                                        <label>Nama Komponen</label>
                                        <input type="text" name="name" class="form-control" placeholder="Contoh: Bonus Kinerja" required>
                                    </div>
                                    <div class="col-md-2 mb-2">
                                        <label>Tipe</label>
                                        <select name="type" class="form-control" required>
                                            <option value="allowance">Tunjangan (+)</option>
                                            <option value="deduction">Potongan (-)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <label>Tipe Perhitungan</label>
                                        <select name="calculation_type" class="form-control" id="calc_type" onchange="updateLabel()" required>
                                            <option value="fixed">Nominal Tetap (Rp)</option>
                                            <option value="percentage">Persenan (%)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <label id="amount_label">Nominal (Rp)</label>
                                        <input type="number" step="any" name="amount" class="form-control" placeholder="Masukkan angka..." required>
                                    </div>
                                    <div class="col-md-1 mb-2 d-flex align-items-end">
                                        <button type="submit" class="btn btn-success btn-block">Simpan</button>
                                    </div>
                                </div>
                                <script>
                                    function updateLabel() {
                                        const type = document.getElementById('calc_type').value;
                                        const label = document.getElementById('amount_label');
                                        if (type === 'percentage') {
                                            label.innerText = 'Persentase (%)';
                                        } else {
                                            label.innerText = 'Nominal (Rp)';
                                        }
                                    }
                                </script>
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
