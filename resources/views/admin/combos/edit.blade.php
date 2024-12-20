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

    <div id='errores_ventas' style='display:none' class='alert alert-danger'></div>
    <div id='success' style='display:none;' style="background: #4e73df  !important;" class='alert alert-primary'></div>
    <input class="btn button " type="hidden" id="mostrar_errores" value="1" disabled error="">

<div class="card shadow mb-3 col-10 ">
        <div class="card shadow mb-6">
            <div class="card-header py-3" style="padding: 5px 10px 5px 10px !important;" id="detalles">
                <h4>{{$combo->name}} / {{$combo->name_co}}</h4>
            </div>
            <div class="card-body">
                <div class="col-sm-11 ">
                <input type="hidden" value="{{$combo->id}}" id="combo_id">
                    <table class="col col-7 " id="tabla-venta">
                        <thead>
                            <tr>
                                <td colspan="2">
                                    <div class=" form-inline">
                                    <div class="col col-10 "><h5>Articulo</h5> </div>  
                                    <div class="col col-2 "><h5>cantidad</h5></div> 
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="form form-inline col-12">
                                        <input id="articulo" type="text" 
                                                class="form-control col col-10" tocado="no">
                                            <input id="cantidad" value="1" 
                                            class="form-group input-lg form-control col-2">
                                    </div>
                                </td>
                            </tr>
                            {{--  --}}
                            @foreach ($cargados as $articu)
                            {{-- esto en realidad es ya el id del detalle --}}
                                <tr id="{{$articu->articulo_id}}" contador="{{$articu->cantidad}}">
                                    <td >
                                        <div class="form form-inline col-12">
                                            <input  type="text" value='{{$articu->articulo}}'  class="form-control col col-10"  disabled>
                                            <input  value='{{$articu->cantidad}}'  class="form-group input-lg form-control col-2" disabled>
                                            <td ><a type="button" onclick="eliminame({{$articu->articulo_id}})" > <i class="far fa-times-circle"></i></a></td>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            {{--  --}}
                          
                        </thead>
                            <tbody id="tbodyid">  
                            </tbody>
                    </table>
                </div>
                <button id="guardar_inventario" class="btn btn-success mt-4">Guardar</button>
            </div>
    
        </div>
    </div>

</div>

@endsection
@section('script')
<script src="{{ asset('vendor/jquery-ui-1.12.1/jquery-ui.min.js') }}"></script>
<script>
$( document ).ready(function() {
@php
    echo ' var articulos = '.json_encode($articulos).'; ';
    echo ' var select = '.json_encode($select).'; ';
    echo ' var cargados = '.json_encode($cargados).'; ';
@endphp
var cantidad= 0;
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
$( "#articulo" ).autocomplete({
      source:(select),
        select: function(){
    }
}).promise().done(function(){
    esperar_cambios();
});

function esperar_cambios(){ 
var name = null ;
var articulo= null;
$('#articulo').on('keypress', function (event) {
    if(event.which == 13){ $("#cantidad").focus();}
});
$("#articulo").change(function() {

        if ($("#articulo").val().length != 0 ) {
        var producto_seleccionado = $("#articulo").val();
        console.log(producto_seleccionado);

            if (isNaN(producto_seleccionado)) {
                select.forEach(function callback(ele, index, array) {
                        if(ele.label == producto_seleccionado){
                            articulo=ele.id
                            name= ele.label     
                        }
                    });
                }
                if (articulo == null) {
                    articulos.forEach(function callback(ele, index, array) {
                            if(ele.name == producto_seleccionado){
                                articulo=ele.id; 
                                name= ele.name; 
                                
                        }
                    });
                }      
            
            if (articulo != null) {
                $("#cantidad").focus();
            }else{
                console.log("vacio");
                $("#articulo").val('');
            }
            
    }else{
        $('#articulo').focus();
    }
});
$('#cantidad').on('input', function () { 
    this.value = this.value.replace(/[^0-9]/g,'');
});

$('#cantidad').on('keypress', function (event) {
if(event.which == 13){
    
            if(articulo !=null) {
            var cantidad= $("#cantidad").val();
            if (cantidad < 1) {
                cantidad = 1;
            }
            var existe= 1; 
                $('#tabla-venta tr').each(function(e,data) {
                        if (data.id == articulo) { 
                            existe=2;
                    }
                    
                }).promise().done(function(){
                    if (existe != 2) {
                    var fila = '';
                    fila += '<tr id='+articulo+' cantidad='+cantidad+'>';
                        fila += '<td >';
                        fila += '<div class="form form-inline col-12">';
                        fila +=     '<input  type="text" value='+name+'  class="form-control col col-10" disabled>';
                        fila +=     '<input  value='+cantidad+'  class="form-group input-lg form-control col-2" disabled>';
                        fila +=    '<td ><a type="button" onclick= eliminame(\''+articulo+'\') > <i class="far fa-times-circle"></i></a></td>';
                        fila += '</div>';
                    fila += '</td>';
                    fila +='</tr>';
                    $("#tabla-venta >tbody").append(fila);
                    $("#articulo").val("");
                    $("#cantidad").val("");
                    $("#articulo").focus();  
                    name = null ;
                    producto= null;
            }else{
                $("#articulo").focus();
                $("#articulo").val('');
                $("#articulo").attr('placeholder','Producto Existente');
                console.log("producto existente");
                name = null ;
                producto= null;
            }
          });

        }else{
            $("#articulo").focus();
            console.log("vacio");
     }  
    }
  });


}//esperar cambios

});//documnt ready
@php
    echo ' var articulos = '.json_encode($articulos).'; ';
    echo ' var select = '.json_encode($select).'; ';
    echo ' var cargados = '.json_encode($cargados).'; ';
@endphp

$("#guardar_inventario").click(function(e){

    e.preventDefault();
    e.stopImmediatePropagation();
    var datos={}; var articulos =[]; 
    var contador_filas=0; combo= $("#combo_id").val();
    $('#tabla-venta tr').each(function(i,data) {
    if (i != 0 && i != 1) {
        articulos[contador_filas] ={'articulo': data.id ,'cantidad':data.attributes[1].value};
        contador_filas=(contador_filas + 1);
    }
    
    
}).promise().done(function(){
    var input_no_vacio=verificar_escrito();
    console.log(input_no_vacio);
    var cantidad= $("#cantidad").val();
            if (cantidad < 1) {
                cantidad = 1;
            }
    if (input_no_vacio != false ) {
        articulos[contador_filas]={'articulo': input_no_vacio ,'cantidad':cantidad};
    }
if ( (articulos.length > 0) && (combo.length > 0 )) {
datos= {datos:JSON.stringify(articulos),combo:combo}
console.log(datos);
$.ajax({
        type:"GET",
        url: "{{url('combos/save')}}",
        type: 'GET',
        data: datos,
        
        success: function(rta){
          console.log(rta);
          if (rta.cod > 200) {
                $("#mostrar_errores").attr('value',1);
                $("#mostrar_errores").attr('error',rta.msg);
                $("#mostrar_errores").click();   
              }else{
                $(location).attr('href','{!!url("combos/")!!}');
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

    
    }else{
        alert("Por favor verifique todos los campos no se selecciono ningun articulo")
    }
});
return false;

});
function verificar_escrito(){
    if ($("#articulo").val().length != 0 ) {
        var producto_seleccionado = $("#articulo").val();
        console.log(producto_seleccionado);

            if (isNaN(producto_seleccionado)) {
                select.forEach(function callback(ele, index, array) {
                        if(ele.label == producto_seleccionado){
                            articulo=ele.id
                            name= ele.label     
                        }
                    });
                }
                if (articulo == null) {
                    articulos.forEach(function callback(ele, index, array) {
                            if(ele.name == producto_seleccionado){
                                articulo=ele.id; 
                                name= ele.name; 
                                
                        }
                    });
                }      
            
            if (articulo != null) {
                console.log("articulo",articulo);
                var existe=0;
                $('#tabla-venta tr').each(function(i,data) {
                    if (i != 0 && i != 1) {
                        if (data.id == articulo ) {
                            existe=1;
                        }
                    }
                })
                    if (existe == 0) {
                        return articulo;
                    }else{
                        return false;
                    }
            
                
            }else{
                return false;
            }
            
    }else{
        return false;
    }
}
function eliminame(parametro){
    
    $("#"+parametro).remove();
}

</script>
@endsection