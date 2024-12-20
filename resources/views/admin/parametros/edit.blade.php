@extends('layouts.admin')
@section('main-content')
@if (session('status'))
<div class="alert alert-success">
    {{ session('status') }}
</div>
@endif
<div class="jumbotron contaier form-inline align-items-start">
    <div class="card" style="width: 50rem;">
        <form action="{{url('/admin/parametros/update/')}}" enctype="multipart/form-data" method="POST">
            <div class="card-body">
                @csrf
                <input type="hidden" value="{{trim($data->clave)}}" name="clave">
                <div class="col col-12 mt-3">
                    <label class="sr-only" for="inlineFormInputGroup">Clave</label>
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <div class="input-group-text">Clave</div>
                        </div>
                        <input value="{{$data->clave}}" type="text" class="form-control" id="inlineFormInputGroup"
                            disabled>
                    </div>
                </div>
                <div class="col col-12 mt-3">
                    <label class="sr-only" for="inlineFormInputGroup">Valor</label>
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <div class="input-group-text">Valor</div>
                        </div>
                        <input value="{{$data->valor}}" type="text" class="form-control" id="inlineFormInputGroup" name="valor" >
                    </div>
                </div>
                @if (trim($data->clave) == "contacto_whatsapp")
                <p>El numero de telefono debe tener estas caracteristicas Ej:"595991747594"</p>
            @endif
            </div>
            <div class="card-footer text-right">
                <button type="submit" class="btn btn-primary">Actualizar</button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')

@endsection