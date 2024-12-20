@extends('layouts.admin')
@section('main-content')
@if (session('status'))
    <div class="alert alert-success">
        {{ session('status') }}
    </div>
@endif
<div class="card">
    <div class="card-body">
        {{-- dividimos productos y combos --}}
        <ul class="nav nav-tabs mb-3" id="pills-tab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="pills-datos-tab" data-toggle="pill" href="#pills-datos" role="tab"
                    aria-controls="pills-datos" aria-selected="true">
                    <h3>Nuevo Tag</h3>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="pills-productos-tab" data-toggle="pill" href="#pills-productos"
                    role="tab" aria-controls="pills-productos" aria-selected="false">
                    <h3>Articulos & Tags</h3>
                </a>
            </li>
        </ul>
        {{-- contruimos la tabla --}}
        <div class="tab-content" id="pills-tabContent">
            <div class="tab-pane fade show active" id="pills-datos" role="tabpanel" aria-labelledby="pills-datos-tab">
                @include('admin.tags.tags')
            </div>
            <div class="tab-pane fade " id="pills-productos" role="tabpanel"
                aria-labelledby="pills-productos-tab">
                @include('admin.tags.tags_articulos')
            </div>
        </div>
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
    var token = '{{ csrf_token() }}';

    $( document ).ready(function() {
        var data = {
            table: "tablaCrud",
            ajax : "{{ route('tags.datatable') }}",
            topMsg: "",
            footerMsg: "Generado: {{ auth()->user()->name }} {{ date("d/m/Y H:i") }}",
            filename: "Listado de Sucursales",
            title: 'Listado de Sucursales',
            columns: [
                {data: 'id', name: 'id'},
                {data: 'name', name: 'Tags'},
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
            var ruta = "{{ route('tags.update') }}";
            var formData = new FormData($('#formCrud')[0]);
            update(formData, ruta);
        }else{
            console.log('store');
            var formData = new FormData($('#formCrud')[0]);
            var ruta = "{{ route('tags.store') }}";
            store(formData, ruta);
        }
        console.log(ruta);
    });

    //tags y articulos
        var data = {
            table: "tabla_asociada",
            ajax : "{{ route('tags.articulo.datatable') }}",
            topMsg: "",
            footerMsg: "Generado: {{ auth()->user()->name }} {{ date("d/m/Y H:i") }}",
            filename: "Listado de Sucursales",
            title: 'Listado de Sucursales',
            columns: [
                {data: 'id', name: 'id'},
                {data: 'tag_name', name: 'Tags'},
                {data: 'art_name', name: 'Articulo Asociado'},                
                {data: 'acciones', name: 'acciones', orderable: false, searchable: false, class: 'noexport'}
            ]
        };
        toDataTable(data,false);

        $('.js-data-tags-ajax').select2({
                minimumInputLength: 2,
                minimumResultsForSearch: 10,
                ajax: {
                    url: '{{ route('tags.articulo.info') }}',
                    dataType: "json",
                    type: "GET",
                    data: function(params) {
                        var queryParameters = {
                            term: params.term,
                            type:  'tag'
                        }
                        return queryParameters;
                    },
                    processResults: function(data) {
                        return {
                            results: $.map(data, function(item) {
                                return {
                                    text: item.text,
                                    id: item.id
                                }
                            })
                        };
                    }
                }
            });
            $('.js-data-articulo-ajax').select2({
                minimumInputLength: 2,
                minimumResultsForSearch: 10,
                ajax: {
                    url: '{{ route('tags.articulo.info') }}',
                    dataType: "json",
                    type: "GET",
                    data: function(params) {
                        var queryParameters = {
                            term: params.term,
                            type:  'articulo'
                        }
                        return queryParameters;
                    },
                    processResults: function(data) {
                        return {
                            results: $.map(data, function(item) {
                                return {
                                    text: item.text,
                                    id: item.id
                                }
                            })
                        };
                    }
                }
            });


    $(document).on("change", "#tag_id", function(){
        $('#tag_id').val($(this).val());
        var data = $(this).select2('data')
        if(data[0]) { $('#tag_principal').val(data[0].text);}
    })
    $(document).on("change", "#articulo_id", function(){
        var data = $(this).select2('data')
        if((data) && (data[0]) && (data[0].text)) { 
            if ( $('#tag_id').val()  ) {
                var formData = new FormData();
                formData.append('_token', token);
                formData.append('tag_id', $('#tag_id').val());
                formData.append('articulo_id', $('#articulo_id').val());
                postData('{{ route('tags.articulo.store.update') }}', formData).then(function(rta){
                    alertsAndMessages('Asociado ',rta.msg,'alert-success')
                    filtrar()
                    $('.js-data-articulo-ajax').val(null).trigger('change');

                }).catch(function(error){
                    console.log('postData dio error'); console.log(error);
                    alertsAndMessages('Error',error.msg,'alert-danger')
                });
            }else{
                alert("Debe seleccionar un tag")
                $('.js-data-articulo-ajax').val(null).trigger('change');
            }
        }
        
        
    })
    var pagina_actual=1;

    function quitar(id){
        var formData = new FormData();
        formData.append('_token', token);
        formData.append('id', id);
        postData('{{ route('tags.articulo.remove') }}', formData).then(function(rta){
            alertsAndMessages('',rta.msg,'alert-success')
            filtrar()
        }).catch(function(error){
            console.log('postData dio error'); console.log(error);
            alertsAndMessages('Error',error.msg,'alert-danger')
        });
    }
    var myCallback= function paginaActual(){
    console.log("callback")
    $('#crud-table').DataTable().page(pagina_actual).draw('page');
    }
    function filtrar(){
        var _newUrl = "{{ route('tags.articulo.datatable') }}";
        var table = $('#tabla_asociada').DataTable();
        pagina_actual = table.page();
        table.ajax.url(_newUrl).load();
        if(pagina_actual != 1 ){
          $('#crud-table').DataTable().ajax.reload(myCallback);
        }else{
          $('#crud-table').DataTable().ajax.reload();
        }
}
</script>
@endsection
