<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" type="image/png" href="{{ asset('AdminLTE/dist/img/vneu.avif') }}" />
  <title>{{ config('app.name', 'HRIS') }} | @yield('title', 'Tampilan')</title>

  <!-- Google Font: Inter / Outfit -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="{{ asset('AdminLTE/plugins/fontawesome-free/css/all.min.css') }}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset('AdminLTE/dist/css/adminlte.min.css') }}">
  @stack('styles')
  <style>
    body { font-family: 'Outfit', sans-serif; }
    .main-sidebar { background: #0f172a !important; transition: all 0.3s ease; }
    .brand-link { border-bottom: 1px solid #1e293b !important; padding: 1.25rem 1rem !important; }
    .brand-link .brand-image { width: 35px; height: 35px; }
    
    /* Sidebar Navigation Modernization */
    .nav-sidebar .nav-item { margin-bottom: 4px; }
    .nav-sidebar .nav-link { 
        border-radius: 10px !important; 
        margin: 0 10px !important; 
        padding: 10px 15px !important;
        transition: all 0.2s ease-in-out !important;
        color: #94a3b8 !important;
    }
    .nav-sidebar .nav-link:hover { 
        background: rgba(255, 255, 255, 0.05) !important; 
        color: #fff !important;
        transform: translateX(5px);
    }
    .sidebar-dark-primary .nav-sidebar > .nav-item > .nav-link.active {
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%) !important;
        box-shadow: 0 4px 15px rgba(37, 99, 235, 0.4) !important;
        color: #fff !important;
    }
    .nav-icon { font-size: 1.1rem !important; margin-right: 12px !important; transition: transform 0.3s ease; }
    .nav-link.active .nav-icon { transform: scale(1.1); }
    
    .nav-header { 
        color: #475569 !important; 
        font-weight: 700 !important; 
        text-transform: uppercase !important;
        font-size: 0.7rem !important;
        letter-spacing: 1.5px !important;
        padding: 1.5rem 1.5rem 0.5rem !important;
    }

    /* Custom Scrollbar */
    .sidebar::-webkit-scrollbar { width: 5px; }
    .sidebar::-webkit-scrollbar-track { background: transparent; }
    .sidebar::-webkit-scrollbar-thumb { background: #334155; border-radius: 10px; }
    .sidebar::-webkit-scrollbar-thumb:hover { background: #475569; }

    .content-wrapper { background: #f1f5f9 !important; }
</style>
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
        <a href="{{ url('/') }}" class="nav-link">Beranda</a>
      </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      @auth
      <li class="nav-item dropdown">
        <a class="nav-link d-flex align-items-center" data-toggle="dropdown" href="#">
          <img src="{{ Auth::user()->photo ? asset('storage/profiles/' . Auth::user()->photo) : asset('AdminLTE/dist/img/user2-160x160.jpg') }}" 
               class="img-circle elevation-2 mr-2" alt="User Image" style="width: 30px; height: 30px; object-fit: cover;">
          <span class="d-none d-md-inline">{{ Auth::user()->name ?? 'User' }}</span>
        </a>
        <div class="dropdown-menu dropdown-menu-right shadow-sm border-0">
          <div class="dropdown-header text-center py-3">
             <img src="{{ Auth::user()->photo ? asset('storage/profiles/' . Auth::user()->photo) : asset('AdminLTE/dist/img/user2-160x160.jpg') }}" 
                  class="img-circle elevation-2 mb-2" alt="User Image" style="width: 60px; height: 60px; object-fit: cover;">
             <div class="font-weight-bold text-dark">{{ Auth::user()->name }}</div>
             <small class="text-muted text-uppercase small">{{ Auth::user()->role->name ?? 'User' }}</small>
          </div>
          <div class="dropdown-divider"></div>
          <a href="{{ route('profile.index') }}" class="dropdown-item">
            <i class="fas fa-user-circle mr-2 text-primary"></i> Profil Saya
          </a>
          <div class="dropdown-divider"></div>
          <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="dropdown-item text-danger">
              <i class="fas fa-sign-out-alt mr-2"></i> Keluar
            </button>
          </form>
        </div>
      </li>
      @endauth
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route('dashboard') }}" class="brand-link">
      <img src="{{ asset('AdminLTE/dist/img/vneu.avif') }}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
      <div class="text-white fw-semibold small" style="line-height:1.2;">
        PT Vneu Teknologi <br> Indonesia
      </div>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-item">
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->is('dashboard') || request()->is('hrd/dashboard') ? 'active' : '' }}">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>Dasbor</p>
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
            <a href="{{ route('attendance.index') }}" class="nav-link {{ request()->is('attendance*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-calendar-check"></i>
              <p>Absensi</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('payroll.index') }}" class="nav-link {{ request()->is('payroll*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-money-bill-wave"></i>
              <p>Payroll</p>
            </a>
          </li>

          <li class="nav-header">REKRUTMEN</li>
          <li class="nav-item">
            <a href="{{ route('jobvacancie.index') }}" class="nav-link {{ request()->is('jobvacancie*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-briefcase"></i>
              <p>Lowongan Kerja</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('jobapplicant.index') }}" class="nav-link {{ request()->is('jobapplicant*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-user-tie"></i>
              <p>Pelamar</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('selection.index') }}" class="nav-link {{ request()->is('selection*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-tasks"></i>
              <p>Proses Seleksi</p>
            </a>
          </li>

          <li class="nav-header">PENGATURAN</li>
          @if(Auth::user()->role->name == 'admin')
          <li class="nav-item">
            <a href="{{ route('user.index') }}" class="nav-link {{ request()->is('user*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-user-cog"></i>
              <p>Manajemen Pengguna</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('role.index') }}" class="nav-link {{ request()->is('role*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-user-tag"></i>
              <p>Peran</p>
            </a>
          </li>
          @endif
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
              <i class="nav-icon fas fa-id-card"></i>
              <p>Jabatan</p>
            </a>
          </li>
          <li class="nav-item mt-4 pb-4">
            <form action="{{ route('logout') }}" method="POST" id="sidebar-logout-form-app">
              @csrf
              <a href="javascript:void(0)" onclick="document.getElementById('sidebar-logout-form-app').submit();" class="nav-link text-danger">
                <i class="nav-icon fas fa-sign-out-alt"></i>
                <p>Keluar Sistem</p>
              </a>
            </form>
          </li>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper text-sm">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">@yield('page_title', 'Dasbor')</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Beranda</a></li>
              <li class="breadcrumb-item active">@yield('page_title', 'Dasbor')</li>
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
    <strong>Hak Cipta &copy; {{ date('Y') }} <a href="#">HRIS System</a>.</strong> Hak cipta dilindungi undang-undang.
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
