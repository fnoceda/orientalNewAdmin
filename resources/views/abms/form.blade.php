<input type="hidden" name="_token" value="{{ csrf_token() }}">
@foreach($foo['cols'] as $col)
    @if( ($col['name'] == 'id') or  ($col['type'] == 'hidden'))
        <input
        type="hidden"
        name="{{ $col['name'] }}"
        id="{{ $col['name'] }}"
        value="{{ $reg[$col['name']] }}">
    @else
        <div class="form-group row">
            <label class="col-sm-2 col-form-label col-form-label-sm" for="{{ $col['name'] }}">{{ __(ucwords(str_replace('_',' ',$col['label']))) }}</label>

            <div class="col-sm-10">
                @if(empty($col['fktable'])) {{-- Si NO es fk --}}

                    @if( in_array(strtok($col['type'], ' '), array('hidden', 'character', 'integer', 'numeric(10,0)', 'number', 'date')) )
                        <input {{ $col['required'] }} class="form-control form-control-sm {{ $col['required'] }} {{ $col['type'] }} "
                        type="text" id="{{ $col['name'] }}" name="{{ $col['name'] }}"
                        value="{{ trim($reg[$col['name']]) }}"
                            @if(!empty($col['disabled']))
                                disabled="disabled"
                            @endif
                        />
                    @else
                        @if(in_array(strtok($col['type'], ' '), array('boolean')))

                            @if(empty($reg))
                            <div class="btn-group btn-group-toggle {{ $col['required'] }}" data-toggle="buttons">

                                <label class="btn btn-outline-secondary">
                                    <input type="radio" name="{{ $col['name'] }}" id="{{ $col['name'].'_si' }}" autocomplete="off" value="true" > Si
                                </label>

                                <label class="btn btn-outline-secondary active">
                                    <input type="radio" name="{{ $col['name'] }}" id="{{ $col['name'].'_no' }}" autocomplete="off" value="false" checked> No
                                </label>

                            </div>
                            @else
                            <div class="btn-group btn-group-toggle {{ $col['required'] }}" data-toggle="buttons">

                                <label class="btn btn-outline-secondary
                                @if($reg[$col['name']] == true)
                                        active
                                    @endif
                                ">
                                    <input
                                    @if($reg[$col['name']] == true)
                                        checked
                                    @endif
                                    type="radio" name="{{ $col['name'] }}" id="{{ $col['name'].'_si' }}" autocomplete="off" value="true" >Si
                                </label>

                                <label class="btn btn-outline-secondary
                                @if($reg[$col['name']] == false)
                                        active
                                    @endif
                                ">
                                    <input
                                    @if($reg[$col['name']] == false)
                                        checked
                                    @endif
                                    type="radio" name="{{ $col['name'] }}" id="{{ $col['name'].'_no' }}" autocomplete="off" value="false" > No
                                </label>

                            </div>
                            @endif
                        @endif
                    @endif

                @else {{-- SI ES FK --}}
                    @if(empty($col['opciones'])) {{-- Si esta vacio mostramos una alerta --}}
                        {{ 'NO hay ningun "'.strtoupper($col['label']).'"  cargado por favor primero cargue algun registro en esa tabla' }}
                    @else
                        @if( count($col['opciones']) < 5 ) {{-- si tiene hasta 5 opciones lo hacemos un radio --}}
                            <div class="btn-group btn-group-toggle {{ $col['required'] }}" data-toggle="buttons">
                                @foreach($col['opciones'] as $op)
                                    @if(!empty($op['id']))
                                        <label class="btn btn-outline-secondary
                                        @if($reg[$col['name']] == $op['id'])
                                                active
                                            @endif
                                        ">
                                            <input class=" {{ $col['required'] }} "
                                            @if($reg[$col['name']] == $op['id'])
                                                checked
                                            @endif
                            type="radio" value="{{ $op['id'] }}" name="{{ $col['name'] }}" id="{{ $col['name'].'_'.$op['id'] }}" autocomplete="off" > {{ $op['descripcion'] }}
                                        </label>
                                        @endif
                                @endforeach
                            </div>
                        @else {{-- Si tiene mas opciones lo mostramos en un select --}}
                            <select class='custom-select my-1 mr-sm-2 {{ $col['required'] }} ' name='{{ $col['name'] }}' id='{{ $col['name'] }}'>
                                @foreach($col['opciones'] as $op)
                                    <option
                                    @if($reg[$col['name']] == $op['id'])
                                        selected='selected'
                                    @endif
                                    value='{{ $op['id'] }}' >{{ $op['descripcion'] }}</option>
                                @endforeach
                            </select>
                        @endif
                    @endif {{-- empty($col['opciones']) --}}
                @endif {{-- empty($col['fktable']) --}}
            </div> {{-- Cierra el div del input --}}
        </div> {{-- Cierra el div del formGroup --}}
    @endif {{-- if($col['name'] == 'id') --}}
@endforeach{{-- foreach($foo['cols'] as $col) --}}
@section('script')
<script>
$( document ).ready(function() {
    console.log("ready");
    @if($foo['table'] == 'sabores') 

    $("#name").on("keypress", function () {
       $input=$(this);
       setTimeout(function () {
        $input.val($input.val().toUpperCase());
       },50);
      })
    @endif
    @if($foo['table'] == 'medidas') 

    $("#name").on("keypress", function () {
       $input=$(this);
       setTimeout(function () {
        $input.val($input.val().toUpperCase());
       },50);
      })
    @endif
});
</script>   
@endsection