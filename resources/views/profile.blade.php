@extends('layouts.admin')

@section('main-content')
@if(session()->has('message'))
    <div class="alert alert-success">
        {{ session()->get('message') }}
    </div>
@endif
<!-- Page Heading -->
<h1 class="h3 mb-4 text-gray-800">{{ __('Profile') }}</h1>

<div class="row">

    <div class="col-lg-4 order-lg-2">

        <div class="card shadow mb-4">
            <div class="card-profile-image mt-4">

                <figure class="rounded-circle avatar avatar font-weight-bold"
                    style="font-size: 60px; height: 180px; width: 180px;" data-initial="{{ Auth::user()->name[0] }}">
                </figure>
            </div>
            <div class="card-body">

                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center">
                            <h5 class="font-weight-bold">{{  Auth::user()->fullName }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="col-lg-8 order-lg-1">

        <div class="card shadow mb-4">

            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Mi Cuenta</h6>
            </div>

            <div class="card-body">

                <h6 class="heading-small text-muted mb-4">Informacion de Usuario</h6>

                <div class="pl-lg-4">
                    <form method="POST" action="{{ url('/usuarios/editar') }}" autocomplete="off">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="id" id="id" value="{{Auth::user()->id}}">
                        <input type="hidden" name="perfil" id="perfil" value="{{Auth::user()->perfil}}">
                        <input type="hidden" name="perfil_id" id="perfil_id" value="{{Auth::user()->perfil_id}}">
                        <input type="hidden" name="seccionPerfil" id="seccionPerfil" value="si">

                        <div class="row">
                            <div class="col-lg-4">
                                <div class="form-group focused">
                                    <label class="form-control-label" for="name">Nombre<span
                                            class="small text-danger">*</span></label>
                                    <input type="text" id="name" class="form-control" name="name" placeholder="Name"
                                        value="{{ old('name', Auth::user()->name) }}">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group focused">
                                    <label class="form-control-label" for="name">Email<span
                                            class="small text-danger">*</span></label>
                                    <input type="email" id="email" class="form-control" name="email" placeholder="email"
                                        value="{{ old('name', Auth::user()->email) }}">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group focused">
                                    <label class="form-control-label" for="name">Ruc<span
                                            class="small text-danger">*</span></label>
                                    <input type="text" id="ruc" class="form-control" name="ruc" placeholder="Ruc"
                                        value="{{ old('name', Auth::user()->ruc) }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="form-group focused">
                                    <label class="form-control-label" for="name">Telefono<span
                                            class="small text-danger">*</span></label>
                                    <input type="text" id="telefono" class="form-control" name="telefono" placeholder="telefono"
                                        value="{{ old('name', Auth::user()->telefono) }}">
                                </div>
                            </div> 
                            <div class="col-lg-4">
                                <div class="form-group focused">
                                    <label class="form-control-label" for="name">Direccion<span
                                            class="small text-danger">*</span></label>
                                    <input type="text" id="direccion" class="form-control" name="direccion" placeholder="Direccion"
                                        value="{{ old('name', Auth::user()->direccion) }}">
                                </div>
                            </div> 
                            <div class="col-lg-4">
                                <div class="form-group focused">
                                    <label class="form-control-label" for="name">Direccion delivery<span
                                            class="small text-danger">*</span></label>
                                    <input type="text" id="direccion_delivery" class="form-control" name="direccion_delivery" placeholder="Delivery"
                                        value="{{ old('name', Auth::user()->direccion_delivery) }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="form-group focused">
                                    <label class="form-control-label" for="current_password">Ciudad*</label>
                                    <select name="ciudad_id" id="ciudad_id" class="form-control" required>
                                        <option value="" disabled>Seleccione una Ciudad</option>
                                        @foreach($ciudades as $l)
                                            @if ($l->id == Auth::user()->ciudad_id)
                                                <option value="{{ $l->id }}" selected>{{ $l->name }}</option>
                                                    @else
                                                <option value="{{ $l->id }}">{{ $l->name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group focused">
                                    <label class="form-control-label" for="name">Latitud<span
                                            class="small text-danger">*</span></label>
                                    <input type="text" id="latitud" class="form-control" name="latitud" placeholder="latitud"
                                        value="{{ old('name', Auth::user()->latitud) }}">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group focused">
                                    <label class="form-control-label" for="name">Longitud<span
                                            class="small text-danger">*</span></label>
                                    <input type="text" id="longitud" class="form-control" name="longitud" placeholder="longitud"
                                        value="{{ old('name', Auth::user()->longitud) }}">
                                </div>
                            </div>
                        </div>
                        <div class="pl-lg-4">
                            <div class="row">
                                <div class="col text-center">
                                    <button type="submit" class="btn btn-primary">Actualizar Perfil</button>
                                </div>
                            </div>
                        </div>
                    </form>
                    {{-- contraseñas --}}
                    <hr>
                    <p>Cambio Contraseña</p>
                    <hr>
                    <form method="POST" action="{{ url('/usuario/pass/') }}" autocomplete="off">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="id_usuario" id="id_usuario" value="{{Auth::user()->id}}">
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="form-group focused">
                                    <label class="form-control-label" for="current_password">Contraseña Actual</label>
                                    <input type="password" id="current_password" class="form-control"
                                        name="current_password" placeholder="Current password">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group focused">
                                    <label class="form-control-label" for="new_password">Nueva Contraseña</label>
                                    <input type="password" id="new_password" class="form-control" name="new_password"
                                        placeholder="New password">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group focused">
                                    <label class="form-control-label" for="confirm_password">Confirmar
                                        Contraseña</label>
                                    <input type="password" id="confirm_password" class="form-control"
                                        name="password_confirmation" placeholder="Confirm password">
                                </div>
                            </div>
                        </div>
                        <!-- Button -->
                        <div class="pl-lg-4">
                            <div class="row">
                                <div class="col text-center">
                                    <button type="submit" class="btn btn-primary">Cambiar Contraseña</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>

    </div>

</div>

@endsection
@section('script')
<script>
    $(document).ready(function (){
    

         
            
    });
</script>
@endsection