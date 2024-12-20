@extends('layouts.admin')
@section('main-content')
@if (session('status'))
    <div class="alert alert-success">
        {{ session('status') }}
    </div>
@endif
<div class="contaier form-inline">
    
    <div class="card" style="width: 50rem;">
        <h4>Editar {{$tabla}}</h4>
    <form action="{{url('/admin/imagenes/update/')}}" enctype="multipart/form-data"  method="POST">
            @csrf
            <div class="col col-12 mt-3">
                <label class="sr-only" for="inlineFormInputGroup">Username</label>
                <div class="input-group mb-2">
                    <div class="input-group-prepend">
                        <div class="input-group-text">name</div>
                    </div>
                <input value="{{$data->name}}" type="text" class="form-control" id="inlineFormInputGroup" name="name" >
                </div>
            </div>
            @if ($tabla == "etiquetas")
            <div class="col col-12 mt-3">
                <label class="sr-only" for="inlineFormInputGroup">Porcentaje Descuento</label>
                <div class="input-group mb-2">
                    <div class="input-group-prepend">
                        <div class="input-group-text">Porcentaje Descuento</div>
                    </div>
                    <input type="number" class="form-control" id="inlineFormInputGroup" value="{{$data->porcentaje_descuento}}" name="porcentaje_descuento" placeholder="porcentaje_descuento">
                </div>
            </div>
            @endif
        <input type="hidden" value="{{$tabla}}" id="tabla_name" name="tabla_name">
        <input type="hidden" value="{{$id}}" id="id" name="id">
            <div class="col col-12 mt-3">
                <label class="sr-only" for="inlineFormInputGrou">Username</label>
                <div class="input-group mb-2">
                    <input type="file" class="custom-file-input" id="path" name="path">
                    <label class="custom-file-label" for="validatedCustomFile"></label>
                </div>
            </div>
            <button type="submit" class="btn btn-success ml-2 mb-3">Actualizar</button>
        </form>
    </div>
    <div  class="card col-4 float-right">
        <div  class="card card-auto " >
            Imagen 
        </div>
        <div align="center">
        @if ($tabla=="iconos")
        <img id="img_muestra" src="{{ asset('/storage/img/'.$data->path) }}"  alt="user-image" width="147px" height="147px">
        @endif
        @if ($tabla=="etiquetas")
        <img id="img_muestra" src="{{ asset('/storage/img/'.$data->path) }}"  alt="user-image" width="147px" height="147px">
        @endif
    </div>
    </div>
</div>

@endsection 