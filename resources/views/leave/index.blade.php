@extends(in_array(strtolower(Auth::user()->role->name ?? ''), ['superadmin', 'hrd', 'admin']) ? 'layouts.admin' : 'layouts.karyawan')

@section('title', 'Manajemen Cuti')
@section('page_title', 'Daftar Pengajuan Cuti')

@section('content')
<div class="row">
    <div class="col-md-12">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle mr-2"></i> {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white shadow-sm border-0">
                    <div class="card-body">
                        <h5><i class="fas fa-calendar-check mr-2"></i> Total Jatah Cuti</h5>
                        <h2 class="font-weight-bold mb-0">{{ Auth::user()->total_jatah_cuti }} Hari</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-warning text-dark shadow-sm border-0">
                    <div class="card-body">
                        <h5><i class="fas fa-calendar-minus mr-2"></i> Cuti Terpakai</h5>
                        <h2 class="font-weight-bold mb-0">{{ Auth::user()->cuti_terpakai }} Hari</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white shadow-sm border-0">
                    <div class="card-body">
                        <h5><i class="fas fa-calendar-plus mr-2"></i> Sisa Cuti</h5>
                        <h2 class="font-weight-bold mb-0">{{ Auth::user()->sisa_cuti }} Hari</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title"><i class="fas fa-list mr-2"></i> Riwayat Pengajuan</h3>
                @if($role === 'karyawan')
                    <a href="{{ route('leave.create') }}" class="btn btn-primary ml-auto">
                        <i class="fas fa-plus mr-1"></i> Ajukan Cuti
                    </a>
                @endif
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="pl-4">No</th>
                                @if($role !== 'karyawan')
                                    <th>Karyawan</th>
                                @endif
                                <th>Periode</th>
                                <th>Durasi</th>
                                <th>Alasan</th>
                                <th>Status</th>
                                <th class="pr-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($leaves as $leave)
                            <tr>
                                <td class="pl-4">{{ $loop->iteration }}</td>
                                @if($role !== 'karyawan')
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="mr-2">
                                                <img src="{{ $leave->user->photo ? asset('storage/profiles/' . $leave->user->photo) : asset('AdminLTE/dist/img/user2-160x160.jpg') }}" class="img-circle" style="width: 30px; height: 30px; object-fit: cover;">
                                            </div>
                                            <div>
                                                <div class="font-weight-bold text-dark">{{ $leave->user->name }}</div>
                                                <small class="text-muted">{{ $leave->user->nip }}</small>
                                            </div>
                                        </div>
                                    </td>
                                @endif
                                <td>
                                    <div class="text-dark font-weight-500">
                                        {{ \Carbon\Carbon::parse($leave->start_date)->isoFormat('D MMM Y') }} 
                                        - 
                                        {{ \Carbon\Carbon::parse($leave->end_date)->isoFormat('D MMM Y') }}
                                    </div>
                                </td>
                                <td><span class="badge badge-info">{{ $leave->days }} Hari</span></td>
                                <td>{{ Str::limit($leave->reason, 30) }}</td>
                                <td>
                                    @if($leave->status === 'PENDING')
                                        <span class="badge badge-warning text-dark"><i class="fas fa-clock mr-1"></i> Menunggu</span>
                                    @elseif($leave->status === 'APPROVED')
                                        <span class="badge badge-success"><i class="fas fa-check-circle mr-1"></i> Disetujui</span>
                                    @else
                                        <span class="badge badge-danger" title="{{ $leave->hrd_note }}"><i class="fas fa-times-circle mr-1"></i> Ditolak</span>
                                    @endif
                                </td>
                                <td class="pr-4 text-center">
                                    @if($leave->status === 'PENDING' && ($role === 'hrd' || $role === 'superadmin'))
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-success mr-1" data-toggle="modal" data-target="#approveModal{{ $leave->id }}" title="Setujui">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#rejectModal{{ $leave->id }}" title="Tolak">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>

                                        <!-- Approve Modal -->
                                        <div class="modal fade" id="approveModal{{ $leave->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content border-0 shadow-lg">
                                                    <div class="modal-header bg-success text-white">
                                                        <h5 class="modal-title font-weight-bold">Setujui Pengajuan Cuti</h5>
                                                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <form action="{{ route('leave.approve', $leave->id) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-body text-left">
                                                            <p>Menyetujui cuti dari <strong>{{ $leave->user->name }}</strong>.</p>
                                                            <div class="form-group">
                                                                <label class="text-muted">Catatan HRD (Opsional):</label>
                                                                <textarea name="hrd_note" class="form-control" rows="3" placeholder="Masukkan catatan jika ada..."></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer bg-light">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn btn-success">Setujui Cuti</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Reject Modal -->
                                        <div class="modal fade" id="rejectModal{{ $leave->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content border-0 shadow-lg">
                                                    <div class="modal-header bg-danger text-white">
                                                        <h5 class="modal-title font-weight-bold">Tolak Pengajuan Cuti</h5>
                                                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <form action="{{ route('leave.reject', $leave->id) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-body text-left">
                                                            <p>Menolak cuti dari <strong>{{ $leave->user->name }}</strong>.</p>
                                                            <div class="form-group">
                                                                <label class="text-muted">Catatan HRD (Opsional):</label>
                                                                <textarea name="hrd_note" class="form-control" rows="3" placeholder="Masukkan alasan penolakan..."></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer bg-light">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn btn-danger">Tolak Pengajuan</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <button class="btn btn-sm btn-light" disabled>
                                            <i class="fas fa-eye"></i> Detail
                                        </button>
                                    @endif
                                </td>
                            </tr>
                            @if(($leave->status === 'REJECTED' || $leave->status === 'APPROVED') && $leave->hrd_note)
                                <tr class="bg-light">
                                    <td colspan="{{ $role === 'karyawan' ? 5 : 6 }}" class="pl-5 py-1">
                                        <small class="{{ $leave->status === 'APPROVED' ? 'text-success' : 'text-danger' }}"><strong>Catatan HRD:</strong> {{ $leave->hrd_note }}</small>
                                    </td>
                                </tr>
                            @elseif($leave->status === 'REJECTED' && !$leave->hrd_note)
                                <tr class="bg-light">
                                    <td colspan="{{ $role === 'karyawan' ? 5 : 6 }}" class="pl-5 py-1">
                                        <small class="text-danger"><strong>Catatan HRD:</strong> -</small>
                                    </td>
                                </tr>
                            @endif
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-calendar-times mb-3 d-block" style="font-size: 3rem;"></i>
                                        Belum ada pengajuan cuti.
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
