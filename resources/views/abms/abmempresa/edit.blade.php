@extends('layouts.admin')

@section('main-content')
<div id='errores' style='display:none' class='alert alert-danger'></div>
<form action="{{url('/abms/empresa/update')}}" enctype="multipart/form-data" method="POST">
    <div class="form-group">
        <div class="card-body">
            @csrf
            <input type="hidden" name="id" value="{{$empresa->id}}">
            <div class="col col-12 mt-3 mb-3">
                <div class="input-group ">
                    <div class="input-group-prepend">
                        <div class="input-group-text">name</div>
                    </div>
                    <input type="text" class="form-control" id="inlineFormInputGroup" name="name"
                        value="{{$empresa->name}}">
                </div>
            </div>
            <div class="col col-12 mt-3 mb-2">
                <div class="input-group mb-2">
                    <div class="input-group-prepend">
                        <div class="input-group-text">Ruc</div>
                    </div>
                    <input type="text" class="form-control" id="inlineFormInputGroup" name="ruc"
                        value="{{$empresa->ruc}}">
                </div>
            </div>

            <div class="col col-12 mt-3">
                <div class="input-group mb-2">
                    <label class="custom-file-label" for="path"></label>
                    <input type="file" class="custom-file-input" id="path" name="logo">
                </div>
            </div>
            <div class="col col-12 mt-3">
                <div class="input-group mb-2">
                    <div class="input-group-prepend">
                        <div class="input-group-text">Descripción</div>
                    </div>
                    <input type="text" class="form-control" id="inlineFormInputGroup" name="descripcion"
                        value="{{$empresa->descripcion}}">
                </div>
            </div>
            <div class="form-inline col-md-10 mb-2">
                <div class="input-group-prepend">
                    <div class="input-group-text mr-5">
                        <h6>Activo</h6>
                    </div>
                </div>
                <label class="switch">
                    <input type="checkbox" name="es_activo" id="es_activo" @if ($empresa->es_activo ==true)
                    checked="true"
                    @endif>
                    <span class="slider round"></span>
                </label>
            </div>
            <div class="form-inline col-md-10 mb-2">
                <div class="input-group-prepend">
                    <div class="input-group-text mr-5">
                        <h6>Stock</h6>
                    </div>
                </div>
                <label class="switch">
                    <input type="checkbox" id="stock" name="stock" @if ($empresa->stock ==true)
                    checked="true"
                    @endif>
                    <span class="slider round"></span>
                </label>
            </div>
        </div>
        <div class="col-sm-offset-2 col-sm-10">
            <button type="reset" class="btn btn-secondary">Cancelar</button>
            <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
    </div>
</form>
@if ($empresa->logo != null)
<div class="card ml-3 p-3">
    <div class="card-header">Imagen</div>
    <div class="card-body">
        <img id="img_muestra" src="{{ asset('/storage/empresas/'.$empresa->logo) }}" alt="user-image" width="300px">
    </div>
    <div class="card-footer">
        <small class="text-muted">Para Cambiar Seleccione Archivo</small>
    </div>
</div>
@endif
@endsection