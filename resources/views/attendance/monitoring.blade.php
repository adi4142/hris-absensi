@extends('layouts.admin')

@section('title', 'Monitoring Absensi')
@section('page_title', 'Monitoring Absensi Harian')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card card-primary card-outline">
             <div class="card-header">
                <h3 class="card-title">Data Absensi Karyawan, {{ \Carbon\Carbon::parse($today)->translatedFormat('l, d F Y') }}</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped" id="monitoringTable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>NIP</th>
                                <th>Nama Karyawan</th>
                                <th>Divisi / Jabatan</th>
                                <th class="text-center">Jam Masuk</th>
                                <th class="text-center">Jam Keluar</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($employees as $employee)
                                @php
                                    // The 'attendance' relation is eager loaded with today's constraint. 
                                    // It returns a collection, so we take first().
                                    $att = $employee->attendance->first();
                                    $status = $att ? $att->status : '-';
                                    $timeIn = $att ? ($att->time_in ? \Carbon\Carbon::parse($att->time_in)->format('H:i') : '-') : '-';
                                    $timeOut = $att ? ($att->time_out ? \Carbon\Carbon::parse($att->time_out)->format('H:i') : '-') : '-';
                                    
                                    $badge = 'badge-secondary';
                                    $label = 'Belum Absen';
                                    
                                    if ($status == 'Present') { $badge = 'badge-success'; $label = 'Hadir'; }
                                    elseif ($status == 'Late') { $badge = 'badge-warning'; $label = 'Terlambat'; }
                                    elseif ($status == 'Permission') { $badge = 'badge-info'; $label = 'Izin / Sakit'; }
                                    elseif ($status == 'Alpha') { $badge = 'badge-danger'; $label = 'Alpha'; }
                                @endphp
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $employee->nip }}</td>
                                    <td>{{ $employee->name }}</td>
                                    <td>
                                        {{ $employee->division->name ?? '-' }} / {{ $employee->position->name ?? '-' }}
                                    </td>
                                    <td class="text-center">{{ $timeIn }}</td>
                                    <td class="text-center">{{ $timeOut }}</td>
                                    <td class="text-center">
                                        <span class="badge {{ $badge }}">{{ $label }}</span>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('attendance.employeeHistory', $employee->nip) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-history"></i> Riwayat
                                        </a>
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
@endsection
