@extends('layouts.admin')
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
        <div class="card-header">Ajustes de Existencia</div>

        <form action="{{url('ajustes/buscar')}}">

            <div class="card-body">
                <div class="form-group mb-5">
                    <label for="exampleFormControlSelect1" class=" mr-4">Empresas</label>
                    <select class="form-control" id="exampleFormControlSelect1" name="empresa">
                        @foreach ($empresas as $emp)
                        <option value="{{$emp->id}}">{{$emp->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group mt-5">
                    <label for="exampleFormControlSelect1" class="mr-3">Categorias</label>
                    <select class="form-control" id="exampleFormControlSelect1" name="categoria">
                        @foreach ($categorias as $cat)
                        <option value="{{$cat->id}}">{{$cat->name}}</option>
                        @endforeach
                    </select>
                </div>
                <button id="buscar" type="submit" class="btn btn-success mt-4">FILTAR</button>
            </div>
        </form>


    </div>
    @if (isset($buscar))
    <div class="card shadow mb-3 col-10 ">
        <table class="table" id="tabla-venta">
            <thead>
                <tr>
                    <td>#</td>
                    <td>Producto</td>
                    <td>Cantidad</td>
                </tr>
            </thead>
            <tbody>
                <form action="{{url('ajustes/guardar/cambios')}}" method="POST" id="myform">
                    @csrf
                    <input class="form-control" id="empresa_grnde" name="empresa" type="hidden" value="{{$empresa}}">
                    <input class="form-control" name="categoria" type="hidden" value="{{$categoria}}">
                    @foreach ($buscar as $bu)
                    <input type="hidden" name="articulos[{{$bu->id}}][id]"  id="opcion[][id]"     value="{{$bu->id}}" >
                    <input type="hidden" name="articulos[{{$bu->id}}][name]"id="opcion[][name]"   value="{{$bu->name}}">
                    <tr>
                        <td>
                            <input value="{{$bu->id}}"  class="form-control" type="text" disabled>
                        </td>
                        <td>
                            <h5>
                                {{$bu->name}}
                            </h5>
                        </td>
                        <td>
                            <div class="form-group w-auto">
                                <input  name="articulos[{{$bu->id}}][cantidad]" id="opcion[][cantidad]" value="{{($bu->existencia)}}" type="number" class="form-control w-auto corregir" required> 
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    <tr>
                        <td><button type="submit" class="btn btn-primary" >Actualizar</button></td>
                    </tr>
                </form>
            </tbody>
        </table>
    </div>
    @endif

</div>

@endsection
@section('script')
<script>
$( document ).ready(function() {
        @if (isset($buscar))
        
        @else
            $( "#buscar" ).click();  
        @endif
    
    $( "#mostrar_errores" ).click(function() {
    var algo= $("#mostrar_errores").val();
    var tipo_error=$("#mostrar_errores").attr('error');
    if (algo == 1) {
        var html = '';
            html += '<strong>Error! '+tipo_error+'</strong><br><br>';
            $('#errores_ventas').html(html).promise().done(function(){
                $(this).slideToggle('');
                setTimeout(function(){ $('#errores_ventas').css('display','none'); }, 5000);
            });   
    }else{
        var html = '';
        html += '<strong>Inventario Actualizado </strong><br><br>';
        $('#success').html(html).promise().done(function(){
            $(this).slideToggle();
            setTimeout(function(){ $('#success').css('display','none'); }, 5000);
        });
    }
});
    console.log( "ready!" );
    var form =null;
    var contador=0;
$("#enviar").click(function(event){
    event.preventDefault();
        form =[];
        contador=0;
        $errores=null;
        if($errores != null){
            $("#mostrar_errores").attr('value',1);
            $("#mostrar_errores").attr('error',$errores);
            $("#mostrar_errores").click();
        }else{
            document.getElementById("myform").submit();   
        }
    });
    

   
});
</script>   
@endsection