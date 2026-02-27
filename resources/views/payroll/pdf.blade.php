<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Slip Gaji - {{ $detail->employee->name }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.5;
        }
        .container {
            width: 100%;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #444;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            text-transform: uppercase;
        }
        .info-section {
            width: 100%;
            margin-bottom: 20px;
        }
        .info-col {
            width: 48%;
            display: inline-block;
            vertical-align: top;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table th, .table td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }
        .table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .text-success {
            color: #28a745;
        }
        .text-danger {
            color: #dc3545;
        }
        .text-primary {
            color: #007bff;
        }
        .font-weight-bold {
            font-weight: bold;
        }
        .total-row {
            background-color: #f1f3f5;
            font-size: 14px;
        }
        .footer {
            margin-top: 30px;
            font-size: 10px;
            color: #777;
            font-style: italic;
        }
        .signature-section {
            margin-top: 50px;
            width: 100%;
        }
        .signature-box {
            width: 200px;
            float: right;
            text-align: center;
        }
        .signature-space {
            height: 60px;
        }
    </Style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Slip Gaji Karyawan</h1>
            <div>Periode: {{ date("F", mktime(0, 0, 0, $detail->payroll->period_month, 10)) }} {{ $detail->payroll->period_year }}</div>
        </div>

        <div class="info-section">
            <div class="info-col">
                <strong>PT. VNEU Teknologi Indonesia</strong><br>
                Jl. Raya Kebayoran Lama No.557<br>
                Jakarta Selatan<br>
                Phone: (021) 7202351
            </div>
            <div class="info-col" style="text-align: right;">
                <strong>{{ $detail->employee->name }}</strong><br>
                NIP: {{ $detail->nip }}<br>
                Posisi: {{ $detail->employee->position->name ?? '-' }}<br>
                Divisi: {{ $detail->employee->division->name ?? '-' }}
            </div>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>Keterangan</th>
                    <th class="text-right">Jumlah (Rp)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Gaji Pokok</strong></td>
                    <td class="text-right font-weight-bold">Rp {{ number_format($detail->basic_salary, 0, ',', '.') }}</td>
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
                        <td style="padding-left: 20px; color: #777;">Tidak ada tunjangan tambahan</td>
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
                        <td style="padding-left: 20px; color: #777;">Tidak ada potongan tambahan</td>
                        <td class="text-right">-</td>
                    </tr>
                @endif

                <tr class="total-row">
                    <td><strong>Total Gaji Bersih</strong></td>
                    <td class="text-right text-primary font-weight-bold">
                        Rp {{ number_format($detail->total_salary, 0, ',', '.') }}
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="signature-section">
            <div class="signature-box">
                Jakarta, {{ date('d F Y') }}<br>
                HR Department<br>
                <div class="signature-space"></div>
                <strong>( ____________________ )</strong>
            </div>
            <div style="clear: both;"></div>
        </div>

        <div class="footer">
            * Slip gaji ini diterbitkan secara otomatis oleh sistem HRIS PT. VNEU Teknologi Indonesia.<br>
            Dicetak pada: {{ date('d/m/Y H:i:s') }}
        </div>
    </div>
</body>
</html>
