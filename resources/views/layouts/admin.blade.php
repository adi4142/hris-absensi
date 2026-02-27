<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" type="image/png" href="{{ asset('AdminLTE/dist/img/vneu.avif') }}" />
  <title>{{ config('app.name', 'HRIS') }} | @yield('title', 'Admin')</title>

  <!-- Google Font: Inter / Outfit -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="{{ asset('AdminLTE/plugins/fontawesome-free/css/all.min.css') }}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset('AdminLTE/dist/css/adminlte.min.css') }}">
  <link rel="stylesheet" href="{{ asset('css/chat-widget.css') }}">
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
    .card { border-radius: 12px !important; border: none !important; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06) !important; }
    .card-header { background-color: transparent !important; border-bottom: 1px solid #f1f5f9 !important; padding: 1.25rem !important; }
    .card-title { font-weight: 700 !important; color: #1e293b; }
    .btn { border-radius: 8px !important; font-weight: 500; transition: all 0.3s ease; }
    .btn-primary { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%) !important; border: none !important; }
    .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2); }
    .table thead th { background: #f8fafc !important; border-top: none !important; color: #64748b !important; font-weight: 600 !important; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.025em; }
    .table td { vertical-align: middle !important; color: #334155; }
    .badge { border-radius: 6px !important; padding: 0.5em 0.8em !important; font-weight: 600 !important; }
    .main-header { border-bottom: 1px solid #f1f5f9 !important; }
    .breadcrumb-item a { color: #3b82f6 !important; }
    /* Form control styling */
    .form-control { border-radius: 8px !important; border: 1px solid #e2e8f0 !important; padding: 0.6rem 0.75rem !important; }
    .form-control:focus { border-color: #3b82f6 !important; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important; }
    label { font-weight: 600 !important; color: #475569 !important; margin-bottom: 0.5rem !important; }
  </style>
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
        <a href="{{ url('/') }}" class="nav-link">Beranda</a>
      </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <li class="nav-item dropdown">
        <a class="nav-link d-flex align-items-center" data-toggle="dropdown" href="#">
          <img src="{{ Auth::user()->photo ? asset('storage/profiles/' . Auth::user()->photo) : asset('AdminLTE/dist/img/user2-160x160.jpg') }}" 
               class="img-circle elevation-2 mr-2" alt="User Image" style="width: 30px; height: 30px; object-fit: cover;">
          <span class="d-none d-md-inline">{{ Auth::user()->name ?? 'Guest' }}</span>
        </a>
        <div class="dropdown-menu dropdown-menu-right shadow-sm border-0">
          <div class="dropdown-header text-center py-3">
             <img src="{{ Auth::user()->photo ? asset('storage/profiles/' . Auth::user()->photo) : asset('AdminLTE/dist/img/user2-160x160.jpg') }}" 
                  class="img-circle elevation-2 mb-2" alt="User Image" style="width: 60px; height: 60px; object-fit: cover;">
             <div class="font-weight-bold text-dark">{{ Auth::user()->name }}</div>
             <small class="text-muted text-uppercase small">{{ Auth::user()->role ? Auth::user()->role->name : 'User' }}</small>
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
            <a href="{{ route('user.index') }}" class="nav-link {{ request()->is('user*') || request()->is('employee*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-users"></i>
              <p>Daftar Anggota</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('attendance.index') }}" class="nav-link {{ request()->is('attendance') || request()->is('attendance/index') ? 'active' : '' }}">
              <i class="nav-icon fas fa-calendar-alt"></i>
              <p>Riwayat Absensi</p>
            </a>
          </li>
          @php $role = Auth::user()->role ? strtolower(Auth::user()->role->name) : ''; @endphp
          @if($role == 'hrd' || $role == 'admin' || $role == 'superadmin')
          <li class="nav-item">
            <a href="{{ route('attendance.monitoring') }}" class="nav-link {{ request()->is('attendance/monitoring*') || request()->is('attendance/history*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-desktop"></i>
              <p>Monitoring Absensi</p>
            </a>
          </li>
          @endif
          
          <li class="nav-item">
            <a href="{{ route('leave.index') }}" class="nav-link {{ request()->is('leave*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-calendar-minus"></i>
              <p>Sistem Cuti</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('payroll.index') }}" class="nav-link {{ request()->is('payroll*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-money-bill-wave"></i>
              <p>Payroll</p>
            </a>
          </li>

          <li class="nav-header">PENGATURAN SISTEM</li>
          @if(Auth::user()->role && strtolower(Auth::user()->role->name) == 'superadmin')
          <li class="nav-item">
            <a href="{{ route('superadmin.settings.index') }}" class="nav-link {{ request()->is('superadmin/settings*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-cogs"></i>
              <p>Pengaturan Global</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('superadmin.leave_settings.index') }}" class="nav-link {{ request()->is('superadmin/leave-settings*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-calendar-cog"></i>
              <p>Pengaturan Cuti</p>
            </a>
          </li>
          @endif
          @if($role == 'hrd' || $role == 'admin' || $role == 'superadmin')
          <li class="nav-item">
            <a href="{{ route('role.index') }}" class="nav-link {{ request()->is('role*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-user-tag"></i>
              <p>Hak Akses (Role)</p>
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
              <i class="nav-icon fas fa-user-tag"></i>
              <p>Jabatan</p>
            </a>
          </li>
          <li class="nav-item mt-4 pb-4">
            <form action="{{ route('logout') }}" method="POST" id="sidebar-logout-form">
              @csrf
              <a href="javascript:void(0)" onclick="document.getElementById('sidebar-logout-form').submit();" class="nav-link text-danger">
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
  <div class="content-wrapper">
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
<script src="{{ asset('js/chat-widget.js') }}"></script>
@stack('scripts')
</body>
</html>
