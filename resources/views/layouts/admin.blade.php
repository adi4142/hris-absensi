<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" type="image/png" href="{{ asset('AdminLTE/dist/img/vneu.avif') }}" />
  <title>{{ config('app.name', 'HRIS') }} | @yield('title', 'Admin')</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="{{ asset('AdminLTE/plugins/fontawesome-free/css/all.min.css') }}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset('AdminLTE/dist/css/adminlte.min.css') }}">
  @stack('styles')
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="{{ url('/') }}" class="nav-link">Home</a>
      </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="far fa-user"></i> {{ Auth::user()->name ?? 'Guest' }}
        </a>
        <div class="dropdown-menu dropdown-menu-right">
          <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="dropdown-item">
              <i class="fas fa-sign-out-alt mr-2"></i> Logout
            </button>
            <a href="#" class="dropdown-item">
              <i class="fas fa-user mr-2"></i> Profile
            </a>
          </form>
        </div>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route('dashboard') }}" class="brand-link">
      <img src="{{ asset('AdminLTE/dist/img/AdminLTELogo.png') }}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light">HRIS System</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="{{ asset('AdminLTE/dist/img/user2-160x160.jpg') }}" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block">{{ Auth::user()->name ?? 'Guest' }}</a>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-item">
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>Dashboard</p>
            </a>
          </li>

          <li class="nav-header">MANAJEMEN SDM</li>
          <li class="nav-item">
            <a href="{{ route('employee.index') }}" class="nav-link {{ request()->is('employee*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-users"></i>
              <p>Karyawan</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('attendance.index') }}" class="nav-link {{ request()->is('attendance') || request()->is('attendance/index') ? 'active' : '' }}">
              <i class="nav-icon fas fa-calendar-alt"></i>
              <p>Riwayat Absensi</p>
            </a>
          </li>
          @if(Auth::user()->role->name == 'hrd' || Auth::user()->role->name == 'admin')
          <li class="nav-item">
            <a href="{{ route('attendance.monitoring') }}" class="nav-link {{ request()->is('attendance/monitoring*') || request()->is('attendance/history*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-desktop"></i>
              <p>Monitoring Absensi</p>
            </a>
          </li>
          @endif
          <li class="nav-item">
            <a href="{{ route('payroll.index') }}" class="nav-link {{ request()->is('payroll*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-money-bill-wave"></i>
              <p>Payroll</p>
            </a>
          </li>

          <li class="nav-header">PENGATURAN</li>
          <li class="nav-item">
            <a href="{{ route('user.index') }}" class="nav-link {{ request()->is('user*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-user-cog"></i>
              <p>User Management</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('role.index') }}" class="nav-link {{ request()->is('role*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-user-tag"></i>
              <p>Roles</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('division.index') }}" class="nav-link {{ request()->is('division*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-layer-group"></i>
              <p>Divisi</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('departement.index') }}" class="nav-link {{ request()->is('departement*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-building"></i>
              <p>Departemen</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('position.index') }}" class="nav-link {{ request()->is('position*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-user-tag"></i>
              <p>Jabatan</p>
            </a>
          </li>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">@yield('page_title', 'Dashboard')</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">@yield('page_title', 'Dashboard')</li>
            </ol>
          </div>
        </div>
      </div>
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        @yield('content')
      </div>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <!-- Main Footer -->
  <footer class="main-footer">
    <strong>Copyright &copy; {{ date('Y') }} <a href="#">HRIS System</a>.</strong> All rights reserved.
  </footer>
</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->
<!-- jQuery -->
<script src="{{ asset('AdminLTE/plugins/jquery/jquery.min.js') }}"></script>
<!-- Bootstrap 4 -->
<script src="{{ asset('AdminLTE/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<!-- AdminLTE App -->
<script src="{{ asset('AdminLTE/dist/js/adminlte.min.js') }}"></script>
@stack('scripts')
</body>
</html>
