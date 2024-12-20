@extends('layouts.admin')
@section('main-content')
@if (session('status'))
    <div class="alert alert-success">
        {{ session('status') }}
    </div>
@endif
<div class="card shadow mb-3 col-12 ">
    {{-- <div class="card shadow mb-12">
        <div class="card-header py-3" style="padding: 5px 10px 5px 10px !important;" >
        <a href="{{url('/banners/images/create')}}" class="btn btn-primary">Crear Nuevo <i class="fas fa-plus"></i></a>
        </div>
    </div> --}}
    <div class="card-body">
        <table id="tabla_">
            <thead>
                <tr>
                    <td>#id</td>
                    <td>Modo</td>
                    <td>Estado</td>
                    <td>Fecha</td>

                    <td>Cliente</td>
                    <td>Ciudad</td>
                    <td>Barrio</td>

                    <td>Direccion</td>
                    <td>Importe</td>
                    <td class=" ml-5">Acciones</td>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $i)
                    <tr>
                        <td>{{$i->id}}</td>
                        <td>{{ ucwords($i->modo) }}</td>
                        <td>{{ ucwords($i->estado) }}</td>
                        <td>{{$i->fecha}}</td>
                        <td>{{$i->cliente}}</td>
                        <td>{{$i->ciudad}}</td>
                        <td>{{$i->barrio}}</td>

                        <td>{{$i->direccion}}</td>
                        <td>{{$i->importe}}</td>
                        <td style="text-align: center"><a  href="{{url('ventas/edit',['id'=>$i->id])}}"><i class="fas fa-edit mr-5"></i></a>
                        <a href="{{url('ventas/delete',['id'=>$i->id])}}"><i class="far fa-trash-alt"></i></a></td>
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
    $('#tabla_').DataTable({
        "order": [[ 0, "desc" ]]
    }
    );
});
</script>

@endsection