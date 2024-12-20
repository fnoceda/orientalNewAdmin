@extends('layouts.admin')
@section('main-content')
@if (session('status'))
    <div class="alert alert-success">
        {{ session('status') }}
    </div>
@endif
<!-- Tabla -->
<div>
    <div class="card">
        <div class="card-body">
            <table class="table table-striped" id="tablaCrud" style="width:100%">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Plazo</th>
                        <th width='120px'>Acciones</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<!-- Tabla -->
<!-- Modal -->
<div class="modal fade" id="formCrudModal"  role="dialog" aria-labelledby="formCrudModalTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form id="formCrud">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="formCrudModalTitle">Nuevo Plazo</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="col-xs-12">
                        <div id="alert_message_type" class="collapse">
                        </div>
                    </div>
                    @csrf
                    <input type="hidden" id="id" name="id">
                    <div class="form-group">
                        <label for="name" class="col-form-label">Plazo: <small>(<span
                                    class="text-danger">*</span>)</small></label>
                        <input type="text" class="form-control" id="name" name="name" autofocus required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="sumbitButton" class="btn btn-primary" type="submit">
                        <span id="submitSpinner" class="spinner-border-sm" role="status" aria-hidden="true"></span>
                        Guardar
                    </button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>

                </div>
            </div>
        </form>
    </div>
</div>
<!-- Modal -->
<div class="modal" tabindex="-1" role="dialog" id="modal_delete">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Eliminar</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <Form id="form-delete">
                        <input type="text" class="form-control" disabled id="text_delete" name="text_delete">
                </Form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary delete" >Si</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
            </div>
        </div>
    </div>
  </div>
@endsection
@section('script')
<script src=" {{ asset('js/comunes.js') }} "></script>
<script>
    $( document ).ready(function() {
        var data = {
            table: "tablaCrud",
            ajax : "{{ route('plazo.entrega.datatable') }}",
            topMsg: "",
            footerMsg: "Generado: {{ auth()->user()->name }} {{ date("d/m/Y H:i") }}",
            filename: "Listado de Sucursales",
            title: 'Listado de Sucursales',
            columns: [
                {data: 'id', name: 'id'},
                {data: 'name', name: 'Plazo'},
                {data: 'acciones', name: 'acciones', orderable: false, searchable: false, class: 'noexport'}
            ]
        };
        toDataTable(data);
    });

    $('[data-toggle="tooltip"]').tooltip();

    $('#formCrud').on('submit', function(e){
        e.preventDefault();
        if( $('#id').val()  ){
            console.log('update'); console.log( $('#id').val() );
            var ruta = "{{ route('plazo.entrega.update') }}";
            var formData = new FormData($('#formCrud')[0]);
            update(formData, ruta);
        }else{
            console.log('store');
            var formData = new FormData($('#formCrud')[0]);
            var ruta = "{{ route('plazo.entrega.store') }}";
            store(formData, ruta);
        }
        console.log(ruta);
    });


</script>
@endsection
