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
<div class="contaier form-inline">

    <div class="card shadow mb-3 col-10 ">
        <div class="card-header">Ordenar Categorias</div>

        <form action="{{url('/categorias/buscar')}}"  method="POST">
            @csrf
            <div class="card-body">
                <div class="form-group ">
                    <label for="exampleFormControlSelect1" class="mr-3">Categorias</label>
                    <select class="form-control" id="exampleFormControlSelect1" name="categoria">
                        <option value="0">Categoria Principal</option>
                        @php
                            echo $selectdos;
                        @endphp
                        {{-- @foreach ($categorias as $cat)
                        <option value="{{$cat->id}}">{{$cat->name}}</option>
                        @endforeach --}}
                    </select>
                </div>
                <button id="buscar" type="submit" class="btn btn-success mt-4">ok</button>
            </div>
        </form>


    </div>
    @if (isset($categoria))
    <div class="card shadow mt-2  col-10 ">
        <div id='errores_ventas' style='display:none' class='alert alert-danger' ></div>
            <div id='success' style='display:none;' style="background: #4e73df  !important;" class='alert alert-primary'></div>
            <input  class="btn button " type="hidden" id="mostrar_errores" value="1" disabled error="">
            <input type="hidden" value="{{$categoria}}" id="categoria">
            <div id="categoria_especifica">
                @if ($categoria_name != null)
                <h5 class=" mb-2">{{$categoria_name->name}}</h5>
                @else
                <h5 class=" mb-2">Categoria Principal</h5>
                @endif
               
            </div>

        <ul id="sortable">
        </ul>
    </div>
    @endif

</div>

@endsection
@section('script')
<script src="{{ asset('vendor/jquery-ui-1.12.1/jquery-ui.min.js') }}">
</script>
<script>
$( document ).ready(function() {

    $('#exampleFormControlSelect1').select2();

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
  echo ' var categorias = '.json_encode($categorias).'; ';
@endphp
if ($("#sortable").length > 0) {
    var contador=0;
    var orden_insert='';
if ($("#categoria").val() == 0) {
        //pertenece a la categoria principal  
        (sortJSON(categorias,'orden','asc')).forEach(function callback(ele, index, array) {
            if (ele.padre_id == null) {
                orden_insert+= '<li orden='+ele.orden+' id_tabla='+ele.id+' class="list-group-item">'+ele.name+'</li>';   
            }
        })   
    }else{
    (sortJSON(categorias,'orden','asc')).forEach(function callback(ele, index, array) {
                if (ele.padre_id == $("#categoria").val()) {
                    orden_insert+= '<li orden='+ele.orden+' id_tabla='+ele.id+' class="list-group-item">'+ele.name+'</li>';   
                }
            }) 
    }
    if (orden_insert.length > 0 ) {
        $("#sortable").html(orden_insert).promise().done(function(){
    });
    }else{
        $("#sortable").html('No existen registros en esta Categoria');
    }
   


//-----------
$( function() {
    $( "#sortable" ).sortable({
      placeholder: "ui-state-highlight", 
      update: function( event, ui ) {
        ordenar_por_id();
      }
    });
    $( "#sortable" ).disableSelection();
  } );
  //-------
  
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
        url: "{{url('/categoria/categorias/ordenar/categorias')}}",
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
//----------------
    }//if--if
});//
</script>   
@endsection