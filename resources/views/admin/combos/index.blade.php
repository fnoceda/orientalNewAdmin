@extends('layouts.admin')
@section('main-content')
@if (session('status'))
<div class="alert alert-success">
    {{ session('status') }}
</div>
@endif
<div class="card shadow mb-3 col-12 ">
    {{-- <div class="card shadow mb-12">
        <div class="card-header py-3" style="padding: 5px 10px 5px 10px !important;">
            <a href="{{url('combos/create')}}" class="btn btn-primary">Crear Nuevo <i class="fas fa-plus"></i></a>
        </div>
    </div> --}}
    <div class="card-body">
        <table id="tabla_">
            <thead>
                <tr>
                    <td>#id</td>
                    <td>name</td>
                    <td>presentacion</td>
                    <td class=" ml-5">Acciones</td>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $i)
                <tr>
                    <td>{{$i->id}}</td>
                    <td>{{$i->name}}</td>
                    <td>{{$i->presentacion}}</td>
                    <td style="text-align: center">
                        <a href="{{url('combos/edit',['id'=>$i->id])}}"><i class="fas fa-edit mr-5"></i></a>
                        <button onclick="modalOpen({{$i->id}})" type="button" class="btn btn-danger" title="Eliminar"> <i class="far fa-trash-alt"></i></button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="modal" tabindex="-1" role="dialog" id="modal_e">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Eliminar</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Seguro que quiere eliminar este combo/articulo y sus detalles una vez eliminados no se podran recuperar
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="Eliminame()">Si</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                </div>
            </div>
        </div>
    </div>
<input type="hidden" id="id_eliminar">
</div>
@endsection
@section('script')
<script>
    $(document).ready(function() {
    $('#tabla_').DataTable();
    href="{{url('combos/delete',['id'=>0])}}"
});
function modalOpen(id){
    $('#id_eliminar').val(id);
    $('#modal_e').modal('show');
}
function Eliminame(){
    let url= "{{url('combos/delete')}}"+"/"+$('#id_eliminar').val();
    console.log(url);
    window.location.href = url;
}
</script>

@endsection