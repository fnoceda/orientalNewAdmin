@extends('layouts.admin')
@section('main-content')


    <div class="card shadow mb-3 col-12 p-0">
        <div class="card-body" style="padding: 10px 20px 0px 20px;">
            <div class="card-header py-3" style="padding: 5px 10px 4px 0px !important;font-size: 20px;font-weight: bold;">
                <button class=" btn btn-primary" id="abrirModalAgregarCliente">NUEVO</button>
                <a class=" btn btn-secondary mr-5"  href="{{url('/users/clientes/list')}}" >VER CLIENTES</a>
            </div>
            <div id="datos_tabla">
                @include('users.listar_datos')
            </div>
        </div>
    </div>

    <!-- Modal Guardar -->
    <div class="modal fade" id="crearVendedoresModal" tabindex="-1" role="dialog" aria-labelledby="crearVendedoresModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="crearVendedoresModalLabel">Datos del cliente</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="crearVendedoresForm">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <div class="alert alert-danger" role="alert" id="alertError" style="display: none"></div>
                            <div class="alert alert-info" role="alert" id="alertInfo" style="display: none"></div>
                        </div>
                        <div class="form-group">
                            <label for="" class="mb-1">Nombre</label>
                            <input type="text" class="form-control"  name="name" id="name" required>
                        </div>
                        <div class="form-group">
                            <label for="" class="mb-1">Email</label>
                            <input type="email" class="form-control"  name="email" id="email" required>
                        </div>
                        <div class="form-group">
                            <label for="" class="mb-1">RUC</label>
                            <input type="text" class="form-control"  name="ruc" id="ruc" required>
                        </div>
                        <div class="form-group">
                            <label for="" class="mb-1">Telefono</label>
                            <input type="text" class="form-control"  name="telefono" id="telefono" required>
                        </div>
                        <div class="form-inline mb-2">
                        <label for="" class="mb-1 col-3 float-rigth">Contraseña</label>
                            <input class="form-control col-10" type="password" name="password" id="password">
                            <button class="btn btn-primary float-right" type="button" onclick="mostrarContrasena()"><i class="fa fa-eye" aria-hidden="true"></i></button>
                         </div>
                         <div class="form-group">
                            <label for="" class="mb-1">Direccion</label>
                            <input type="text" class="form-control"  name="direccion" id="direccion" >
                        </div>
                        <div class="form-group">
                            <label for="" class="mb-1">Ciudad</label>
                            <select name="ciudad_id" id="ciudad_id" class="form-control" required>
                                <option value="" selected disabled>Seleccione una Ciudad</option>
                                @foreach($ciudades as $l)
                                    <option value="{{ $l->id }}">{{ $l->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="" class="mb-1">Direccion Delivery</label>
                            <input type="text" class="form-control"  name="direccion_delivery" id="direccion_delivery" >
                        </div>
                        <div class="form-group">
                            <label for="" class="mb-1">Latitud</label>
                            <input type="text" class="form-control"  name="latitud" id="latitud" >
                        </div>
                        <div class="form-group">
                            <label for="" class="mb-1">Longitud</label>
                            <input type="text" class="form-control"  name="longitud" id="longitud" >
                        </div>
                        <div class="form-group">
                            <label for="" class="mb-1">Perfil App</label>
                            <select name="perfil" id="perfil"  class="form-control" required>
                                <option value="" selected disabled >Seleccione un Perfil</option>
                                <option value="admin" >Administrador</option>
                                <option value="cliente" >Cliente</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="" class="mb-1">Perfil Web</label>
                            <select name="perfil_id" id="perfil_id"  class="form-control" required>
                                <option value="" selected disabled >Seleccione un Perfil web</option>
                                @foreach($perfiles as $l)
                                <option value="{{ $l->id }}">{{ $l->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="" class="mb-1">Empresa Web</label>
                            <select name="empresa_id" id="empresa_id"  class="form-control" >
                                <option value="" selected  >Seleccione una Empresa</option>
                                @foreach($empresas as $l)
                                <option value="{{ $l->id }}">{{ $l->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary" id="btn_guardar">Agregar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Modal Editar -->
    <div class="modal fade" id="editarVendedoresModal" tabindex="-1" role="dialog" aria-labelledby="editarVendedoresModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editarVendedoresModalLabel">Datos del vendedor</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="editarVendedoresForm">
                    @csrf
                    <input type="hidden" class="form-control"  name="id" id="id" required>
                    <div class="modal-body">
                        <div class="form-group">
                            <div class="alert alert-danger" role="alert" id="alertError" style="display: none"></div>
                            <div class="alert alert-info" role="alert" id="alertInfo" style="display: none"></div>
                        </div>
                        <div class="form-group">
                            <label for="" class="mb-1">Nombre</label>
                            <input type="text" class="form-control"  name="name" id="name" required>
                        </div>
                        <div class="form-group">
                            <label for="" >Email</label>
                            <input type="email" class="form-control form-control-user" name="email" id="email" required>
                        </div>
                        <div class="form-group">
                            <label for="" class="mb-1">RUC</label>
                            <input type="text" class="form-control"  name="ruc" id="ruc" required>
                        </div>
                        <div class="form-group">
                            <label for="" class="mb-1">Telefono</label>
                            <input type="text" class="form-control"  name="telefono" id="telefono" required>
                        </div>
                        <div class="form-inline mb-2">
                        <label for="" class="mb-1 col-3 float-rigth">Contraseña</label>
                            <input class="form-control col-10" type="password" name="password" id="password">
                            <button class="btn btn-primary float-right" type="button" onclick="mostrarContrasena()"><i class="fa fa-eye" aria-hidden="true"></i></button>
                         </div>
                         <div class="form-group">
                            <label for="" class="mb-1">Direccion</label>
                            <input type="text" class="form-control"  name="direccion" id="direccion" >
                        </div>
                        <div class="form-group">
                            <label for="" class="mb-1">Ciudad</label>
                            <select name="ciudad_id" id="ciudad_id" class="form-control" required>
                                <option value="" selected disabled>Seleccione una Ciudad</option>
                                @foreach($ciudades as $l)
                                    <option value="{{ $l->id }}">{{ $l->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="" class="mb-1">Direccion Delivery</label>
                            <input type="text" class="form-control"  name="direccion_delivery" id="direccion_delivery" >
                        </div>
                        <div class="form-group">
                            <label for="" class="mb-1">Latitud</label>
                            <input type="text" class="form-control"  name="latitud" id="latitud" >
                        </div>
                        <div class="form-group">
                            <label for="" class="mb-1">Longitud</label>
                            <input type="text" class="form-control"  name="longitud" id="longitud" >
                        </div>
                        <div class="form-group">
                            <label for="" class="mb-1">Perfil App</label>
                            <select name="perfil" id="perfil"  class="form-control" required>
                                <option value="" selected disabled >Seleccione un Perfil</option>
                                <option value="admin" >Administrador</option>
                                <option value="cliente" >Cliente</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="" class="mb-1">Perfil Web</label>
                            <select name="perfil_id" id="perfil_id"  class="form-control" required>
                                <option value="" selected disabled >Seleccione un Perfil web</option>
                                @foreach($perfiles as $l)
                                <option value="{{ $l->id }}">{{ $l->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="" class="mb-1">Empresa Web</label>
                            <select name="empresa_id" id="empresa_id"  class="form-control" >
                                <option value="" selected  >Seleccione una Empresa</option>
                                @foreach($empresas as $l)
                                <option value="{{ $l->id }}">{{ $l->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-success" id="btn_editar">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('script')

    <script src="{{ asset('vendor/jquery-ui-1.12.1/jquery-ui.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.5/jspdf.debug.js"></script>

    <script type="text/javascript">

        //<editor-fold desc="Funciones para formatear">
        var format = function(num){
            var str = num.toString().replace("$", ""), parts = false, output = [], i = 1, formatted = null;
            if(str.indexOf(",") > 0) {
                parts = str.split(",");
                str = parts[0];
            }
            str = str.split("").reverse();
            for(var j = 0, len = str.length; j < len; j++) {
                if(str[j] != ",") {
                    output.push(str[j]);
                    if(i%3 == 0 && j < (len - 1)) {
                        output.push(".");
                    }
                    i++;
                }
            }
            formatted = output.reverse().join("");
            return(formatted + ((parts) ? "." + parts[1].substr(0, 2) : ""));
        };

        function formatearFecha(fecha){
            return fecha.replace(/^(\d{4})-(\d{2})-(\d{2})$/g,'$3/$2/$1');
        }


        function printDiv(nombreDiv) {
            var contenido= document.getElementById(nombreDiv).innerHTML;
            var contenidoOriginal= document.body.innerHTML;
            document.body.innerHTML = contenido;
            window.print();
            document.body.innerHTML = contenidoOriginal;
            despuesDeLaImpresion();
        }

        function despuesDeLaImpresion(){
            console.log('OK despuesDeLaImpresion');
        }


//contraseñas partes
    function mostrarContrasena(){
        var tipo = document.getElementById("password");
        if(tipo.type == "password"){
            tipo.type = "text";
        }else{
            tipo.type = "password";
        }
    }
    function mostrarContrasenaEdit(){
        var tipo = document.getElementById("passwordEdit");
        if(tipo.type == "password"){
            tipo.type = "text";
        }else{
            tipo.type = "password";
        }
    }

$(document).ready(function (){
    $('#abrirModalAgregarCliente').on('click', function (){
        $('#crearVendedoresModal').modal('show');
        $('#crearVendedoresModal #id').val('');
        $('#crearVendedoresModal #name').val('');
        $('#crearVendedoresModal #email').val('');
        $('#crearVendedoresModal #ruc').val('');
        $('#crearVendedoresModal #telefono').val('');
        $('#crearVendedoresModal #password').val('');
        $('#crearVendedoresModal #direccion').val('');
        $('#crearVendedoresModal #ciudad_id').val('1');
        $('#crearVendedoresModal #direccion_delivery').val('');
        $('#crearVendedoresModal #latitud').val('');
        $('#crearVendedoresModal #longitud').val('');
        $('#crearVendedoresModal #perfil').val('');
        $('#crearVendedoresModal #perfil_id').val('');
        $('#crearVendedoresModal #empresa_id').val('');
        setTimeout(function (){ $('#crearVendedoresModal #name').focus(); }, 500);
    });
            $('#crearVendedoresForm').on('submit', function (e){
                $('#crearVendedoresModal #alertError').hide();
                $('#crearVendedoresModal #alertInfo').hide();

                e.preventDefault();
                var formData = new FormData($('#crearVendedoresForm')[0]);
                $.ajax({
                    url: "{{ url('/usuarios/agregar') }}",
                    type: 'POST',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    cache: false,
                    success:function(data){
                        console.log(data);
                        if (data.code == 200){
                            $('#crearVendedoresModal #alertInfo').html(data.message);
                            $('#crearVendedoresModal #alertInfo').show();
                            $('#crearVendedoresModal').modal('hide');
                            $('#crearVendedoresForm').trigger('reset');
                            location.reload();
                        }else{ 
                            $('#crearVendedoresModal #alertError').html(data.message);
                            $('#crearVendedoresModal #alertError').show();
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

            $('#editarVendedoresForm').on('submit', function (e){ //kaka
                e.preventDefault();
                $('#editarVendedoresModal #alertError').html(''); $('#editarVendedoresModal #alertError').hide();
                $('#editarVendedoresModal #alertInfo').html(''); $('#editarVendedoresModal #alertInfo').hide();
                var formData = new FormData($('#editarVendedoresForm')[0]);
                console.log(formData);
                $.ajax({
                    url: "{{ url('/usuarios/editar') }}",
                    type: 'POST',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    cache: false,
                    success:function(data){
                        console.log(data);
                        if (data.code == 200){
                            // cerrar modal
                            $('#editarVendedoresModal #alertInfo').html(data.message);
                            $('#editarVendedoresModal #alertInfo').show();
                            $('#editarVendedoresModal').modal('hide');
                            $('#editarVendedoresForm').trigger('reset');
                            location.reload();
                        }else{
                            $('#editarVendedoresModal #alertError').html(data.message);
                            $('#editarVendedoresModal #alertError').show();
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


     table = $('#clientesTable').DataTable();

});

function abrirModalEditarCliente( id ,name ,email ,ruc,telefono,direccion,ciudad_id,direccion_delibery,latitud,longitud,perfil,perfil_id,empresa_id){
    $('#editarVendedoresModal').modal('show');
            $('#editarVendedoresModal #id').val(id);
            $('#editarVendedoresModal #name').val(name);
            $('#editarVendedoresModal #email').val(email);
            $('#editarVendedoresModal #ruc').val(ruc);
            $('#editarVendedoresModal #telefono').val(telefono);
            $('#editarVendedoresModal #password').val('');
            $('#editarVendedoresModal #direccion').val(direccion);
            $('#editarVendedoresModal #ciudad_id').val(ciudad_id);
            $('#editarVendedoresModal #direccion_delivery').val(direccion_delibery);
            $('#editarVendedoresModal #latitud').val(latitud);
            $('#editarVendedoresModal #longitud').val(longitud);
            $('#editarVendedoresModal #perfil').val(perfil);
            $('#editarVendedoresModal #perfil_id').val(perfil_id);
            $('#editarVendedoresModal #empresa_id').val(empresa_id);

            setTimeout( function (){ $('#editarVendedoresForm #name').focus(); }, 1000);
}

      
        $(document).on('keydown', 'body', function(event) {
            var array= [107];//F1,F2,F3,F4
            if(array.includes(event.keyCode)) {
                // Limpiar campos
                $('#id').val('');
                $('#name').val('');
                $('#entidad_id').val('');

                $('#crearVendedoresModal').modal('show');
                setTimeout(function (){
                    $('#name').focus();
                }, 500);
            }
        });

    </script>

@endsection
