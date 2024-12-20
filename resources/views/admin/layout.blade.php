<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>SB Admin 2 - Dashboard</title>

  <link href="{{ asset('css/app.css') }}" rel="stylesheet">
  <!-- Custom fonts for this template-->
  <link rel="stylesheet" href={{asset("/adminlte/css/adminlte.min.css")}}>
  <link href={{asset("/estilobostrap/vendor/fontawesome-free/css/all.min.css")}} rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
  <!-- Custom styles for this template-->
  <link href={{asset("/estilobostrap/css/sb-admin-2.min.css")}} rel="stylesheet">
    {{-- datatables --}}
    <link href=" https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/1.5.2/css/buttons.jqueryui.min.css" rel="stylesheet">
    <!-- autocomplete bootstrap -->
</head>

<body id="page-top">

  <!-- Page Wrapper -->
  <div id="wrapper">
    <div id="app"></div>
    <!-- Sidebar -->
    @include('admin.sidebar')
    <!-- End of Sidebar -->
    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

      <!-- Main Content -->
    <div id="content">
        @include('admin.nav')
        @include('error.error')
        @yield('content')

    </div>
      <!-- End of Main Content -->

      <!-- Footer -->
      <footer class="sticky-footer bg-white">
        <div class="container my-auto">
          <div class="copyright text-center my-auto">
            <span>Copyright &copy; Your Website 2020</span>
          </div>
        </div>
      </footer>
      <!-- End of Footer -->

    </div>
    <!-- End of Content Wrapper -->

  </div>
  <!-- End of Page Wrapper -->

  <!-- Scroll to Top Button-->
  <a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
  </a>

  <!-- Logout Modal-->
  <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
          <a class="btn btn-primary" href="#">Logout</a>
        </div>
      </div>
    </div>
  </div>

  <script src="{{ asset('js/app.js') }}"></script>
  <!-- Bootstrap core JavaScript-->

  {{-- adminlte --}}
  <script src={{URL::asset("/adminlte/plugins/jquery/jquery.min.js")}}></script>
  <!-- Bootstrap 4 -->
  <script src={{URL::asset("/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js")}}></script>
  <!-- overlayScrollbars -->
    <script src={{URL::asset("/adminlte/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js")}}></script>
  <!-- AdminLTE App -->
  <script src={{URL::asset("adminlte/js/adminlte.min.js")}}></script>
  <script src={{URL::asset("adminlte/js/demo.js")}}></script>
  {{-- adminlte --}}
  <!-- Core plugin JavaScript-->
  <script src={{asset("/estilobostrap/vendor/jquery-easing/jquery.easing.min.js")}}></script>

  <script src="https://cdn.jsdelivr.net/gh/xcash/bootstrap-autocomplete@v2.3.5/dist/latest/bootstrap-autocomplete.min.js"></script>


  <!-- Custom scripts for all pages-->
  <script src={{asset("/estilobostrap/js/sb-admin-2.min.js")}}></script>

  <!-- Page level plugins -->
  <script src={{asset("/estilobostrap/vendor/chart.js/Chart.min.js")}}></script>

  <!-- Page level custom scripts -->
  <script src={{asset("/estilobostrap/js/demo/chart-area-demo.js")}}></script>
  <script src={{asset("/estilobostrap/js/demo/chart-pie-demo.js")}}></script>




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


@include('admin.scripts')
@include('comunes.js')
@section('scripts')

@show

</body>
</html>
