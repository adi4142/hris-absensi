<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" type="image/png" href="{{ asset('AdminLTE/dist/img/vneu.avif') }}" />
  <title>{{ config('app.name', 'HRIS') }} | @yield('title', 'Karyawan')</title>

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
    @media print {
        .card-tools {
            display: none;
        }
    }
</style>

</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

  

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
