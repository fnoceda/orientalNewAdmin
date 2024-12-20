@extends('layouts.admin')
@section('main-content')
@if (session('status'))
    <div class="alert alert-success">
        {{ session('status') }}
    </div>
@endif
<div class="contaier">
<h4 > Crear Banners</h4>
    <div class="card" style="width: 50rem;">
    <form action="{{url('/banners/images/guardar/')}}" enctype="multipart/form-data"  method="POST">
            @csrf
            <div class="col col-12 mt-3">
                <label class="sr-only" for="inlineFormInputGroup">name</label>
                <div class="input-group mb-2">
                    <div class="input-group-prepend">
                        <div class="input-group-text">name</div>
                    </div>
                    <input type="text" class="form-control" id="inlineFormInputGroup" name="name" >
                </div>
            </div>
            <div class="col col-12 mt-3">
                <label class="sr-only" for="inlineFormInputGrou">Username</label>
                <div class="input-group mb-2">
                    <input type="file" class="custom-file-input" id="path" name="path">
                    <label class="custom-file-label" for="validatedCustomFile"></label>
                </div>
            </div>
            <div class="form-group col-md-4">
                <label for="inputState">Origen:</label>
                <select id="inputState" class="form-control col-12" name="destino">
                        <option value="header" selected="true">Header</option>
                        <option value="footer">Footer</option>
                        
                </select>
              </div>
            <div class="form-group col-md-4">
                <label for="inputState">Destino:</label>
                <select id="inputState" class="form-control col-12" name="seccion_id">
                    <option value="">Seleccione el destino</option>
                    @foreach ($data as $d)
                        <option value="{{$d->id}}">{{$d->name}}</option>
                    @endforeach
                </select>
              </div>
              
            <div class="col col-12 mt-3">
                <label class="sr-only" for="inlineFormInputGroup">Parametro</label>
                <div class="input-group mb-2">
                    <div class="input-group-prepend">
                        <div class="input-group-text">Parametro</div>
                    </div>
                    <input type="text" class="form-control" id="inlineFormInputGroup" name="parametro" >
                </div>
            </div>

            
            <button type="submit" class="btn btn-success ml-2 mb-3">Guardar</button>
        </form>
    </div>
</div>
@endsection 