@extends('layouts.admin')

@section('title', 'Manajemen Gaji Pokok')
@section('page_title', 'Manajemen Gaji')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Form Section -->
        <div class="col-md-4">
            <div class="card shadow-sm border-0 mb-4" style="border-radius: 15px;">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="card-title mb-0 font-weight-bold text-primary">
                        <i class="fas fa-money-check-alt mr-2"></i> Atur Gaji Pokok
                    </h5>
                </div>
                <div class="bg-primary" style="height: 3px; width: 100%;"></div>
                
                <form action="{{ route('salary.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <label for="user_nip" class="font-weight-bold small text-muted text-uppercase">Pilih Karyawan</label>
                            <select name="user_nip" id="user_nip" class="form-control custom-select @error('user_nip') is-invalid @enderror" required>
                                <option value="">-- Pilih --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->nip }}" {{ old('user_nip') == $user->nip ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->nip }})
                                    </option>
                                @endforeach
                            </select>
                            @error('user_nip') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="basic_salary" class="font-weight-bold small text-muted text-uppercase">Gaji Pokok (IDR)</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-light border-right-0">Rp</span>
                                </div>
                                <input type="number" name="basic_salary" id="basic_salary" class="form-control border-left-0 @error('basic_salary') is-invalid @enderror" placeholder="Contoh: 5000000" value="{{ old('basic_salary') }}" required>
                            </div>
                            @error('basic_salary') <div class="invalid-feedback text-danger small">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-group mb-4">
                            <label for="effective_date" class="font-weight-bold small text-muted text-uppercase">Tanggal Berlaku</label>
                            <input type="date" name="effective_date" id="effective_date" class="form-control @error('effective_date') is-invalid @enderror" value="{{ old('effective_date', date('Y-m-d')) }}" required>
                            @error('effective_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <button type="submit" class="btn btn-primary btn-block py-2 shadow-sm">
                            <i class="fas fa-save mr-1"></i> Simpan Pengaturan Gaji
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- History / List Section -->
        <div class="col-md-8">
            <div class="card shadow-sm border-0" style="border-radius: 15px; overflow: hidden;">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="card-title mb-0 font-weight-bold text-dark">
                        <i class="fas fa-history mr-2 text-secondary"></i> Riwayat Perubahan Gaji
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if(session('success'))
                        <div class="alert alert-success border-0 rounded-0 mb-0">
                            <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
                        </div>
                    @endif
                    
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light text-muted small text-uppercase">
                                <tr>
                                    <th class="py-3 pl-4">Karyawan</th>
                                    <th class="py-3">Gaji Pokok</th>
                                    <th class="py-3">Tanggal Berlaku</th>
                                    <th class="py-3">Diinput Pada</th>
                                    @if(strtolower(auth()->user()->role->name ?? '') === 'superadmin')
                                    <th class="py-3 text-center">Aksi</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($salaries as $salary)
                                <tr>
                                    <td class="py-3 pl-4 text-dark font-weight-bold">
                                        {{ $salary->user->name ?? 'N/A' }}<br>
                                        <small class="text-muted">{{ $salary->user_nip }}</small>
                                    </td>
                                    <td class="py-3 font-weight-bold text-success">
                                        Rp {{ number_format($salary->basic_salary, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3">
                                        <span class="badge badge-light border px-2 py-1">
                                            <i class="far fa-calendar-alt mr-1"></i> {{ date('d M Y', strtotime($salary->effective_date)) }}
                                        </span>
                                    </td>
                                    <td class="py-3 small text-muted">
                                        {{ $salary->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    @if(strtolower(auth()->user()->role->name ?? '') === 'superadmin')
                                    <td class="text-center py-3">
                                        <form action="{{ route('salary.destroy', $salary->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger border-0" onclick="return confirm('Hapus riwayat ini?')" title="Hapus">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </td>
                                    @endif
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted italic">
                                        <i class="fas fa-info-circle mr-1"></i> Belum ada data riwayat gaji.
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
</div>

<style>
    .form-control:focus { border-color: #3b82f6; box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.1); }
    .custom-select:focus { border-color: #3b82f6; box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.1); }
    .table td { vertical-align: middle; }
</style>
@endsection
