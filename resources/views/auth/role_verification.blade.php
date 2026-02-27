@extends('layouts.auth')

@section('title', 'Verifikasi Keamanan')

@section('content')
<div class="login-box">
  <div class="card card-outline card-danger">
    <div class="card-header text-center">
      <a href="/" class="h1"><b>Security</b> Check</a>
    </div>
    <div class="card-body">
      <p class="login-box-msg">Masukkan Kode Lisensi Khusus Role Anda (Admin/HRD)</p>
        
      @error('license_code')
          <div class="alert alert-danger">{{ $message }}</div>
      @enderror

      <form action="{{ route('role.verification.verify') }}" method="POST">
        @csrf
        <div class="input-group mb-3">
          <input type="text" name="license_code" class="form-control" placeholder="Kode Lisensi" required autofocus>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-shield-alt"></span>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-12">
            <button type="submit" class="btn btn-danger btn-block">Verifikasi</button>
          </div>
        </div>
      </form>

      <div class="mt-3 text-center">
          <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-link text-muted">Batal & Logout</button>
          </form>
      </div>
    </div>
  </div>
</div>
@endsection
