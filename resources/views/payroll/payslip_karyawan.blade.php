@extends('layouts.karyawan')

@section('title', 'Detail Slip Gaji')
@section('page_title', 'Rincian Slip Gaji')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-file-invoice-dollar mr-1"></i>
                        Slip Gaji: {{ date("F", mktime(0, 0, 0, $detail->payroll->period_month, 10)) }} {{ $detail->payroll->period_year }}
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('payroll.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                        <a href="{{ route('payroll.download', $detail->payroll_detail_id) }}" class="btn btn-danger btn-sm">
                        <i class="fas fa-download"></i> Download PDF
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row invoice-info mb-4">
                        <div class="col-sm-6 invoice-col">
                            Dari
                            <address>
                                <strong>PT. VNEU Teknologi Indonesia</strong><br>
                                Jl. Raya Kebayoran Lama No.557<br>
                                Jakarta Selatan<br>
                                Phone: (021) 7202351<br>
                                Email: admin@vneu.co.id
                            </address>
                        </div>
                        <div class="col-sm-6 invoice-col">
                            Kepada
                            <address>
                                <strong>{{ $detail->employee->name }}</strong><br>
                                NIP: {{ $detail->nip }}<br>
                                Posisi: {{ $detail->employee->position->name ?? '-' }}<br>
                                Divisi: {{ $detail->employee->division->name ?? '-' }}
                            </address>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Keterangan</th>
                                        <th class="text-right">Jumlah (Rp)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong>Gaji Pokok</strong></td>
                                        <td class="text-right text-dark font-weight-bold">Rp {{ number_format($detail->basic_salary, 0, ',', '.') }}</td>
                                    </tr>
                                    
                                    <!-- Tunjangan -->
                                    @php $hasAllowance = false; @endphp
                                    @foreach($detail->components as $comp)
                                        @if($comp->type == 'allowance')
                                            @php $hasAllowance = true; @endphp
                                            <tr>
                                                <td style="padding-left: 20px;">{{ $comp->name }} (Tunjangan)</td>
                                                <td class="text-right text-success">Rp {{ number_format($comp->amount, 0, ',', '.') }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                    @if(!$hasAllowance)
                                        <tr>
                                            <td style="padding-left: 20px;" class="text-muted font-italic">Tidak ada tunjangan tambahan</td>
                                            <td class="text-right">-</td>
                                        </tr>
                                    @endif

                                    <!-- Potongan -->
                                    @php $hasDeduction = false; @endphp
                                    @foreach($detail->components as $comp)
                                        @if($comp->type == 'deduction')
                                            @php $hasDeduction = true; @endphp
                                            <tr>
                                                <td style="padding-left: 20px;">{{ $comp->name }} (Potongan)</td>
                                                <td class="text-right text-danger">(Rp {{ number_format($comp->amount, 0, ',', '.') }})</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                    @if(!$hasDeduction)
                                        <tr>
                                            <td style="padding-left: 20px;" class="text-muted font-italic">Tidak ada potongan tambahan</td>
                                            <td class="text-right">-</td>
                                        </tr>
                                    @endif

                                    <tr style="border-top: 2px solid #dee2e6;">
                                        <td><strong>Total Gaji Bersih</strong></td>
                                        <td class="text-right"><h4 class="text-primary font-weight-bold">Rp {{ number_format($detail->total_salary, 0, ',', '.') }}</h4></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="row no-print mt-4">
                        <div class="col-12">
                            <p class="text-muted small">
                                * Slip gaji ini diterbitkan secara otomatis oleh sistem HRIS.
                                Jika terdapat kesalahan, harap hubungi HRD.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
