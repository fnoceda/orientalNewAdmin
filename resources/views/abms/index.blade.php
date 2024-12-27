@extends('layouts.admin')

@section('main-content')
<div class="col-sm-12">
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h4 style="margin: 0;"><a href="{{ url($foo['rute'].'create') }}">+ Crear {{ $foo['title'] }} </a></h4>
    </div>
    @if (count($errors) > 0)
    <div class="alert alert-danger">
      <strong>Error!</strong> Revise los campos obligatorios.<br><br>
      <ul>
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
    @endif
    @if(Session::has('success'))
      <div class="alert alert-info">
            {{Session::get('success')}}
      </div>
    @endif
    <div class="card-body">
      <div class="table">
        <table id="crud-table" class="table">
          <thead>
            <tr>
              @foreach ($foo['cols'] as $col)
              <th>{{ ucwords(str_replace('_', ' ', $col)) }}</th>
              @endforeach
              <th style="min-width:110px">Acciones</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
    <div class="modal fade" id="crudModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel">Modal title</h4>
          </div>
          <div class="modal-body">
            <div id='myModalMsg'></div>
            <div id="myModalSendForm" style="display:none"></div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">NO</button>
            <button type="submit" class="btn btn-primary" id="modal-btn-si">SI</button>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

@endsection

@section('script')
<script type="text/javascript">
  function verRegistro(id){ console.log('mostrando datos');}
  var table = $('#crud-table').DataTable({
        dom: 'Bfrtip',
        lengthChange: false,
        processing: true,
        serverSide: true,

        ajax: '{!! url( $foo["rutedata"]  ) !!}',

        responsive: true,

        columns: [
            @foreach ($foo['cols'] as $col)
                {data:'{{ $col }}', name:'{{ $col }}' },
            @endforeach
            {data: 'acciones', name: 'acciones', class:'notexport', orderable: false, searchable: false}
        ],
        lengthMenu: [
            [ 10, 25, 50, 100, -1 ],
            [ 'ver 10 filas', 'ver 25 filas', 'ver 50 filas', 'ver 100 filas', 'ver todas' ]
        ],
         buttons: [
             {
                extend:'pageLength',
                text: 'ver 10 filas <i class="fa fa-sort-down"></i>'
             },
             {
                extend: 'copy',
                text: '<i class="fas fa-file-alt"></i>',

                exportOptions: {
                    columns: 'th:not(.notexport)'
                }
            },{
                extend: 'print',
                text: '<i class="fas fa-print"></i>',

                exportOptions: {
                    columns: 'th:not(.notexport)'
                }
            },
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel"></i>',
                title: '{!! $foo['title'] !!}',
                messageTop: '{{ config('app.name', 'Laravel') }}',
                messageBottom: '{{ auth()->user()->name }} {{ date("d/m/Y G:i") }}',

                exportOptions: {
                    columns: 'th:not(.notexport)'
                }
            },{
                extend: 'pdf',
                text: '<i class="fas fa-file-pdf"></i>',
                title: '{!! $foo['title'] !!}',
                messageTop: '{{ config('app.name', 'Laravel') }}',
                messageBottom: '{{ auth()->user()->name }} {{ date("d/m/Y G:i") }}',

                exportOptions: {
                    columns: 'th:not(.notexport)'
                }
            },
        ],
        "language": {
            "sProcessing":     "Procesando...",
            "sLengthMenu":     "Mostrar _MENU_ registros",
            "sZeroRecords":    "No se encontraron resultados",
            "sEmptyTable":     "Ningún dato disponible en esta tabla",
            "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
            "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix":    "",
            "sSearch":         "Buscar:",
            "sUrl":            "",
            "sInfoThousands":  ",",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
                "sFirst":    "Primero",
                "sLast":     "Último",
                "sNext":     "Siguiente",
                "sPrevious": "Anterior"
            },
            "oAria": {
                "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            },
        },
        "decimal":     ",",
        "thousands":        ".",
    });
$('body .dropdown-toggle').dropdown();
</script>
@endsection
