<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Skill India Challenge | Dashboard</title>
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ assets('libraries/fontawesome-free/css/all.min.css'); }}">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <link rel="stylesheet" href="{{ assets('libraries/jquery-toast/dist/jquery.toast.min.css'); }}">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">
  <!-- Tempusdominus Bootstrap 4 -->
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="{{ assets('css/adminlte.min.css'); }}">
  <link rel="stylesheet" href="{{ assets('css/style.css'); }}">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <!-- Preloader -->
  
   @include('admin.includes.header')
   @include('admin.includes.slidebar')
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Dashboard</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Dashboard v1</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
      @yield('content')
  </div>
  <!-- /.content-wrapper -->
  @include('admin.includes.footer')
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="{{ assets('libraries/jquery/jquery.min.js'); }}"></script>
<!-- jQuery UI 1.11.4 -->
<script src="{{ assets('libraries/jquery-ui/jquery-ui.min.js'); }}"></script>
<script src="{{ assets('libraries/bootstrap/js/bootstrap.min.js'); }}"></script>
<script src="{{ assets('libraries/datatables/jquery.dataTables.min.js'); }}"></script>
<script src="{{ assets('libraries/jquery-validation/jquery.validate.js'); }}"></script>
<script src="{{ assets('libraries/jquery-toast/dist/jquery.toast.min.js'); }}"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{ assets('js/adminlte.js') }} "></script>
<!-- AdminLTE for demo purposes -->
<script src="{{ assets('js/pages/dashboard.js'); }}"></script>
<script>
    $(document).ready(function() {
        $('select').select2();
    });
</script>
@stack('js')
  
@stack('jsfun')
</body>
</html>
