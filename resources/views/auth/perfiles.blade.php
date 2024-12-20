@extends('layouts.admin')
@section('main-content')
    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif
    <div>
        <div class="form-group row">
            <label class="col-sm-2 col-form-label col-form-label-sm" for="perfil">
                <h3>Elija el Perfil</h3>
            </label>
            <div class="col-sm-10">
                <select class="" id="perfil" name="perfil">
                    <option value="">Seleccione</option>
                    @foreach ($foo['perfiles'] as $perfil)
                        <option value="{{ $perfil->id }}"> {{ $perfil->name }} </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <hr>
    <form>
        <div id="accesos"></div>
    </form>
@endsection
@section('script')
    <script>
        $(document).ready(function() {
            $('#perfil').change(function(e) {
                if ($(this).val() != '') {

                    console.log('ejecutando');
                    e.preventDefault();
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: "{{ url('/privilegios') }}",
                        method: 'post',
                        data: {
                            perfil: $('#perfil').val()
                        },
                        success: function(result) {
                            console.log('Se obtuvo el perfil solicitado exitosamente');
                            //console.log(result);
                            $('#accesos').html(result);
                        },
                        error: function(xhr, ajaxOptions, thrownError) {
                            console.log('Error al obtener datos del perfil');
                            console.log(xhr.responseText);
                        }
                    });
                }
            });
        });
    </script>
@endsection
