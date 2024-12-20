@extends('layouts.admin')
@section('main-content')
@if (session('status'))
<div class="alert alert-success">
    {{ session('status') }}
</div>
@endif
<div class="contaier align-top">
    <div class="card" style="width: 50rem;">
        <div class="card-header">
            Nuevo Banner
        </div>

        <form action="{{url('/banners/images/guardar/')}}" enctype="multipart/form-data" method="POST">
            <div class="card-body">
                @csrf
                <div class="col col-12 mt-3">
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <div class="input-group-text">name</div>
                        </div>
                        <input type="text" class="form-control" id="inlineFormInputGroup" name="name">
                    </div>
                </div>
                <div class="col col-12 mt-3">
                    <div class="input-group mb-2">
                        <label class="custom-file-label" for="path">Archivo</label>
                        <input type="file" class="custom-file-input" id="path" name="path">
                    </div>
                </div>
                <div class="form-group col-md-4">
                    <label for="inputState">Origen:</label>
                    <select id="inputState" class="form-control col-12" name="destino">
                        <option value="header" selected="true">Header</option>
                        <option value="footer">Footer</option>
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label for="seccion">Seccion:</label>
                    <select id="seccion" class="form-control col-12" name="seccion_id">
                        <option value="">Seleccione el destino</option>
                        @foreach ($secciones as $d)
                        <option value="{{$d->id}}">{{$d->name}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col col-12 mt-3">
                    <label class="sr-only" for="inlineFormInputGroup">Parametro</label>
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <div class="input-group-text">Elemento</div>
                        </div>
                        <input type="hidden" id="parametro" name="parametro">
                        <input readonly type="text" class="form-control" name="label_parametro" id="label_parametro" />
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">
                            Elegir
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-footer text-right">
                <button type="submit" class="btn btn-success ml-2 mb-3">Guardar</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Modal title</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                ...
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')

<script>
    function cambiarParametro(id, name) {
        console.log(id);
        console.log(name);
        $('#parametro').val(id);
        $('#label_parametro').val(name);
    }


    $(document).ready(function () {


        @php
        echo ' var categorias = '.json_encode($categorias).
        '; ';
        echo ' var articulos = '.json_encode($articulos).
        '; ';
        echo ' var promos = '.json_encode($promos).
        '; ';
        @endphp

        $('#myModal').on('show.bs.modal', function (event) {
            var lista = []; // Extract info from data-* attributes
            var modal = $(this);

            if ($('#seccion').val() == 1) { //listamos las categorias
                categorias.forEach(function callback(ele, index, array) {
                    lista.push(ele);
                });
            }

            if ($('#seccion').val() == 2) { //listamos los productos
                articulos.forEach(function callback(ele, index, array) {
                    lista.push(ele);
                });
            }

            if ($('#seccion').val() == 3) { //listamos las promos
                promos.forEach(function callback(ele, index, array) {
                    lista.push(ele);
                });
            }

            var html = jsonToList(lista);

            modal.find('.modal-title').text('Elija la ' + $('select[name="seccion_id"] option:selected')
                .text());
            modal.find('.modal-body').html(html);

        });



        function jsonToList(lista) {

            /* <div class="list-group">
                   <div class="list-group" id="list-tab" role="tablist">
                     <a class="list-group-item list-group-item-action active" id="list-home-list" data-toggle="list" href="#list-home" role="tab" aria-controls="home">Home</a>
                     <a class="list-group-item list-group-item-action" id="list-profile-list" data-toggle="list" href="#list-profile" role="tab" aria-controls="profile">Profile</a>
                     <a class="list-group-item list-group-item-action" id="list-messages-list" data-toggle="list" href="#list-messages" role="tab" aria-controls="messages">Messages</a>
                     <a class="list-group-item list-group-item-action" id="list-settings-list" data-toggle="list" href="#list-settings" role="tab" aria-controls="settings">Settings</a>
                   </div>
               </div>  */
            var html = '<div class="list-group">';
            var activo = '';
            lista.forEach(function callback(ele, index, array) {
                activo = '';
                if (ele.id.toString() == $('#parametro').val().toString()) {
                    activo = 'active';
                }
                html += '<a onClick="javascript: cambiarParametro(' + ele.id + ', \'' + ele.name +
                    '\')" class="list-group-item list-group-item-action ' + activo +
                    '" id="list-profile-list" data-toggle="list" href="#list-profile" role="tab" aria-controls="profile">' +
                    ele.name + '</a>';
            });
            html += '</div>';

            console.log(html);

            return html;

        }



    });

</script>

@endsection
