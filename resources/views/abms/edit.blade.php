@extends('layouts.admin')

@section('main-content')
<div class="col-sm-12">
  <div class="card shadow mb-4">
    {{-- <h4><a href="{{ url($foo['rute']) }}"> Listar {{ $foo['title'] }} </a></h4> --}}
    <div class="card-body">
      <div id='errores' style='display:none' class='alert alert-danger'></div>
        @include('abms.messages')
      <form id='abmform' method="POST" action="{{ url($foo['rute'].$foo['id']) }}" role="form">
        <input name="_method" type="hidden" value="PATCH">
        @include('abms.form')
    </div>
    <div class="card-footer">
      <div class="col-sm-offset-2 col-sm-10">
        <button type="reset" class="btn btn-secondary">Cancelar</button>
        <button type="submit" class="btn btn-primary">Guardar</button>
      </div>
    </div>
    </form>
  </div>
</div>
@endsection
