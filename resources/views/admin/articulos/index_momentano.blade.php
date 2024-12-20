@extends('layouts.admin')
@section('styles')
<link href="{{ asset('vendor/jquery-ui-1.12.1/jquery-ui.min.css') }}" rel="stylesheet">
@endsection
@section('main-content')
@if (session('status'))
    <div class="alert alert-success">
        {{ session('status') }}
    </div>
@endif
<div class="card" id="ocultar">
   <div class="card-body">
    <style>
        .cancelar  {
                            display: none;
                      }
    </style>
    <button class="btn btn-primary cancelar mb-2" id="cancelar">Cancelar Edicion</button>
       <form action="{{url('/categorias/articulos/save')}}" enctype="multipart/form-data" method="POST">
        @csrf
        <div class="form-group ">
            <label for="padre">Categoria Principal</label>
            <select name="padre" id="padre" class="custom-select">
                <option value="0">Categoria Principal</option>
                  @php
                      echo $anidada;
                  @endphp
              </select>
            </select>
        </div>
        <h5 class="card-title" id="cabecera">Nuevo</h5>
        <input type="hidden" id="id_categoria" name="id_categoria">
    <div class="form-group">
        <label for="name">Name</label>
        <input type="text" class="form-control" id="name"  name="name" >
    </div>
    <div class="form-group ">
        <label for="exampleInputEmail1">Name Coreano</label>
        <input type="text" class="form-control" id="name_co"  name="name_co" >
    </div>

    <button id="orden" type="button" class="btn btn-primary mb-2" data-toggle="modal" data-target="#crudModal">
        Orden
    </button>

    <div class="form-group">
        <label for="exampleInputEmail1">Imagen</label>
        <input type="file" class="form-control" id="path"  name="path" >
    </div>
    <div class="form-group ">
        <label for="icono_id">Icono</label>
        <select name="icono_id" id="icono_id" class="custom-select" required>
            <option value="" selected disabled>Seleccione un icono</option>
                @foreach ($iconos as $i)
                    <option value="{{$i->id}}">{{$i->name}}</option>
                @endforeach
          </select>
        </select>
    </div>
    <style>
        .imagenes  {
                            display: none;
                      }
    </style>
        <img id="img_muestra" class=" imagenes" src="" class="rounded-circle" alt="user-image" width="50px" height="50px">
        <button class=" btn btn-primary"> Guardar</button>
    </form>
   </div>    
</div>

<div class="card shadow mb-3 col-12 ">
    <div class=" form-inline">
    <h5 class="card-title mr-1" id="abrir"><button class="btn btn-primary">Crear Nuevo+</button></h5>
    <h5 class="card-title"><a href="{{url('/categorias/ordenar')}}" class="btn btn-primary">Ordenar Categoria</a></h5>
    </div>
    <div class="card-body">
        <table id="tabla_">
            <thead>
                <tr>
                    <td>#id</td>
                    <td>Nombre</td>
                    <td>Nombre Coreano</td>
                    <td>Categoria Principal</td>
                    <td>Logo</td>
                    <td>Images</td>
                    <td class=" ml-5">Acciones</td>
                </tr>
            </thead>
            <tbody>
                @foreach ($categorias as $i)
                    <tr>
                        <td>{{$i->id}}</td>
                        <td>{{$i->name}}</td>
                        <td>{{$i->name_co}}</td>
                        <td>{{$i->padre}}</td>
                        <td>
                            @if ($i->imagen != null )
                            <img id="img_muestra" src="{{ asset('/storage/categorias/'.$i->imagen) }}"  alt="user-image" width="50px" height="50px"></td>
                            @else
                            Sin Logo
                            @endif
                        </td>
                        @if ($i->path == null)
                        <td>Sin icono</td> 
                        @else
                        <td><img src="{{ asset('/storage/img/'.$i->path) }}" alt="user-image" width="50px" height="50px"></td>
                        @endif
                        <td style="text-align: center"><a onclick=" editar_articulo( {{$i->id}} ); "><i class="fas fa-edit mr-5"></i></a>
                        <a href="{{url('/categoria/articulos/eliminar',['id'=>$i->id])}}"><i class="far fa-trash-alt"></i></a></td>
                        </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>  
{{--  --}}
<div class="modal fade" id="crudModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <h5 class="modal-title">
            <div id='errores_ventas' style='display:none' class='alert alert-danger' ></div>
            <div id='success' style='display:none;' style="background: #4e73df  !important;" class='alert alert-primary'></div>
            <input  class="btn button " type="hidden" id="mostrar_errores" value="1" disabled error="">
        </h5>
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="myModalLabel">Ordenar</h4>
        </div>
        <div class="modal-body">
            <ul id="sortable">
            </ul>
          <div id='myModalMsg'></div>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('script')
<script src="{{ asset('vendor/jquery-ui-1.12.1/jquery-ui.min.js') }}">
</script>
<script type="text/javascript">
$( document ).ready(function() {
    $("#ocultar").css("display", "none");
@php
  echo ' var iconos = '.json_encode($iconos).'; ';
  echo ' var categorias = '.json_encode($categorias).'; ';
@endphp
    $('#tabla_').DataTable();

$("#icono_id").change(function() {

          iconos.forEach(function callback(ele, index, array) {
                if(ele.id == ($("#icono_id").val()) ){
                  var html= '{!!asset("storage/img/'+ele.path+'")!!}'
                  console.log(ele.path);
                    $('#img_muestra').css("display", "block"); 
                    $('#img_muestra').attr('src',html); 

                }
            });

    })
});
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

@php
  echo ' var iconos = '.json_encode($iconos).'; ';
  echo ' var categorias = '.json_encode($categorias).'; ';
@endphp

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
        articulo_imagenes_tabla[orden_contador]={'id_tabla':data.attributes[1].nodeValue,'orden':orden_nuevo}
        orden_nuevo = orden_nuevo + 1;
        orden_contador = orden_contador + 1;
    }).promise().done(function(){
        // console.log(articulo_imagenes_tabla);
        guardarorden(articulo_imagenes_tabla);
      });
  }
  //------------
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
//-------------------
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
        url: "{{url('/imagenes/imagenes/ordenar/categorias')}}",
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
                $("#mostrar_errores").attr('error',rta.msg);
                $("#mostrar_errores").click();
                categorias = rta.dta;

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

//-----------------------------------------
function editar_articulo(parametro){
    $("#orden").css("display", "none");
    $('#sortable').each(function(e,data) {
                if (data.innerHTML !="") {
                data.innerHTML=="";
        }
    });
    $("#sortable li").html('');
    var contador=0;
    var padre=null;
    var categoria_id=null;
    var orden_insert='';
    (sortJSON(categorias,'orden','asc')).forEach(function callback(ele, index, array) {
        if (ele.id == parametro) {
            padre=ele.padre_id;
            categoria_id=ele.id;
        }
    })
    if (padre == null) {
        //pertenece a la categoria principal  
        (sortJSON(categorias,'orden','asc')).forEach(function callback(ele, index, array) {
            if (ele.padre_id == null) {
                orden_insert+= '<li orden='+ele.orden+' id_tabla='+ele.id+' class="">'+ele.name+'</li>';   
            }
        })   
    }else{
    (sortJSON(categorias,'orden','asc')).forEach(function callback(ele, index, array) {
                if (ele.padre_id == padre) {
                    orden_insert+= '<li orden='+ele.orden+' id_tabla='+ele.id+' class="">'+ele.name+'</li>';   
                }
            }) 
        }
        $("#sortable").html(orden_insert).promise().done(function(){
        });
    //------------------
    $('html, body').animate({scrollTop: 0}, 600);
    $('#id_categoria').attr('value',parametro); 

    categorias.forEach(function callback(ele, index, array) {
        if(ele.id == parametro){
            $('#cancelar').css("display", "block");
            $('#cabecera').html("Editar categoria "+ele.name);
            $('#name').val(ele.name);
            $('#name_co').val(ele.name_co);
            if (ele.path != null) {
            var html= '{!!asset("storage/img/'+ele.path+'")!!}'
            console.log(html);
                $('#img_muestra').attr('src',html);   
                $('#icono_id').val(ele.icono_id);
            }else{
                $('#icono_id').val(0); 
            }

            if (ele.padre_id != null) {
                $('#padre').val(ele.padre_id);   
            }else{
                $('#padre').val(0); 
            }
            $("#ocultar").css("display", "block");     
            $("#abrir").attr('disabled',true)     
        }
    });

}
$("#cancelar").on("click", function(){
    location.reload(true);
})
$("#abrir").on("click", function(){
    $("#ocultar").css("display", "block");
    $("#orden").css("display", "none");
    $("#abrir").attr('disabled',true)
})
</script>

@endsection