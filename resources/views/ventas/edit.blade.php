@extends('layouts.admin')
@section('main-content')
@if (session('status'))
<div class="alert alert-success">
    {{ session('status') }}
</div>
@endif

<div class="modal fade" id="modalImage" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Imagenes Del Articulo</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <div id="image" class="form-inline">

            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
<div class="contaier form-inline">


    <table>
        <thead>
            <tr>
                <td>
                    <div id='errores_ventas' style='display:none' class='alert alert-danger' ></div>
                    <div id='success' style='display:none;' style="background: #4e73df  !important;" class='alert alert-primary' ></div>
                    <input  class="btn button " type="hidden" id="mostrar_errores" value="1" disabled error="">
                </td>

            </tr>
        </thead>
        <tr>
            <td valign="top">
                <div class="card mb-3 mr-3" style="width: 25rem;">
                    <div class="card-header">Datos del Cliente</div>

                    <div class="card-body">
                        <div class="table-responsive-sm">
                            <table class="table table-hover table-borderless table-sm">
                                <tbody>
                                    <tr>
                                        <th>Ruc</th>
                                        <td>{{ $cliente->ruc }}</td>
                                    </tr>
                                    <tr>
                                        <th>Nombre</th>
                                        <td>{{ $cliente->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Telefono</th>
                                        <td>{{ $cliente->telefono }}</td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td>{{ $cliente->email }}</td>
                                    </tr>
                                    <tr>
                                        <th>Direccion</th>
                                        <td>{{ $cliente->ciudad.' '.$cliente->direccion }}


                                        </td>
                                    </tr>

                                    <tr>
                                        <th>Mapa</th>
                                        <td>
                                            <a target="_blank" href=" http://maps.google.com/maps?z=20&q={{ $cliente->latitud }}+{{ $cliente->longitud }}">
                                                <i class="fas fa-map-marked-alt"></i>
                                            </a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                          </div>

                          <div class="card-footer text-muted">
                            <div style="float: left">
                                Se unio {{ $cliente->created_at }} <br />
                                Compras <strong class="text-primary"> {{ $veces }} </strong>
                                Promedio <strong class="text-primary"> {{ number_format($promedio, '0', ',', '.') }} </strong>

                            </div>
                            <div style="float: right"><a href="/historial/{{ $cliente->id }}" class="btn btn-outline-primary"> Historial </a></div>

                          </div>
                    </div>
                </div>


            </td>
            <td valign="top">


            <div class="card mb-3 " style="width: 25rem;">
                <div class="card-header">Datos de la Venta</div>
            <input type="hidden" value="{{$venta->id}}" id="venta_id">
                <div class="card-body">
                    <div class="table-responsive-sm">
                        <table class="table table-hover table-borderless table-sm">
                            <tbody>
                                <tr>
                                    <th>Modo</th>
                                    <td>{{ ucwords($venta->modo) }}</td>
                                </tr>
                                <tr>
                                    <th>Estado</th>
                                    <td>
                                        {{--  'pendiente', 'despachando', 'enviado', 'entregado', 'cancelado'  --}}
                                        <select class="custom-select my-1 mr-sm-2" id="inlineFormCustomSelectPref">

                                            <option value="pendiente" @if($venta->estado == 'pendiente') {{ 'selected'  }} @endif >Pendiente</option>
                                            <option value="despachando" @if($venta->estado == 'despachando') {{ 'selected'  }} @endif>Despachando</option>
                                            <option value="enviado" @if($venta->estado == 'enviado') {{ 'selected'  }} @endif>Enviado</option>
                                            <option value="entregado" @if($venta->estado == 'entregado') {{ 'selected'  }} @endif>Entregado</option>
                                            <option value="cancelado" @if($venta->estado == 'cancelado') {{ 'selected'  }} @endif>Cancelado</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Forma de Pago</th>
                                    <td>{{ ucwords($venta->forma_pago) }}</td>
                                </tr>
                                <tr>
                                    <th>Productos</th>
                                    <td>
                                        <strong class="text-danger">{{ number_format(($venta->importe), 0, ',', '.') }}</strong>
                                    </td>
                                </tr> <tr>
                                    <th>Delivery ({{ number_format((float)$venta->delivery_kilometros, 2, '.', '') }} kilometros) </th>
                                    <td>
                                        <strong class="text-danger">{{ number_format(($venta->delivery_importe), 0, ',', '.') }}</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Total a Cobrar</th>
                                    <td>
                                        <strong class="text-danger">{{ number_format(($venta->importe + $venta->delivery_importe), 0, ',', '.') }}</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Direccion</th>
                                    <td>
                                        {{ $venta->ciudad.' '.$venta->direccion }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>Mapa</th>
                                    <td>
                                        <a target="_blank" href=" http://maps.google.com/maps?z=20&q={{ $venta->latitud }}+{{ $venta->longitud }}">
                                            <i class="fas fa-map-marked-alt"></i>
                                        </a>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer text-muted">
                        Fecha y Hora de venta: {{ $venta->created_at }} <br />
                        Fecha y Hora Programada: <strong class="text-danger"> {{ $venta->entrega_programada }} </strong>
                      </div>
            </div>
            </td>
        </tr>

        <tr>
            <td colspan="2">

                <div class="card mb-3" style="width: 51rem;">
                    <div class="card-header">Detalles de la Venta</div>

                    <div class="card-body">

                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Qty</th>
                                    <th>Articulo</th>
                                    <th>Imagen</th>
                                    <th>Color</th>
                                    <th>Medida</th>
                                    <th>Sabor</th>
                                    <th>Precio Unitario</th>
                                    <th>Precio Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $precioTotal = 0; $costoTotal = 0;
                                @endphp
                                @foreach ($detalles as $det)
                                <tr>
                                    <td> {{ number_format($det->cantidad, 0, ',', '.') }} </td>
                                    <td> {{ ucwords($det->name) }} </td>
                                    <td align="center">
                                        <div style="height: 50px; width: 50px " class="border" >
                                            @if (isset($det->articulo->articuloimagen[0]))
                                            <img class="img-fluid mostar_img" onclick="add_image({{$det->deta_id}})"   src="{{asset('storage/articulos/')}}/{{$det->articulo->articuloimagen[0]->path}}" >
                                            @else
                                            <img class="img-fluid" src="{{ asset('img/no-imagen.jpg') }}" >
                                            @endif
                                        </div>

                                    </td>
                                    <td>
                                        @if (!empty($det->color))
                                        <div style="height: 30px; width: 30px; background-color: {{$det->color}}" class="border"></div>   
                                        @else
                                        <div style="height: 30px; width: 30px; background-color: {{$det->color}}" ></div>   
                                            
                                        @endif
                                    </td>
                                    <td>
                                        <div class="ml-3"><h5>{{$det->medida}} </h5></div>
                                        
                                    </td>
                                    <td align="center">
                                        <div class="ml-3"><h5>{{$det->sabor}} </h5></div>
                                        
                                    </td>
                                    <td align="right"> {{ number_format($det->precio, 0, ',', '.') }} </td>
                                    <td align="right"> {{ number_format($det->precio_total, 0, ',', '.') }} </td>
                                </tr>

                                    @php
                                        $precioTotal = $precioTotal + $det->precio_total; $costoTotal = $costoTotal + $det->costo_total;
                                    @endphp
                                @endforeach
                                <tr>
                                    <th colspan="7">Totales</th>
                                    <td align="right"> <strong class="text-primary">{{ number_format($precioTotal, 0, ',', '.') }}</strong> </td>
                                </tr>
                            </tbody>
                        </table>

                            {{--  <a href="#" class="btn btn-primary">Go somewhere</a>  --}}
                        </div>
                </div>

            </td>
        </tr>

        <tr>
            <td colspan="2">

                <div class="card mb-3" style="width: 51rem;">
                    <div class="card-header">Detalles de Utilidad</div>

                    <div class="card-body">

                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Qty</th>
                                    <th>Articulo</th>
                                    <th>Costo Unitario</th>
                                    <th>Costo Total</th>

                                    <th>Precio Unitario</th>
                                    <th>Total a Cobrar</th>
                                    <th>Utilidad</th>

                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $precioTotal = 0; $costoTotal = 0;
                                @endphp
                                @foreach ($detalles as $det)
                                <tr>
                                    <td> {{ number_format($det->cantidad, 0, ',', '.') }} </td>
                                    <td> {{ ucwords($det->name) }} </td>
                                    <td align="right"> {{ number_format($det->costo, 0, ',', '.') }} </td>
                                    <td align="right"> {{ number_format($det->costo_total, 0, ',', '.') }} </td>
                                    <td align="right"> {{ number_format($det->precio, 0, ',', '.') }} </td>
                                    <td align="right"> {{ number_format($det->precio_total, 0, ',', '.') }} </td>
                                    <td align="right"> {{ number_format($det->utilidad, 0, ',', '.') }} </td>
                                </tr>

                                    @php
                                        $precioTotal = $precioTotal + $det->precio_total; $costoTotal = $costoTotal + $det->costo_total;
                                    @endphp
                                @endforeach
                                <tr>
                                    <th colspan="3">Totales</th>
                                    <td align="right"> <strong>{{ number_format($costoTotal, 0, ',', '.') }}</strong> </td>
                                    <th>&nbsp;</th>
                                    <td align="right"> <strong>{{ number_format($precioTotal, 0, ',', '.') }}</strong> </td>
                                    <td align="right"> <strong class="text-primary">{{ number_format(($precioTotal - $costoTotal), 0, ',', '.') }}</strong> </td>
                                </tr>
                            </tbody>
                        </table>
                        </div>
                </div>

            </td>
        </tr>
    </table>




</div>

@endsection
@section('script')
<script>
$( document ).ready(function() {
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
$("#inlineFormCustomSelectPref").change(function(e) {
    e.preventDefault();//no borrar este hace la magia
    e.stopImmediatePropagation();//este tampoco
    $.ajax({
        type:"GET",
        url: "{{url('ventas/cambiar/estado')}}",
        type: 'GET',
        data: { estado: $("#inlineFormCustomSelectPref").val(),
                            venta: $("#venta_id").val()},

        success: function(rta){
            console.log(rta);
          if (rta.cod_retorno > 200) {
                $("#mostrar_errores").attr('value',1);
                $("#mostrar_errores").attr('error',rta.des_retorno);
                $("#mostrar_errores").click();
              }else{
                $("#mostrar_errores").attr('value',0);
                $("#mostrar_errores").attr('error',rta.des_retorno);
                $("#mostrar_errores").click();

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

  });
});

    @php
        echo ' var detalles_js = '.($detalles).'; ';

    @endphp

    const ItemImages = ({path}) => `
    <div  style="height: 200px; width: 200px ">
        <img class="img-fluid"  src="{{asset('storage/articulos/')}}/${path}" >
    </div>
`;

function add_image(id){
    $('#modalImage #image').empty().promise().done(function(){
        var detalle = detalles_js.find( item => item.deta_id == id );
    if (detalle.articulo.articuloimagen.length > 0) {
        var img=detalle.articulo.articuloimagen
        img.map(function (x){
            $('#modalImage #image').append([x].map(ItemImages).join(''));
        })
        //
        $('#modalImage').modal('show');
    }
    })
}

</script>
@endsection
