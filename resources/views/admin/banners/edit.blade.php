@extends('layouts.admin')
@section('main-content')
@if (session('status'))
<div class="alert alert-success">
    {{ session('status') }}
</div>
@endif
<div class="jumbotron contaier form-inline align-items-start">
    <div class="card" style="width: 50rem;">
        <div class="card-header">
            Editar Banner {{ $data->name }}
        </div>
        <form action="{{url('/banners/imagenes/update/')}}" enctype="multipart/form-data" method="POST">
            <div class="card-body">
                @csrf

                <input type="hidden" value="{{$id}}" id="id" name="id">

                <div class="col col-12 mt-3">
                    <label class="sr-only" for="inlineFormInputGroup">Username</label>
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <div class="input-group-text">name</div>
                        </div>
                        <input value="{{$data->name}}" type="text" class="form-control" id="inlineFormInputGroup"
                            name="name">
                    </div>
                </div>

                <div class="col col-12 mt-3">
                    <div class="input-group mb-2">
                        <label class="custom-file-label" for="path">{{ $data->path }}</label>
                        <input type="file" class="custom-file-input" id="path" name="path">
                    </div>
                </div>
                <div class="form-group col-md-3">
                    <label for="combo_articulo">Activo</label><br />
                    <label class="switch">
                      <input type="checkbox" type="checkbox"  id="es_activo" name="es_activo"  @if ($data->es_activo == true)
                      checked="true"
                      @endif >
                      <span class="slider round"></span>
                    </label>
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
                        @if ($d->id== $data->seccion_id )
                        <option value="{{$d->id}}" selected>{{$d->name}}</option>
                        @else
                        <option value="{{$d->id}}">{{$d->name}}</option>
                        @endif
                       
                        @endforeach
                    </select>
                </div>
                <div class="col col-12 mt-3">
                    <label class="sr-only" for="inlineFormInputGroup">Parametro</label>
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <div class="input-group-text">Elemento</div>
                        </div>
                        <input type="hidden" id="parametro" name="parametro" value="{{ $data->parametro }}">
                        <input readonly type="text" class="form-control" name="label_parametro" id="label_parametro" />
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">
                            Cambiar
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-footer text-right">
                <button type="submit" class="btn btn-primary">Actualizar</button>
            </div>
        </form>
    </div>

    <div class="card ml-3 p-3">
        <div class="card-header">Imagen</div>
        <div class="card-body">
            <img id="img_muestra" src="{{ asset('/storage/banners/'.$data->path) }}" alt="user-image" width="300px">
        </div>
        <div class="card-footer">
            <small class="text-muted">Para Cambiar Seleccione Archivo</small>
        </div>
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
       function cambiarParametro(id) {
         console.log(id);
        $('#parametro').val(id);
        // $('#label_parametro').val(name);
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

        $('#label_parametro').val('{{ $data->seccion }}');

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
            $('#exampleFormControlSelect1').select2();


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
               var html = '<select class="form-control"  id="exampleFormControlSelect1" style="width: 75%" onchange="cambiarParametro(this.value);"><option></option>';
            var activo = '';
            lista.forEach(function callback(ele, index, array) {
                activo = '';
                if (ele.id.toString() == $('#parametro').val().toString()) {
                    activo = 'active';
                }
                html += '<option value="'+ele.id+'" name="'+ele.name+'">'+ele.name+'</option>';
            });
            html += '</select>';

            // console.log(html);

            return html;

        }



    });

</script>

@endsection
