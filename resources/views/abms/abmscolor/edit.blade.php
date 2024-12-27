@extends('layouts.admin')
@section('main-content')
@if (session('status'))
<div class="alert alert-success">
    {{ session('status') }}
</div>
@endif
<div class="jumbotron contaier form-inline align-items-start">
    <div class="card" style="width: 50rem;">
        <div class="card-header">
            Editar color {{ $color->name }}
        </div>
        <form action="{{url('/abms/colores/colors/update')}}" enctype="multipart/form-data" method="POST">
            <div class="card-body">
                @csrf
                <input type="hidden" name="id" value="{{$color->id }}">
                <input data-jscolor="" name="name" value="{{$color->name }}">
            </div>
            <div class="card-footer text-right">
                <button type="submit" class="btn btn-primary">Actualizar</button>
            </div>
        </form>
    </div>
</div>

@endsection
@section('scripts')
<script src="{{ asset('js/jscolor.js') }}">
</script>
@endsection
