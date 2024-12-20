@extends('layouts.admin')
@section('styles')
<link href="{{ asset('vendor/jquery-ui-1.12.1/jquery-ui.min.css') }}" rel="stylesheet">
@endsection
@section('main-content')

<div class="col-sm-12" style="padding: 0; float: left;">
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <a href="#" data-toggle="modal" data-target="#modal-cargar-articulos" class="btn btn-sm btn-primary float-left mr-5" id="nuevo_articulo">
        + Crear Articulos
      </a>
      <label for="filtar" class="float-left mr-3"><h3>Filtrar Por:</h3></label>
    <form action="#" method="POST" class="float-left form-inline">
      @csrf
      <select name="filtar" id="filtar" class="custom-select">
        <option value="">Todos los articulos</option>
          @php
              echo $categorias_anidadas;
          @endphp
      </select>

      <label for="filtar" class="float-left ml-3 mr-3"><h3>Empresas:</h3></label>
      <select name="filtar_empresa" id="filtar_empresa" class="custom-select w-full">
        @if (!isset($empresa[0]))
        <option value="100000">Todas las empresas</option>
        @else
        <option value="" >Todas las empresas</option>
        @endif
        @foreach ($empresa as $emp)
            @if ($emp->id == Auth()->user()->empresa_id)
            <option value="{{$emp->id}}" selected>{{$emp->name}}</option>
            @else
            <option value="{{$emp->id}}">{{$emp->name}}</option>
            @endif
            
        @endforeach
      </select>
      <label for="filtar" class="float-left ml-3 mr-3"><h3>Usuarios:</h3></label>
      <select name="filtar_usuario" id="filtar_usuario" class="custom-select w-full">
        <option value="">Usuarios Autorizados</option>
        @foreach ($usuarios as $usu)
            <option value="{{$usu->id}}">{{$usu->name}}</option>
        @endforeach
      </select>
      <button class="btn btn-primary float-left ml-3" type="button" onclick="filtrar();">Filtrar</button>
    </form>
    </div>
  </div>
</div>

<div class="col-sm-12" style="padding: 0 5px 0 0; float: left;">
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h5 style="margin: 0;">Categorias</h5>
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
      <div class="table table-sm">
        <table id="crud-table" class="table table-striped table-bordered tablas">
        </table>
      </div>
    </div>

  </div>
</div>

<div class="modal" tabindex="-1" role="dialog" id="modal_e">
  <div class="modal-dialog" role="document">
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title">Eliminar</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
              </button>
          </div>
          <div class="modal-body">
              <p>
                Seguro que quiere eliminar este Articulo una vez eliminado ya no se podra recuperar
              </p>
          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-primary" onclick="eliminar_articulo()">Si</button>
              <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
          </div>
      </div>
  </div>
</div>
<input type="hidden" id="id_eliminar">

<!-- Modal -->
<div class="modal fade" id="modal-cargar-articulos"  role="dialog"  style="overflow-y: scroll;">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Crear Articulo</h5>
        <table>
          <tr>
            <td><button class=" btn btn-primary float-right" type="button" id="cerrar_guardar">
              <span aria-hidden="true">Guardar</span>
            </button></td>
            <td><button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button></td>
          </tr>
        </table>


      </div>
      <form action="#" method="" enctype="multipart/form-data">
          {{ csrf_field() }}
        <div id='errores_ventas' style='display:none' class='alert alert-danger' ></div>
        <div id='success' style='display:none;' style="background: #4e73df  !important;" class='alert alert-primary' ></div>
        <input  class="btn button " type="hidden" id="mostrar_errores" value="1" disabled error="">

        <input type="hidden" value="nuevo" id="id_articulo">
        <div class="modal-body">
          <div class="col-sm-12" style="padding: 0; overflow: hidden;">

            <div class="col-sm-6" style="float: left;">
              <div class="form-row">
                <div class="form-group col-md-12">
                  <label for="inputEmail4">Empresa</label>
                  <select class="form-control form-control-sm" id="empresa">
                    @foreach($empresa as $emp)
                    @if ($emp->id == 1)
                    <option value="{{$emp->id}}" selected="true">{{$emp->name}}</option>
                    @else
                    <option value="{{$emp->id}}">{{$emp->name}}</option>
                    @endif
                    @endforeach
                  </select>
                </div>
              </div>

              <div class="form-row">
                <div class="form-group col-md-12">
                  <label for="cod_articulo">Codigo</label>
                  <input type="text" class="form-control form-control-sm" id="cod_articulo" placeholder="Codigo del articulo">
                </div>
              </div>
              <div class="form-row">
                <div class="form-group col-md-12">
                  <label for="inputEmail4"><a href="#" style="color: brown">*</a> Categoria</label>
                  <select class="form-control form-control-sm" id="categoria">
                    @php
                        echo $categorias_anidadas;
                    @endphp
                    {{-- @foreach($categorias as $cat)
                      <option value="{{$cat->id}}">{{$cat->name}}</option>
                    @endforeach --}}
                  </select>
                </div>
              </div>

              <div class="form-row">
                <div class="form-group col-md-6">
                  <label for="nombre_articulo"><a href="#" style="color: brown">*</a>Articulo</label>
                  <input type="text" class="form-control form-control-sm" id="nombre_articulo" placeholder="Nombre del articulo">
                </div>
                <div class="form-group col-md-6">
                  <label for="nombre_co_articulo"><a href="#" style="color: brown">*</a>제품</label>
                  <input type="text" class="form-control form-control-sm" id="nombre_co_articulo" placeholder="Nombre Coreano">
                </div>
              </div>
              <div class="form-row">
                <div class="form-group col-md-9">
                  <label for="inputEmail4">Presentacion</label>
                  <input type="text" class="form-control form-control-sm" id="presentacion" placeholder="presentacion">
                </div>
                <div class="form-group col-md-3">
                  <label for="combo_articulo">Producto Activo</label><br />
                  <label class="switch">
                    <input type="checkbox" type="checkbox"  id="es_activo" checked="true">
                    <span class="slider round"></span>
                  </label>
                </div>
              </div>
              <div class="form-row">
                <div class="form-group col-md-12">
                  <label for="desc_articulo">Descripción del articulo</label>
                  <textarea type="text"  id="desc_articulo" rows="5" placeholder="Descripcion del articulo" class="form-control form-control-sm"></textarea>
                  {{-- <input  class="form-control form-control-sm" id="desc_articulo" > --}}
                </div>
              </div>

              <div class="form-row">
                <div class="form-group col-md-12">
                  <label for="desc_co_articulo">제품 설명</label>
                  <textarea type="text" class="form-control form-control-sm" id="desc_co_articulo" placeholder="Descripcion coreano"  cols="30" rows="5"></textarea>
                  {{-- <input type="text" class="form-control form-control-sm" id="desc_co_articulo" placeholder="Descripcion coreano"> --}}
                </div>
              </div>


              <div class="form-row">
                <div class="form-group col-md-12">
                  <label for="obs_articulo">Observaciones</label>
                  <textarea type="text" class="form-control form-control-sm" id="obs_articulo" placeholder="observaciones del articulo" cols="30" rows="5"></textarea>
                  {{-- <input type="text" class="form-control form-control-sm" id="obs_articulo" placeholder="observaciones del articulo"> --}}
                </div>
              </div>

              <div class="form-row">
                <div class="form-group col-md-12">
                  <label for="obs_co_articulo">관찰</label>
                  <textarea type="text" class="form-control form-control-sm" id="obs_co_articulo" placeholder="observaciones del articulo coreano"  cols="30" rows="5"></textarea>
                  {{-- <input type="text" class="form-control form-control-sm" id="obs_co_articulo" placeholder="observaciones del articulo coreano"> --}}
                </div>
              </div>
{{--  --}}
{{--  --}}

              {{--  --}}

            </div>
            <style>
              .button-container{
                display:inline-block;
                position:relative;
                }

                .button-container a{
                position: absolute;
                bottom:5em;
                right:5em;
                background-color:#4e73df;
                border-radius:1.5em;
                color:rgb(233, 228, 228);
                text-transform:uppercase;
                text-align: right;
                padding-top: 0.2em;
                cursor: pointer;
                padding-right: 1em;
                padding-bottom: 1.1em;
                padding-left: 0.7em;
                width: 10px ;
                height: 10px ;
                }
            </style>

            <div class="col-sm-6" style="float: left;">
              {{-- -------------------- --}}
              <div class="form-row">
                <div class="form-group col-md-4">
                  <label for="costo_articulo">Costo</label>
                  <input type="number" class="form-control form-control-sm" id="costo_articulo" placeholder="Costo de articulo">
                </div>
                <div class="form-group col-md-2 align-self-md-center">
                  <label for="combo_articulo">Combo</label><br />
                  <label class="switch">
                    <input type="checkbox" type="checkbox"  id="es_combo" name="es_combo" >
                    <span class="slider round"></span>
                  </label>
                </div>
                <div class="form-group col-md-3">
                  <label for="existencia"><a href="#" style="color: brown">*</a>Existencia Actual</label>
                  <input type="number" class="form-control form-control-sm" id="existencia" placeholder="Existencia actual">
                </div>
                <div class="form-group col-md-3">
                  <label for="existencia"><a href="#" style="color: brown">*</a>Existencia Minima</label>
                  <input type="number" class="form-control form-control-sm" id="existencia_minima" placeholder="Existencia minima">
                </div>
              </div>
              {{--  --}}
              <div class="form-row">
                <div class="form-group col-md-4">
                  <label for="costo_antes">Precio Antes</label>
                  <input type="number" class="form-control form-control-sm" id="costo_antes" >
                </div>
                <div class="form-group col-md-4">
                  <label for="costo_ahora"><a href="#" style="color: brown">*</a>Precio de Venta</label>
                  <input type="number" class="form-control form-control-sm" id="costo_ahora" >
                </div>
                <div class="form-group col-md-4">
                  <label for="tiempo_entrega">Plazo de Entrega</label>
                  <select class="form-control form-control-sm " id="tiempo_entrega">
                    <option value="A confirmar">A confirmar</option>
                    <option value="24">24Hs.</option>
                    <option value="48">48Hs.</option>
                  </select>
                </div>
              </div>
              <div class="form-row">
                <div class="form-group col-md-4">
                  <label for="tiempo_entrega">Plazo de Entrega (Configurado)</label>
                  <select class="form-control form-control-sm " id="plazo_entrega_id">
                    @foreach ($plazo as $item)
                      <option value="{{$item->id}}">{{$item->name}}</option>
                    @endforeach
                  </select>
                </div>
                <div class="form-group col-md-4 mt-3">
                  <label for="unidad_de_medida">Unidad de Medida</label>
                  <select class="form-control form-control-sm" id="unidad_de_medida">
                    <option value="">Seleccione una unidad</option>
                    <option value="unidad">Unidad</option>
                    <option value="kilo">Kilo</option>
                    <option value="gramo">Gramo</option>
                  </select>
                </div>
              </div>
              {{--  --}}
              {{-- añadimos select 2 --}}
              <div class=" form-inline">
              <div class="form-group col-md-4">
                    <label for="exampleFormControlSelect1" class="">Sabores</label>
                    <select class="form-control" id="exampleFormControlSelect1" name="categoria" multiple="multiple" style="width: 100%">
                        @php
                            echo $sabores;
                        @endphp
                    </select>
              </div>
              <div class="form-group col-md-4">
                    <label for="exampleFormControlSelect2" class="">Medidas</label>
                    <select class="form-control" id="exampleFormControlSelect2" name="categoria" multiple="multiple" style="width: 100%">
                        @php
                            echo $medidas;
                        @endphp
                    </select>
              </div>
          <div class="form-group col-md-12 mt-3 mb-3" id="plano_colores">
            @foreach ($colores_completos as $item)
              <div class="form-check ml-2 mr-2">
                <input class="form-check-input color_code " type="checkbox" name="{{$item->name}}" id="{{$item->name}}">
                <div style="height: 30px; width: 30px; background-color: {{$item->name}}" class="border"></div>  
              </div>
              @endforeach
          </div>

              {{-- <div  class="form-group col-md-12 mt-2">
                <table id="tabla_colores">
                  <thead>
                    <tr>
                      <td><button type="button" id="colores" class="btn btn-primary mb-2"><i class="fa fa-plus" aria-hidden="true"></i></button></td>
                    </tr>
                  </thead>
                  <tbody id="tbodyid">    
                  </tbody>
                </table>
                
              </div> --}}
             
            </div>
              {{--  --}}
              <hr>
              <p>Timer</p>
              <hr>
              <div class="form-row">
                <div class="form-group col-md-4">
                  <label for="inputEmail4">Desde</label>
                  <input type="date" class="form-control form-control-sm" id="desde" >
                </div>

                <div class="form-group col-md-4">
                  <label for="inputEmail4">Hasta</label>
                  <input type="date" class="form-control form-control-sm" id="hasta" >
                </div>

                <div class="form-group col-md-4">
                  <label for="inputEmail4">Precio</label>
                  <input type="number" class="form-control form-control-sm" id="precio" >
                </div>
              </div>
              {{-- ---------------------------- --}}


              <div class="form-group" id="ocultar_imagen">
                <label for="file">Imagenes del Articulo </label>
                <div class="custom-file" id="ocultar_imagenes">

                  <input type="file" class="custom-file-input" id="file" name="file" required>
                  <label class="custom-file-label" for="file">Seleccionar una imagen</label>
                  <div class="invalid-feedback"></div>
                </div>
                <div class="form-group" style="margin-top: 20px;">

                  <div >

                    <ul id="sortable">
                      <div id="file-preview-zone">
                        

                      </div>
                    </ul>
                  </div>

                </div>
              </div>
            </div>
            <div id="ocultar">
                <div class="form-row col-md-6" style="float: left;" id="listas_precios">
                  @foreach ($listas as $l)
                  <div class="form-group col-md-12">
                  <label for="">Lista de Precio: {{$l->name.'/'.$l->name_co}}</label>
                  <input type="number" class="form-control form-control-sm" id="lista_{{$l->id}}">
                  </div>
                  @endforeach

                </div>

                <div class="form-row col-md-6" style="float: left;" >
                  <select class="form-control form-control-sm" id="etiqueta">
                    <option value="sin_etiqueta">Sin Etiqueta</option>
                    @foreach($etiquetas as $cat)
                      <option value="{{$cat->id}}">{{$cat->name}}</option>
                    @endforeach
                  </select>
                </div>
                <div class="form-row col-md-6 " style="float: left;"  >
                  <div class=" ml-5"></div>
                  <div class=" ml-5"></div>
                  <div class=" ml-5"></div>
                  <div class=" ml-3"></div>
                  <style>
                    .imagenes img[src=""] {
                            display: none;
                      }
                  </style>
                    <img class=" ml-5 mt-3 imagenes"  src="" id="img_muestra"  width="80px" height="70px" >

                </div>
          </div>
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal" id="cerrame">Cerrar</button>
          <button type="button" class="btn btn-primary" id="guardar_articulo">Guardar y Cerrar</button>
        </div>
      </form>
    </div>
  </div>
</div>


{{-- agregamos un mini modal de colores --}}
<input type="hidden" value="nuevo" id="exampleFormControlSelect3">
<div id="modalmodal" class="modal fade" role="dialog"> 
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header"> 
        <h4 class="modal-tittle">Agregar Colores</h4>
      </div> 
      <form class="form-horizontal" role="form" id="form-agregar">
        @php
            echo $colores;
        @endphp
        <div class="modal-footer">
          <button type="button" id="GuardarNombre" name="GuardarNombre" class="btn btn-primary">
            <span class="fa fa-save"></span><span class="hidden-xs"> Guardar</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
{{--  --}}
{{-- agregamos modal de descripcion y imagenes --}}
@include('admin.articulos.modal_art_des_img')
{{--  --}}
<style>
  #file-preview {width: 120px; float: left; padding: 5px; border: 1px solid #e5e5e5; margin: 10px;}
</style>

@endsection

@section('script')
<script src="{{ asset('vendor/jquery-ui-1.12.1/jquery-ui.min.js') }}">
</script>
<script src=" {{ asset('js/articulo_img_desc.js') }} "></script>
<script src=" {{ asset('js/comunes.js') }} "></script>


<script type="text/javascript">
var url_images = '{{ asset("storage/articulos") }}';
var no_image = '{{ asset("img/no-imagen.jpg") }}';
var url_delete = '{{ route("delete.descripcion") }}';
var token = '{{ csrf_token() }}';
var url_safe='{{ route("safe.description") }}';
$( document ).ready(function() {
  // $('.tablas').DataTable();
    var pagina_actual=1;
    var _url = "{{ url('/articulos/get/data') }}" + '?categoria='+$('#filtar').val()+'&empresa='+$('#filtar_empresa').val()+'&usuario='+$('#filtar_usuario').val();
    console.log(_url);
    var data = {
        table: "crud-table",
        ajax : _url,
        topMsg: "",
        title: 'Listado de Articulos',
      columns: [
          {data: 'id', name: 'id',title:'#'},
          {data: 'name', name: 'name',title:'Nombre'},
          {data: 'name', name: 'name_co',title:'Nombre 이름'},
          {data: 'codigo', name: 'codigo', title:'Codigo'},
          {data: 'precio_venta', name: 'precio_venta', title:'Precio Ahora'},
          {data: 'existencia', name: 'existencia', title:'Existencia'},
          {data: 'categoria', name: 'categoria', title:'Categoria'},
          {data: 'creadoPor', name: 'creadoPor', title:'Creado por'},
          {data: 'unidad_de_medida', name: 'creadoPor', title:'U.D.M'},
          {data: 'actualizadoPor', name: 'actualizadoPor', title:'Actualizado Por'},
          {data: 'acciones', name: 'acciones', orderable: false, searchable: false, class: 'noexport'}
      ]
    };
    toDataTable(data);  

      function toDataTable(data) {
      $('#' + data.table).DataTable({
          order: [[0, "desc"]],
          responsive: true,
          autoWidth: false,
          ajax: data.ajax,
          columns: data.columns,
          dom: 'Bfrtip',
          initComplete: function () {
          },
          drawCallback: function( settings ) {
              $('[data-toggle="tooltip"]').tooltip();
              $('#tablaCrud_filter label input').on('keydown',function(e) {
                  console.log( 'entra => ' + e.which );
                  if ( (e.which == 78)) {
                      $('#formCrudModal').modal('show');
                  }
              });
          }
      });
  }
});
var myCallback= function paginaActual(){
  console.log("callback")
  $('#crud-table').DataTable().page(pagina_actual).draw('page');

}
function filtrar(filtro=1)  {
        console.log('filtrando');
        var table = $('#crud-table').DataTable();
        pagina_actual = table.page();
        var _newUrl = "{{ url('/articulos/get/data') }}" + '?categoria='+$('#filtar').val()+'&empresa='+$('#filtar_empresa').val()+'&usuario='+$('#filtar_usuario').val();
        table.ajax.url(_newUrl).load();
        if(filtro != 1 ){
          $('#crud-table').DataTable().ajax.reload(myCallback);
        }else{
          $('#crud-table').DataTable().ajax.reload();
        }
    }


@php
    echo ' var listas = '.json_encode($listas).'; ';
    echo ' var articulos = '.json_encode($articulos).'; ';
    echo ' var articulo_lista_fuera = '.json_encode($articulo_lista).'; ';
    echo ' var articulo_imagenes = '.json_encode($articulo_imagenes).'; ';
    echo ' var etiquetas = '.json_encode($etiquetas).'; ';
    echo ' var colores_completos = '.json_encode($colores_completos).'; ';
@endphp
//apertir de aqui haremos lo del select 2 para que se pueda selecionar
var select2Sabore = $('#exampleFormControlSelect1').select2();
var select2Medidas = $('#exampleFormControlSelect2').select2();

$( "#colores" ).click(function() {
  if ($('#id_articulo').attr('value') == "nuevo" ) {
    colores_completos.forEach(function callback(ele, index, array) {
      console.log(ele.name);
      $(ele.name).prop('checked',false);
    });
  }else{
    articulos.forEach(function callback(ele, index, array) {
      if (ele.id == $('#id_articulo').attr('value')) {
          if (ele.colores != null) {
          var coloresarray = ele.colores.split(',');
          coloresarray.forEach(function callback(ele, index, array) {
            $(ele).prop('checked',true);
          })
        }else{
          console.log("vaciar chek")
          colores_completos.forEach(function callback(ele, index, array) {
            $(ele.name).prop('checked',false)
          });
        }
      }
      
    });
  }

  $('#modalmodal').modal('show');
});
//cerramos el modal y guardamos en una variable lo que venga
$( "#GuardarNombre" ).click(function(e){
  e.preventDefault();
  e.stopImmediatePropagation();
  var inputcolor =[];
  var orden = 0;
  $('#form-agregar input').each(function(e,data) {
    if (data.checked == true) {
      inputcolor [orden] ={'name':data.name};
      orden =orden + 1;
    }
  }).promise().done(function(){
   if (inputcolor.length > 0) {
      guardarcolores( {'id':$('#id_articulo').attr('value'), 'colores' :(inputcolor) });
   }else{
      guardarcolores({'id':$('#id_articulo').attr('value'), 'colores' :(inputcolor) });
   }
    
});
 
});
function guardarcolores(input){
var formData = new FormData();
formData.append('data', JSON.stringify(input));

  $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });
  $.ajax({
        type:"POST",
        url: "{{url('/colores/colors/guardar')}}",
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,

        success: function(rta){
          console.log(rta);
          if (rta.cod > 200) {
                $("#mostrar_errores").attr('value',1);
                $("#mostrar_errores").attr('error',rta.msg);
                $("#mostrar_errores").click();
              }else{
                $("#mostrar_errores").attr('value',0);
                $("#mostrar_errores").attr('error',"Colores Actualizados");
                $("#mostrar_errores").click();
                articulos = rta.articulos;
                editame($('#id_articulo').attr('value'));
                $('#modalmodal').modal('hide');


          }

        }
      }).fail( function( jqXHR, textStatus, errorThrown ) {
            if (jqXHR.status === 0) {
                console.log('Not connect: Verify Network.');
            } else if (jqXHR.status == 404) {
                console.log('Requested page not found [404]');
            } else if (jqXHR.status == 500) {
                console.log('Internal Server Error [500].');
            } else if (textStatus === 'parsererror') {
                console.log('Requested JSON parse failed.');
            } else if (textStatus === 'timeout') {
                console.log('Time out error.');
            } else if (textStatus === 'abort') {
                console.log('Ajax request aborted.');
            } else {
                console.log('Uncaught Error: ' + jqXHR.responseText);
            }
    });
}
// ---------------------
//
$( function() {
    $( "#sortable" ).sortable({
      placeholder: "ui-state-highlight", 
      update: function( event, ui ) {
        ordenar_por_id();
      }
    });
    $( "#sortable" ).disableSelection();
  } );
  function ordenar_por_id(){
    var orden_nuevo = 1;
    var orden_contador=0;
    var articulo_imagenes_tabla = [];
    $('#sortable li').each(function(e,data) {
        articulo_imagenes_tabla[orden_contador]={'id_articulo':data.attributes[1].nodeValue,'orden':orden_nuevo,'id_tabla':data.attributes[2].nodeValue}
        orden_nuevo = orden_nuevo + 1;
        orden_contador = orden_contador + 1;
      
    }).promise().done(function(){

        guardarorden(articulo_imagenes_tabla);
      });
  }
  function guardarorden(input){
var formData = new FormData();
formData.append('data', JSON.stringify(input));

  $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });
  $.ajax({
        type:"POST",
        url: "{{url('/imagenes/imagenes/ordenar')}}",
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,

        success: function(rta){
          console.log(rta);
          if (rta.cod > 200) {
                $("#mostrar_errores").attr('value',1);
                $("#mostrar_errores").attr('error',rta.msg);
                $("#mostrar_errores").click();
              }else{
                $("#mostrar_errores").attr('value',0);
                $("#mostrar_errores").attr('error',"Imagen ordenada");
                $("#mostrar_errores").click();
                articulo_imagenes=rta.articulo_imagenes;

            articulo_imagenes.forEach(function callback(ele, index, array) {
              $( "#span_"+ele.id).html(ele.orden);
            });


          }

        }
      }).fail( function( jqXHR, textStatus, errorThrown ) {
            if (jqXHR.status === 0) {
                console.log('Not connect: Verify Network.');
            } else if (jqXHR.status == 404) {
                console.log('Requested page not found [404]');
            } else if (jqXHR.status == 500) {
                console.log('Internal Server Error [500].');
            } else if (textStatus === 'parsererror') {
                console.log('Requested JSON parse failed.');
            } else if (textStatus === 'timeout') {
                console.log('Time out error.');
            } else if (textStatus === 'abort') {
                console.log('Ajax request aborted.');
            } else {
                console.log('Uncaught Error: ' + jqXHR.responseText);
            }
    });
}

//al principio bloqueamos el insertar imagenes
//errores
$( "#mostrar_errores" ).click(function() {
    var algo= $("#mostrar_errores").val();
    var tipo_error= $("#mostrar_errores").attr('error');
    if (algo == 1) {
        var html = '';
            html += '<strong>Error!'+tipo_error+'</strong><br><br>';
            $('#errores_ventas').html(html).promise().done(function(){
                $(this).slideToggle('');
                setTimeout(function(){ $('#errores_ventas').css('display','none'); }, 5000);
            });

    }else{
        var html = '';
                    html += '<strong>'+tipo_error+'</strong><br><br>';
                    $('#success').html(html).promise().done(function(){
                        $(this).slideToggle();
                        setTimeout(function(){ $('#success').css('display','none'); }, 5000);
                    });
    }
});

$("#nuevo_articulo").click(function(){
  $( "#id_articulo" ).attr('value','nuevo'); $( "#cod_articulo" ).val('');
   $( "#nombre_articulo" ).val('');          $( "#nombre_co_articulo" ).val('');
   $( "#desc_articulo" ).val('');            $( "#desc_co_articulo" ).val('');
   $( "#obs_articulo" ).val('');             $( "#obs_co_articulo" ).val('');
   $( "#costo_articulo" ).val('');           $( "#existencia_minima" ).val('');
  $( "#existencia" ).val('');                $( "#tiempo_entrega" ).val('24');
  $( "#desde" ).val('');                     $( "#costo_antes" ).val('');
  $( "#empresa" ).val(1);                    $( "#costo_ahora" ).val('');
  $( "#hasta" ).val('');
  $( "#precio" ).val('');
  $( "#presentacion" ).val('');
  $('#file').attr('disabled',true);
  $("#ocultar").css("display", "none");
  $("#ocultar_imagen").css("display", "none");
  $("#colores").css("display", "none"); 
select2Sabore.val(null).trigger("change");
select2Medidas.val(null).trigger("change");
$("#tbodyid").empty();
  // $('#file').hide();
});
$("#guardar_articulo").click(function(e){
  e.preventDefault();
  e.stopImmediatePropagation();
  CheckAttributes()
  // guardar();
});
$("#cerrar_guardar").click(function(e){
  e.preventDefault();
  e.stopImmediatePropagation();
  CheckAttributes()
  // guardar();

});
function CheckAttributes(){

    if(!$('#unidad_de_medida').val()){
      $("#mostrar_errores").attr('value',1);
                $("#mostrar_errores").attr('error',' seleccione una unidad de medida');
                $("#mostrar_errores").click();
    }else{
      guardar()
    }
}
//primero guardaremos
function guardar(){
var datos={};
var combo=false;
if( $('#es_combo').prop('checked') ) { combo = true;}
var es_activo=false;
if( $('#es_activo').prop('checked') ) { es_activo = true;}
var colors='';
$('#plano_colores .color_code').each(function(e,data){
  if( $(this).prop('checked') ) { colors += $(this).prop('name')+',' }
})
datos={
 id_articulo : $( "#id_articulo" ).val(),
 cod_articulo : $( "#cod_articulo" ).val(),
 categoria : $( "#categoria" ).val(),
 nombre_articulo : $( "#nombre_articulo" ).val(),
 nombre_co_articulo : $( "#nombre_co_articulo" ).val(),
 desc_articulo : $( "#desc_articulo" ).val(),
 desc_co_articulo : $( "#desc_co_articulo" ).val(),
 obs_articulo : $( "#obs_articulo" ).val(),
 obs_co_articulo : $( "#obs_co_articulo" ).val(),
 costo_articulo : $( "#costo_articulo" ).val(),
 costo_antes : $( "#costo_antes" ).val(),
 costo_ahora : $( "#costo_ahora" ).val(),
 tiempo_entrega : $( "#tiempo_entrega" ).val(),
 tiempo_entrega_id:$('#plazo_entrega_id').val(),
 unidad_de_medida:$('#unidad_de_medida').val(),
 existencia_minima:$( "#existencia_minima" ).val(),
 sabores:$('#exampleFormControlSelect1').val(),
 medidas:$('#exampleFormControlSelect2').val(),
//  colores:$('#exampleFormControlSelect3').val(),
 colores:colors,
 es_activo:es_activo,

desde:$( "#desde" ).val(),
hasta:$( "#hasta" ).val(),
precio:$( "#precio" ).val(),
empresa:$( "#empresa" ).val(),
presentacion:$( "#presentacion" ).val(),
 es_combo :combo,
 existencia:$( "#existencia" ).val(),
 lista_precio:null,
 etiqueta_id:null,
}
var lista_precio=[];
var contador=0;
if ($( "#id_articulo" ).val() != 'nuevo') {
  listas.forEach(function callback(ele, index, array) {
      var valor=null;
      valor=$( "#lista_"+ele.id ).val();
      lista_precio[contador]={"id": ele.id,"valor":valor};
      contador=contador+1;
   });
    datos.lista_precio=JSON.stringify(lista_precio);
    datos.etiqueta_id=$( "#etiqueta" ).val();
    enviar(datos);
}else{
  enviar(datos);
}

}
function enviar(datos){
$.ajax({
        type:"GET",
        url: "{{url('/articulos/save/Guardar/')}}",
        type: 'GET',
        data: datos,

        success: function(rta){
          if (rta.cod_retorno > 200) {
                $("#mostrar_errores").attr('value',1);
                $("#mostrar_errores").attr('error',rta.des_retorno);
                $("#mostrar_errores").click();
              }else{
                $("#mostrar_errores").attr('value',0);
                $("#mostrar_errores").attr('error',rta.des_retorno);
                $("#mostrar_errores").click();
                listas = rta.listas;
                articulos = rta.articulos;
                articulo_lista_fuera=rta.articulo_lista;
                articulo_imagenes=rta.articulo_imagenes;
                etiquetas=rta.etiquetas;
                
                editame(rta.id);
                $("#"+rta.id).remove();
                filtrar(2);
                $('#modal-cargar-articulos').modal('hide');
          }
        }

      }).fail( function( jqXHR, textStatus, errorThrown ) {
            if (jqXHR.status === 0) {
                console.log('Not connect: Verify Network.');
            } else if (jqXHR.status == 404) {
                console.log('Requested page not found [404]');
            } else if (jqXHR.status == 500) {
                console.log('Internal Server Error [500].');
            } else if (textStatus === 'parsererror') {
                console.log('Requested JSON parse failed.');
            } else if (textStatus === 'timeout') {
                console.log('Time out error.');
            } else if (textStatus === 'abort') {
                console.log('Ajax request aborted.');
            } else {
                console.log('Uncaught Error: ' + jqXHR.responseText);
            }
    });
}

//---scrit validar imagen

var banderaTamano = false;
$("#file").change(function(e) {
  e.preventDefault();
  e.stopImmediatePropagation();
    validar();
});

function validar() {
  var o = document.getElementById('file');
  var foto = o.files[0];
  var c = 0;
  if (o.files.length == 0 || !(/\.(jpg|png|jpeg)$/i).test(foto.name)) {
    alert('Ingrese una imagen con alguno de los siguientes formatos: .jpeg/.jpg/.png.');
    return false;
  }
  // Si el tamaño de la imagen fue validado
  if (banderaTamano) {
    return true;
  }
  var img = new Image();
  img.onload = function dimension() {
    if (this.width.toFixed(0) <= 3000 && this.height.toFixed(0) <= 3000) {
        banderaTamano = true;
        // readFile(o);
        guardaimagen(o);
    } else {
      alert('Las medidas deben ser menor o igual a: 300 x 300');
    }
  };
  img.src = URL.createObjectURL(foto);
  return false;
}

//llamamos al metodo guardar
function guardaimagen(input){
$avatarInput = $('#file');
var formData = new FormData();
formData.append('file', $avatarInput[0].files[0]);
formData.append('id', $( "#id_articulo" ).val());

  $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });
  $.ajax({
        type:"POST",
        url: "{{url('/imagenes/imagenes/guardar')}}",
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,

        success: function(rta){

          if (rta.cod > 200) {
                $("#mostrar_errores").attr('value',1);
                $("#mostrar_errores").attr('error',rta.msg);
                $("#mostrar_errores").click();
              }else{
                $("#mostrar_errores").attr('value',0);
                $("#mostrar_errores").attr('error',"Imagen Guardada");
                $("#mostrar_errores").click();
                articulo_imagenes=rta.dta;
                actualizar_imagenes(articulo_imagenes);
                banderaTamano=false;

          }

        }
      }).fail( function( jqXHR, textStatus, errorThrown ) {
            if (jqXHR.status === 0) {
                console.log('Not connect: Verify Network.');
            } else if (jqXHR.status == 404) {
                console.log('Requested page not found [404]');
            } else if (jqXHR.status == 500) {
                console.log('Internal Server Error [500].');
            } else if (textStatus === 'parsererror') {
                console.log('Requested JSON parse failed.');
            } else if (textStatus === 'timeout') {
                console.log('Time out error.');
            } else if (textStatus === 'abort') {
                console.log('Ajax request aborted.');
            } else {
                console.log('Uncaught Error: ' + jqXHR.responseText);
            }
    });
}
function actualizar_imagenes(articulo_imagenes){
  var imagenes_insert="";
  articulo_imagenes.forEach(function callback(ele, index, array) {
        if (ele.articulo_id == $( "#id_articulo" ).val()) {
         imagenes_insert+= '<li orden='+ele.orden+' articulo_id='+ele.articulo_id+' id_tabla='+ele.id+' class=""><div id='+ele.path+' class="button-container"><img src="{!! asset("storage/articulos/'+ele.path+'") !!}" width="90px" height="90px" > <a style="text-align: center;" onclick=eliminame("'+ele.path+'"); >X</a><span id=span_'+ele.id+'>'+ele.orden+'</span></div></li>' ;
        }
   });
   $('#sortable').each(function(e,data) {
            if (data.innerHTML !="") {
            data.innerHTML=="";
            }
    });
    $("#sortable img").html('').promise().done(function(){
      $("#sortable").html(imagenes_insert);
    });
 
}

function editame(id){
  $('#sortable').each(function(e,data) {
                   if (data.innerHTML !="") {
                    data.innerHTML=="";
                   }
    });
  //añadimos select dos para que se pueda saber que se selecciono
    select2Sabore.val(null).trigger("change");
    select2Medidas.val(null).trigger("change");
  //
  articulos.forEach(function callback(ele, index, array) {
          if (ele.id == id) {
          //recorremos y vemos cuantas comas existen
          if (ele.sabores != null ) {
            var saboresarray = ele.sabores.split(',');
            select2Sabore.val(saboresarray).trigger("change");
          }
         //
          if (ele.medidas != null) {
            var medidasarray = ele.medidas.split(',');
            select2Medidas.val(medidasarray).trigger("change");
          }
          //
          $("#tbodyid").empty();
          if (ele.colores != null) {
            var coloresarray = ele.colores.split(',');
            coloresarray.forEach(function callback(ele, index, array) {
              $('#plano_colores .color_code').each(function(e,data){
                if( $(this).prop('id') == ele ) { $(this).prop('checked',true);  }
              })
            })
          }else{
            $('#plano_colores .color_code').each(function(e,data){
             $(this).prop('checked',false);
            })
          }
         
          if (ele.es_activo == false) {
            $("#es_activo").prop("checked", false);
          }else{
            $("#es_activo").prop("checked", true);
          }
          if (ele.es_combo == false) {
            $("#es_combo").prop("checked", false);
          }else{
            $("#es_combo").prop("checked", true);
          }
          if (ele.timer_desde !=null) {
          var d= new Date( ele.timer_desde );
          var h= new Date( ele.timer_hasta );
          d = d.toJSON().slice(0,10);
          h = h.toJSON().slice(0,10);
          $( "#desde" ).val(d);  
          $( "#hasta" ).val(h);
          }else{
            $( "#desde" ).val("");  
          $( "#hasta" ).val("");
          }
         
          if (ele.etiqueta_id == null) {
            $('#img_muestra').css("display", "none");
          }else{
            $('#img_muestra').css("display", "block");
          }
          $("#colores").css("display", "block");
          $( "#id_articulo" ).attr('value',id);            $( "#cod_articulo" ).val(ele.codigo);
          $( "#nombre_articulo" ).val(ele.name);           $( "#nombre_co_articulo" ).val(ele.name_co);
          $( "#desc_articulo" ).val(ele.descripcion);      $( "#desc_co_articulo" ).val(ele.descripcion_co);
          $( "#obs_articulo" ).val(ele.observaciones);     $( "#obs_co_articulo" ).val(ele.observaciones_co);
          $( "#costo_articulo" ).val(ele.costo);           $( "#existencia" ).val(ele.existencia);
           $( "#presentacion" ).val(ele.presentacion);
                                $( "#empresa" ).val(ele.empresa_id);
          $( "#categoria" ).val(ele.categoria_id);
          
          $( "#precio" ).val(ele.timer_precio);
          $('#file').attr('disabled',false);
          $("#ocultar").css("display", "block");
          $("#ocultar_imagen").css("display", "block");
          $("#file").show();
          // $("#file").attr("type", "file");
          $("#sortable img").html('');

          $( "#costo_antes" ).val(ele.precio_antes);
          $( "#costo_ahora" ).val(ele.precio_venta);
          $( "#tiempo_entrega" ).val(ele.plazo_entrega);
          $( "#tiempo_entrega_id" ).val(ele.plazo_entrega_id);
          $( "#unidad_de_medida" ).val(ele.unidad_de_medida);

          $( "#existencia_minima" ).val(ele.existencia_minima);
          if (ele.etiqueta_id == null) {
            var html= '{!!asset("storage/img/106804.png")!!}'
            $('#img_muestra').attr('src',html);
            $( "#etiqueta" ).val("sin_etiqueta");
          }else{
            $( "#etiqueta" ).val(ele.etiqueta_id);
          }



        }
   });
   etiquetas.forEach(function callback(ele, index, array) {
                if(ele.id == ($("#etiqueta").val()) ){
                  var html= '{!!asset("storage/img/'+ele.path+'")!!}'
                  $('#img_muestra').attr('src',html);
                }
    });

   etiquetas.forEach(function callback(ele, index, array) {
                if(ele.id == ($("#etiqueta").val()) ){
                  var html= '{!!asset("storage/img/'+ele.path+'")!!}'
                  $('#img_muestra').attr('src',html);
                }
            })
   articulo_lista_fuera.forEach(function callback(ele, index, array) {
        if (ele.articulo_id==id) {
          $( "#lista_"+ele.lista_id ).val(ele.costo);
        }
   });
  var contador=0;
  var imagenes_insert="";
   (sortJSON(articulo_imagenes,'orden','asc')).forEach(function callback(ele, index, array) {
        if (ele.articulo_id == id) {
         imagenes_insert+= '<li orden='+ele.orden+' articulo_id='+ele.articulo_id+' id_tabla='+ele.id+' class=""><div id='+ele.path+' class="button-container"><img src="{!! asset("storage/articulos/'+ele.path+'") !!}" width="90px" height="90px" > <a style="text-align: center;" onclick=eliminame("'+ele.path+'"); >X</a><span id=span_'+ele.id+'>'+ele.orden+'</span></div></li>' ;
        }
   })
   console.log()
  $("#sortable").html(imagenes_insert).promise().done(function(){
  $('#modal-cargar-articulos').modal('show');
  });
}
function sortJSON(data, key, orden) {
    return data.sort(function (a, b) {
        var x = a[key],
        y = b[key];

        if (orden === 'asc') {
            return ((x < y) ? -1 : ((x > y) ? 1 : 0));
        }

        if (orden === 'desc') {
            return ((x > y) ? -1 : ((x < y) ? 1 : 0));
        }
    });
}

function eliminame(parametro){
  $.ajax({
        type:"GET",
        url: "{{url('/imagenes/imagenes/eliminar_imagen')}}",
        type: 'GET',
        data: {id:parametro},

        success: function(rta){
          if (rta.cod_retorno > 200) {
                $("#mostrar_errores").attr('value',1);
                $("#mostrar_errores").attr('error',rta.des_retorno);
                $("#mostrar_errores").click();
              }else{
                $("#mostrar_errores").attr('value',0);
                $("#mostrar_errores").attr('error',rta.des_retorno);
                $("#mostrar_errores").click();
                var el = document.getElementById(parametro);
                el.remove();
                articulo_imagenes=rta.dta;

          }
        }
      }).fail( function( jqXHR, textStatus, errorThrown ) {
            if (jqXHR.status === 0) {
                console.log('Not connect: Verify Network.');
            } else if (jqXHR.status == 404) {
                console.log('Requested page not found [404]');
            } else if (jqXHR.status == 500) {
                console.log('Internal Server Error [500].');
            } else if (textStatus === 'parsererror') {
                console.log('Requested JSON parse failed.');
            } else if (textStatus === 'timeout') {
                console.log('Time out error.');
            } else if (textStatus === 'abort') {
                console.log('Ajax request aborted.');
            } else {
                console.log('Uncaught Error: ' + jqXHR.responseText);
            }
    });

}
function eliminar_articulo(parametro){
parametro=$('#id_eliminar').val();
  $.ajax({
        type:"GET",
        url: "{{url('/imagenes/imagenes/eliminar')}}",
        type: 'GET',
        data: {id:parametro},

        success: function(rta){
          $('#modal_e').modal('hide');
          if (rta.cod_retorno > 200) {
                console.log(rta);
                alert(rta.des_retorno);
                $("#mostrar_errores").attr('value',1);
                $("#mostrar_errores").attr('error',rta.des_retorno);
                $("#mostrar_errores").click();
              }else{
                $("#mostrar_errores").attr('value',0);
                $("#mostrar_errores").attr('error',rta.des_retorno);
                $("#mostrar_errores").click();
                filtrar(2);
                // setTimeout(function(){location.reload(true); }, 1000);

          }
        }
      }).fail( function( jqXHR, textStatus, errorThrown ) {
            if (jqXHR.status === 0) {
                console.log('Not connect: Verify Network.');
            } else if (jqXHR.status == 404) {
                console.log('Requested page not found [404]');
            } else if (jqXHR.status == 500) {
                console.log('Internal Server Error [500].');
            } else if (textStatus === 'parsererror') {
                console.log('Requested JSON parse failed.');
            } else if (textStatus === 'timeout') {
                console.log('Time out error.');
            } else if (textStatus === 'abort') {
                console.log('Ajax request aborted.');
            } else {
                console.log('Uncaught Error: ' + jqXHR.responseText);
            }
    });

}
$("#etiqueta").change(function() {
  if (($(this).val()) !=0 ) {
    etiquetas.forEach(function callback(ele, index, array) {
                if(ele.id == ($("#etiqueta").val()) ){
                  var html= '{!!asset("storage/img/'+ele.path+'")!!}'
                  $('#img_muestra').attr('src',html);
                }
            });
  }

});
function modalOpen(id){
    $('#id_eliminar').val(id);
    $('#modal_e').modal('show');
}


</script>
@endsection
