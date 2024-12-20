@extends('layouts.admin')
@section('main-content')


    <div class="card shadow mb-3 col-12 p-0">
        <div class="card-body" style="padding: 10px 20px 0px 20px;">
            <div class="card-header py-3" style="padding: 5px 10px 4px 0px !important;font-size: 20px;font-weight: bold;">
              Clientes
            </div>
            <div id="datos_tabla">
                @include('users.listar_datos')
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

function abrirModalEditarCliente(){
    console.log("no")
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
    table = $('#clientesTable').DataTable();

});
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
