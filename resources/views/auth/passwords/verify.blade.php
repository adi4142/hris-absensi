@extends('layouts.auth')

@section('title', 'Verifikasi Kode')

@section('content')
<div class="login-box">
  <div class="card card-outline card-primary">
    <div class="card-header text-center">
      <a href="/" class="h1"><b>HRIS</b> System</a>
    </div>
    <div class="card-body">
      <p class="login-box-msg">Masukkan kode 6 digit yang dikirim ke email: <strong>{{ session('email') ?? $email }}</strong></p>

      <form action="{{ route('password.verify') }}" method="POST" novalidate>
        @csrf
        <input type="hidden" name="email" value="{{ old('email', $email) }}">
        
        <div class="input-group mb-3">
          <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" placeholder="Kode Verifikasi" maxlength="6" autofocus>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-key"></span>
            </div>
          </div>
          @error('code')
              <span class="invalid-feedback" role="alert">
                  <strong>{{ $message }}</strong>
              </span>
          @enderror
        </div>
        <div class="row">
          <div class="col-12">
            <button type="submit" class="btn btn-primary btn-block">Verifikasi Kode</button>
          </div>
        </div>
      </form>
      
      <p class="mt-3 mb-1">
        <form action="{{ route('password.email') }}" method="POST" novalidate>
             @csrf
             <input type="hidden" name="email" value="{{ session('email') ?? $email }}">
             <button type="submit" class="btn btn-link p-0">Kirim Ulang Kode</button>
        </form>
      </p>
    </div>
  </div>
</div>
@endsection
