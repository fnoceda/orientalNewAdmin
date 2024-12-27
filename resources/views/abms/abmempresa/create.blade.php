@extends('layouts.admin')

@section('main-content')
    <div id='errores' style='display:none' class='alert alert-danger'></div>
    <form action="{{url('/abms/empresa/guardar')}}" enctype="multipart/form-data" method="POST">
        <div class="form-group">
            <div class="card-body">
                @csrf
                <div class="col col-12 mt-3 mb-3">
                    <div class="input-group ">
                        <div class="input-group-prepend">
                            <div class="input-group-text">name</div>
                        </div>
                        <input type="text" class="form-control" id="inlineFormInputGroup" name="name">
                    </div>
                </div>
                <div class="col col-12 mt-3 mb-2">
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <div class="input-group-text">Ruc</div>
                        </div>
                        <input type="text" class="form-control" id="inlineFormInputGroup" name="ruc">
                    </div>
                </div>
                <div class="form-inline col-md-10 mb-2">
                    <div class="input-group-prepend">
                        <div class="input-group-text mr-5"><h6>Activo</h6></div>
                    </div>
                    <label class="switch">
                      <input type="checkbox" type="checkbox" name="es_activo"  id="es_activo" checked="true">
                      <span class="slider round"></span>
                    </label>
                </div>
                <div class="col col-12 mt-3 mb-3">
                    <div class="input-group ">
                        <label class="custom-file-label" for="path">Archivo</label>
                        <input type="file" class="custom-file-input" id="path" name="logo">
                    </div>
                </div>
                {{--  --}}
                
                  <div class="form-inline col-md-10 mb-2">
                    <div class="input-group-prepend">
                        <div class="input-group-text mr-5"><h6>Stock</h6></div>
                    </div>
                    <label class="switch">
                      <input type="checkbox" type="checkbox"  id="stock" name="stock" checked="true">
                      <span class="slider round"></span>
                    </label>
                  </div>
                <div class="col col-12 mt-3">
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <div class="input-group-text">Descripción</div>
                        </div>
                        <input type="text" class="form-control" id="inlineFormInputGroup" name="descripcion">
                    </div>
                </div>
            </div>
            <div class="col-sm-offset-2 col-sm-10">
                <button type="reset" class="btn btn-secondary">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </div>
    </form>
@endsection