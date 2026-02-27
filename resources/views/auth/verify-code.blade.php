@extends('layouts.auth')

@section('title', 'Verifikasi Email')

@section('content')
<div class="register-box">
  <div class="card card-outline card-primary">
    <div class="card-header text-center">
      <a href="/" class="h1"><b>HRIS</b> System</a>
    </div>
    <div class="card-body">
      <p class="login-box-msg">Masukkan Kode Verifikasi</p>
      
      @if (session('success'))
          <div class="alert alert-success mt-2" role="alert">
              {{ session('success') }}
          </div>
      @endif

      @if (session('error'))
          <div class="alert alert-danger mt-2" role="alert">
              {{ session('error') }}
          </div>
      @endif

      <p class="text-sm text-center">Kami telah mengirimkan 6 digit kode verifikasi ke email <strong>{{ $email }}</strong></p>

      <form action="{{ route('verification.verify') }}" method="POST">
        @csrf
        
        <div class="input-group mb-3">
          <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" placeholder="Kode 6 Digit" maxlength="6" autofocus required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-key"></span>
            </div>
          </div>
          @error('code')
              <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
          @enderror
        </div>

        <div class="row">
          <div class="col-12">
            <button type="submit" class="btn btn-primary btn-block">Verifikasi</button>
          </div>
        </div>
      </form>

      <div class="mt-3 text-center">
        <p class="mb-1">Tidak menerima kode?</p>
        <form action="{{ route('verification.resend') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-link p-0 m-0 align-baseline">Kirim ulang kode</button>
        </form>
      </div>

      <div class="mt-3 text-center">
        <a href="{{ route('login') }}">Kembali ke Login</a>
      </div>
    </div>
    <!-- /.card-body -->
  </div><!-- /.card -->
</div>
@endsection
