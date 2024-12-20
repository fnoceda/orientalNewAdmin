@extends('layouts.auth')

@section('main-content')
    <div class="container">
        @include('error.error')
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-12 col-md-9">
                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <div class="row">
                            <div class="col-lg-6 d-none d-lg-block bg-login-image">
                                <img src="{{ asset('img/oriental_logo.jpg') }}" alt="user-image">
                            </div>
                            <div class="col-lg-6">
                                <div class="p-5 mb-0">
                                    <div class="ml-3">
                                        <h3>Shopp Oriental</h3>
                                    </div>
                                    <label class="ml-3">Lamentamos que tengas que irte</label>
                                    @if (isset($user))
                                        <label class="ml-3">{{ $user->name }}</label>
                                        <div class="ml-1">
                                            <ul>
                                                <li>No volveras a tener acceso a la app de Shopp Oriental</li>
                                            </ul>
                                        </div>
                                    @else
                                        <div class="ml-1">
                                            <ul>
                                                <li>Por favor ingresa tus credenciales</li>
                                            </ul>
                                        </div>
                                    @endif
                                </div>
                                <div class="p-5">
                                    @if (!isset($user))
                                        <form method="POST" action="{{ route('autenticar') }}" class="user ml-5">
                                            <div class="form-group">
                                                <input type="email" class="form-control form-control-user" name="email"
                                                    placeholder="{{ __('E-Mail Address') }}" value="{{ old('email') }}"
                                                    required autofocus>
                                            </div>

                                            <div class="form-group">
                                                <input type="password" class="form-control form-control-user"
                                                    name="password" placeholder="{{ __('Password') }}" required>
                                            </div>
                                        @else
                                            <form method="POST" action="{{ route('baja') }}" class="user ml-5">
                                                <input type="hidden" id="user_id" name="user_id" value="{{ $user->id }}" >
                                                <input type="hidden" id="token_baja" name="token_baja"
                                                    value="{{ $user->baja_token }}">
                                    @endif
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary btn-user btn-block">
                                            @if (!isset($user))
                                                Autenticar
                                            @else
                                                Desactivar Usuario
                                            @endif
                                        </button>
                                    </div>
                                    <hr>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
