@extends('layouts.admin')
@section('main-content')
@if (session('status'))
    <div class="alert alert-success">
        {{ session('status') }}
    </div>
@endif
<div class="contaier">
<h4 > Crear {{$tabla}}</h4>
    <div class="card" style="width: 50rem;">
    <form action="{{url('/admin/images/guardar',['tabla' => $tabla])}}" enctype="multipart/form-data"  method="POST">
            @csrf
            <div class="col col-12 mt-3">
                <label class="sr-only" for="inlineFormInputGroup">Username</label>
                <div class="input-group mb-2">
                    <div class="input-group-prepend">
                        <div class="input-group-text">name</div>
                    </div>
                    <input type="text" class="form-control" id="inlineFormInputGroup" name="name" placeholder="Username">
                </div>
            </div>
            @if ($tabla == "etiquetas")
            <div class="col col-12 mt-3">
                <label class="sr-only" for="inlineFormInputGroup">Porcentaje Descuento</label>
                <div class="input-group mb-2">
                    <div class="input-group-prepend">
                        <div class="input-group-text">Porcentaje Descuento</div>
                    </div>
                    <input type="number" class="form-control" id="inlineFormInputGroup" name="porcentaje_descuento" placeholder="porcentaje_descuento">
                </div>
            </div>
            @endif
        <input type="hidden" value="{{$tabla}}" id="tabla_name" name="tabla_name">
            <div class="col col-12 mt-3">
                <label class="sr-only" for="inlineFormInputGrou">Username</label>
                <div class="input-group mb-2">
                    <input type="file" class="custom-file-input" id="path" name="path">
                    <label class="custom-file-label" for="validatedCustomFile"></label>
                </div>
            </div>
            <button type="submit" class="btn btn-success ml-2 mb-3">Guardar</button>
        </form>
    </div>
</div>
@endsection 