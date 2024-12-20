@extends('layouts.admin')
@section('main-content')
@if (session('status'))
    <div class="alert alert-success">
        {{ session('status') }}
    </div>
@endif
<div class="modal fade" id="eliminame" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="myModalLabel"></h4>
        </div>
        <div class="modal-body">
          <div id='myModalMsg'><h4 id="delete_yes_not"></h4></div>
          <div id="myModalSendForm" style="display:none"></div>
        </div>
        <div class="modal-footer">
  
            <p class="m float-right">Podria afectar a otras categorias</p>
          <button type="button" class="btn btn-primary" data-dismiss="modal">NO</button>
          <a href="#"  class="btn btn-danger" id="eliminame_a">Si</a>
        </div>
      </div>
    </div>
  </div>

<div class="card shadow mb-3 col-12 ">
    <div class="card shadow mb-12">
        <div class="card-header py-3" style="padding: 5px 10px 5px 10px !important;" >
        <a href="{{url('/admin/images/create',['tabla' => $tabla])}}" class="btn btn-primary">Crear Nuevo <i class="fas fa-plus"></i></a>
        </div>
    </div>
    <div class="card-body">
        <table id="tabla_">
            <thead>
                <tr>
                    <td>#id</td>
                    <td>name</td>
                    @if ($tabla=="etiquetas")
                    <td>Porcetaje Descuento</td>
                    @endif
                    <td>images</td>
                    <td class=" ml-5">Acciones</td>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $i)
                    <tr>
                        <td>{{$i->id}}</td>
                        <td>{{$i->name}}</td>
                        @if ($tabla=="iconos")
                        <td><img id="img_muestra" src="{{ asset('/storage/img/'.$i->path) }}" class="rounded-circle" alt="user-image" width="50px" height="50px"></td>
                        @endif
                        @if ($tabla=="etiquetas")
                        <td>{{$i->porcentaje_descuento}}</td>
                        <td><img id="img_muestra" src="{{ asset('/storage/img/'.$i->path) }}" class="rounded-circle" alt="user-image" width="50px" height="50px"></td>
                        @endif
                        <td style="text-align: center"><a  href="{{url('/admin/images/edit',['tabla' => $tabla,'id'=>$i->id])}}"><i class="fas fa-edit mr-5"></i></a>
                        <a href="{{url('/admin/images/delete',['tabla' => $tabla,'id'=>$i->id])}}"><i class="far fa-trash-alt"></i></a></td>
                        </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>  
@endsection
@section('script')
<script>
$(document).ready(function() {
    $('#tabla_').DataTable();
});
</script>
    
@endsection