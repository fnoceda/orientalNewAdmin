@extends('layouts.admin')
@section('main-content')
@if (session('status'))
    <div class="alert alert-success">
        {{ session('status') }}
    </div>
@endif
<div class="card shadow mb-3 col-12 ">
    <div class="card shadow mb-12">
        <div class="card-header py-3" style="padding: 5px 10px 5px 10px !important;" >
        <a href="{{url('/banners/images/create')}}" class="btn btn-primary">Crear Nuevo <i class="fas fa-plus"></i></a>
        </div>
    </div>
    <div class="card-body">
        <table id="tabla_">
            <thead>
                <tr>
                    <td>#id</td>
                    <td>name</td>
                    <td>images</td>
                    <td>Origen</td>
                    <td>Destino</td>
                    <td>Activo</td>
                    <td>Elemento</td>
                    <td class=" ml-5">Acciones</td>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $i)
                    <tr>
                        <td>{{$i->id}}</td>
                        <td>{{$i->name}}</td>
                        <td><img id="img_muestra" src="{{ asset('/storage/banners/'.$i->path) }}"  alt="user-image" width="50px" height="50px"></td>
                        <td>{{$i->destino}}</td>
                        <td>{{$i->seccion}}</td>
                        <td>
                            @if ($i->es_activo == true)
                                SI
                            @else
                                NO
                            @endif
                            </td>
                        <td>{{$i->elemento}}</td>

                        <td style="text-align: center"><a  href="{{url('/banners/images/edit',['id'=>$i->id])}}"><i class="fas fa-edit mr-5"></i></a>
                        <a href="{{url('/banners/images/delete',['id'=>$i->id])}}"><i class="far fa-trash-alt"></i></a></td>
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
