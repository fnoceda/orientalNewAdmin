@extends('adminlte::layouts.app')
@section('main-content')
    <h4><a href="{{ url($foo['rute']) }}"> Listar {{ $foo['title'] }} </a></h4>
    <div id='errores' style='display:none' class='alert alert-danger'></div>
    @include('abms.messages')
    <form id='abmform' method="post" action="{{ url($foo['rute']) }}" class="form-horizontal" role="form">
        @include('abms.form')
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="reset" class="btn btn-secondary">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick=" validarForm(this.form); ">Buscar</button>
            </div>
        </div>
    </form>
@endsection
