@extends('layouts.karyawan')

@section('title', 'Slip Gaji Saya')
@section('page_title', 'Riwayat Gaji')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">Daftar Slip Gaji</h3>
            </div>
            <div class="card-body">
                @if($payrollDetails->isEmpty())
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Belum ada data gaji yang tersedia.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>Periode</th>
                                    <th>Status</th>
                                    <th>Total Tunjangan</th>
                                    <th>Total Potongan</th>
                                    <th>Total Gaji Bersih</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($payrollDetails as $detail)
                                <tr>
                                    <td>
                                        <span class="font-weight-bold">{{ \Carbon\Carbon::create()->month($detail->payroll->period_month)->translatedFormat('F') }} {{ $detail->payroll->period_year }}</span>
                                    </td>
                                    <td>
                                        @if($detail->payroll->status == 'calculated')
                                            <span class="badge badge-info">Dalam Perhitungan</span>
                                        @elseif($detail->payroll->status == 'approved')
                                            <span class="badge badge-warning">Menunggu Pembayaran</span>
                                        @elseif($detail->payroll->status == 'paid')
                                            <span class="badge badge-success">Sudah Dibayar</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="font-weight-bold text-success">
                                            Rp {{ number_format($detail->total_allowance, 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="font-weight-bold text-danger">
                                            Rp {{ number_format($detail->total_deduction, 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td class="font-weight-bold text-success">
                                        Rp {{ number_format($detail->total_salary, 0, ',', '.') }}
                                    </td>
                                    <td>
                                        <a href="{{ route('payroll.detail', $detail->payroll_detail_id) }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-file-invoice-dollar"></i> Lihat Slip Gaji
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
