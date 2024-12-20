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
        {{-- <a href="{{url('/banners/images/create')}}" class="btn btn-primary">Crear Nuevo <i class="fas fa-plus"></i></a> --}}
        </div>
    </div>
    <div class="card-body">
        <table id="tabla_">
            <thead>
                <tr>
                    <td>#Clave</td>
                    <td>Valor</td>
                    <td class=" ml-5">Acciones</td>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $i)
                    <tr>
                        <td>{{$i->clave}}</td>
                        <td>{{$i->valor}}</td>
                        <td style="text-align: center"><a  href="{{url('/admin/parametros/edit',['clave'=>$i->clave])}}"><i class="fas fa-edit mr-5"></i></a></td>
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
