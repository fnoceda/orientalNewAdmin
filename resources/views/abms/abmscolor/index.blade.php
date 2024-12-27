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
        <a href="{{url('/abms/colores/colors/create')}}" class="btn btn-primary">Crear Nuevo <i class="fas fa-plus"></i></a>
        </div>
    </div>
    <div class="card-body">
        <table id="tabla_">
            <thead>
                <tr>
                    <td>#id</td>
                    <td>name</td>
                    <td>color</td>
                    <td class=" ml-5">Acciones</td>
                </tr>
            </thead>
            <tbody>
                @foreach ($color as $i)
                    <tr>
                        <td>{{$i->id}}</td>
                        <td >{{$i->name}}</td>
                        @php
                            echo '<td><p style="background:'.$i->name.';">COLOR</p></td>';
                        @endphp
                        <td style="text-align: center"><a  href="{{url('/abms/colores/colors/edit',['id'=>$i->id])}}"><i class="fas fa-edit mr-5"></i></a>
                        <a href="{{url('/abms/colores/colors/delete',['id'=>$i->id])}}"><i class="far fa-trash-alt"></i></a></td>
                        </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
@section('script')
<script src="{{ asset('js/jscolor.js') }}">
</script>
<script>
$(document).ready(function() {
    $('#tabla_').DataTable();
});
</script>

@endsection
