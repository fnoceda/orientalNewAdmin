@extends('layouts.admin')

@section('main-content')
    <div id='errores' style='display:none' class='alert alert-danger'></div>
    @include('abms.messages')
    <form id='abmform' method="POST" action="{{ url($foo['rute']) }}" class="form-horizontal" role="form">
        @include('abms.form')
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="reset" class="btn btn-secondary">Cancelar</button>
                {{-- <button type="button" class="btn btn-primary" onclick=" validarForm(this.form); ">Guardar</button> --}}
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </div>
    </form>
@endsection

