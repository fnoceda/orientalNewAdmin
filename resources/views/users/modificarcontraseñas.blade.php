@extends('layouts.admin')
@section('main-content')
<div class="alert alert-info">
    <h4>
        <ul>Debe contener un minimo seis caracteres</ul>
    </h4>
</div>
{{-- pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}" --}}
<form id='estado_de_cuenta' method="POST" action="{{ url('/usuario/pass/') }}" class="form-horizontal" role="form">
    {{ csrf_field() }}
    <div class="form-group row">
        <label class="col-sm-2 col-form-label col-form-label-sm" for="password">Nueva contraseña</label>
        <div class="col-sm-10">
            <input false class="form-control form-control-sm false character check-seguridad "
                type="password" id="password" name="password" required />
            <div class="text-danger">{{$errors->first('password')}}</div>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-2 col-form-label col-form-label-sm" for="password_confirmation">Repita la
            contraseña</label>
        <div class="col-sm-10">
            <input false class="form-control form-control-sm false character" type="password" id="password_confirmation"
                name="password_confirmation" required />
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-2 col-form-label col-form-label-sm" for="contraseña_actual">Contraseña actual</label>
        <div class="col-sm-10">
            <input false class="form-control form-control-sm false character" type="password" id="contraseña_actual"
                name="contraseña_actual" required />
            <div class="text-danger">{{$errors->first('contraseña_actual')}}</div>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-2">
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        </div>
    </div>
</form>
@endsection