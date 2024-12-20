<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="x-ua-compatible" content="ie=edge">

  <title>AdminLTE 3 | Starter</title>

  <link href="{{ asset('css/app.css') }}" rel="stylesheet">
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href={{asset("/plugins/fontawesome-free/css/all.min.css")}}>

  <!-- Theme style -->
  <link rel="stylesheet" href={{asset("/adminlte/css/adminlte.min.css")}}>

  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
  
  {{-- datatables --}}
  <link href=" https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css" rel="stylesheet">
  <link href="https://cdn.datatables.net/buttons/1.5.2/css/buttons.jqueryui.min.css" rel="stylesheet">

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
        <a href="index3.html" class="nav-link">{{__('Home')}}</a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="#" class="nav-link">Contact</a>
      </li>
    </ul>

    <!-- SEARCH FORM -->
    <form class="form-inline ml-3">
      <div class="input-group input-group-sm">
        <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
        <div class="input-group-append">
          <button class="btn btn-navbar" type="submit">
            Buscar
            <i class="fas fa-search"></i>
          </button>
        </div>
      </div>
    </form>
{{-- idioma --}}

@include('admin.idioma')

{{-- /idioma --}}
    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      {{-- perfil de usuario--}}
      
      <li class="nav-item dropdown">
        
          <a href="#" class=" user-panel mt-2 pb-2 mb-2 d-flex mr-4 mb-0" data-toggle="dropdown">
            {{-- <div class="user-panel mt-2 pb-2 mb-2 d-flex"> --}}
            <span class="badge badge-secondary "><img src={{asset("/adminlte/img/user2-160x160.jpg")}} class="img-circle elevation-1 " alt="User Image"> {{auth()->user()->name}}</span> 
          </a>
          <div class="dropdown-menu dropdown-menu dropdown-menu-right">
            <a href="#" class="dropdown-item">
                <a class="nav-link" href="{{ route('logout') }}" onclick="event.preventDefault();
                              document.getElementById('logout-form').submit();">
                    Salir
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </a>
            <a href="#" class="dropdown-item">
              Perfil
            </a>
          </div>
        
        
    </li>
  
      {{-- end perfil de usuario --}}
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar bg-primary elevation-4">
    <!-- Brand Logo -->
    <a href="index3.html" class="brand-link bg-purple">
      
      <img src="{{asset("adminlte/img/AdminLTELogo.png")}}"  alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
           style="opacity: .8">
      <span class="brand-text font-weight-light">AdminLTE 3</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar imagen de usuario -->
      <div class="user-panel mt-3 pb-3  d-flex">
        <div class="image">
          <img src="{{asset("/adminlte/img/user2-160x160.jpg")}}" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a class="d-block">{{auth()->user()->name}}</a>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        @include('nav.menu-sidebar')
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header cabecera) -->
    <div class="content-header">
      <div class="container-fluid">
        
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- aqui va el contenido-->
    <div class="content">
      @yield('content')
    </div>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
    <div class="p-3">
      <h5>Title</h5>
      <p>Sidebar content</p>
    </div>
  </aside>
  <!-- /.control-sidebar -->

  <!-- Main Footer -->
  <footer class="main-footer">
    <!-- To the right -->
    <div class="float-right d-none d-sm-inline">
      Anything you want
    </div>
    <!-- Default to the left -->
    <strong>Copyright &copy; 2014-2019 <a href="https://adminlte.io">AdminLTE.io</a>.</strong> {{__('All rights reserved.')}}
  </footer>
</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->

<!-- jQuery -->
<script src="{{ asset('js/app.js') }}" defer></script>
<script src={{URL::asset("/adminlte/plugins/jquery/jquery.min.js")}}></script>
<!-- Bootstrap 4 -->
<script src={{URL::asset("/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js")}}></script>
<!-- AdminLTE App -->
<script src={{URL::asset("adminlte/js/adminlte.min.js")}}></script>

{{-- datatables --}}

<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.19/js/dataTables.jqueryui.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.jqueryui.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.colVis.min.js"></script>
<script src="https://cdn.datatables.net/plug-ins/1.10.19/api/sum().js"></script>

@include('comunes.js')

@section('scripts')
    @include('admin.scripts')
@show

</body>
</html>
