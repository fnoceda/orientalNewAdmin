@extends('adminlte::layouts.app')

@section('htmlheader_title')
    {{ $foo['title'] }}
@endsection

@section('main-content')
   
    <div id='errores' style='display:none' class='alert alert-danger'></div>
    @include('abms.messages')
    <form id='abmform' method="get" action="{{ route($foo['rute']['name'], $foo['rute']['params']) }}" class="form-horizontal" role="form">
        
            

            @foreach($foo['cols'] as $col)
            @if($col['type'] == 'hidden')
                <input type="hidden" name="{{ $col['name'] }}" id="{{ $col['name'] }}" value="{{ trim($col['value']) }}">
            @else
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label col-form-label-sm" for="{{ $col['name'] }}">{{ ucwords(str_replace('_',' ',$col['label'])) }}</label>
                    
                    <div class="col-sm-10">
                        @if(empty($col['fktable'])) {{-- Si NO es fk --}}
                            @if(in_array(strtok($col['type'], ' '), array('div')) )
                                <div id="{{ $col['name'] }}"></div>
                            @elseif( in_array(strtok($col['type'], ' '), array('character', 'integer', 'numeric(10,0)', 'number', 'date')) )
                                <input {{ $col['required'] }} class="form-control form-control-sm {{ $col['required'] }} {{ $col['type'] }} " 
                                type="text" id="{{ $col['name'] }}" name="{{ $col['name'] }}" 
                                value="{{ trim($reg[$col['name']]) }}" /> 
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
                                            type="radio" name="{{ $col['name'] }}" id="{{ $col['name'].'_si' }}" autocomplete="off" value="true" > Si
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
            
           

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="button" class="btn btn-primary" onclick=" validarForm(this.form); ">Ver</button>
            </div>
        </div>
    </form>
@endsection

@section('scripts')
<script>

    $( document ).ready(function(){
        console.log('document.ready');    
    });

    @if($foo['table'] == 'cl_contratos') 
        console.log('entra en cl_contratos');
        $("form input[name='sucursal']").change(function(){
            //alert( $(this).val() );
            $.ajax
                ({
                    url: '{!! url("get_data") !!}',
                    type:'get',
                    dataType:'json',
                    data:{ t : 'sucursales', i : $(this).val()}, 
                    success: function (data)
                    {
                        
                        $.each( data, function (key, val) {
                            if($('form #'+key).length > 0){
                                $('form #'+key).val(val);
                            }
                        });
                    }, 
                    error: function(){
                        console.log('Ocurrio un error en el request');
                    }
                });
        });

    @elseif($foo['table'] == 'cl_ventas') 
        console.log('entra en cl_ventas');

        $("form input[name='sucursal']").change(function(){ getTimbrados($(this).val()); });



        function getTimbrados(sucursal){
            $.ajax
                ({
                    url: '{!! route("get_timbrados") !!}',
                    type:'get',
                    dataType:'json',
                    data:{ sucursal : sucursal }, 
                    success: function (data)
                    {
                        //console.log(data);
                        
                        console.log( 'elementos=> '+ Object.keys(data).length  );
                        var html = '<select id="timbrado" name="timbrado" class="required form-control">';
                        if(Object.keys(data).length == 1){
                            
                            $.each(data, function (i, items) {
                                console.log(items.timbrado+'=>'+items.numero_timbrado);
                                html += '<option value="'+items.timbrado+'">'+items.numero_timbrado+'</option>';
                            });
                        }else{
                           html += '<option value="">Seleccione un timbrado</option>';
                            $.each(data, function (i, items) {
                                console.log(items.timbrado+'=>'+items.numero_timbrado);
                                html += '<option value="'+items.timbrado+'">'+items.numero_timbrado+'</option>';
                            });
                        }
                        html += '</select>';                        
                        $('#timbrado').html(html);

                    }, 
                    error: function(){
                        console.log('Ocurrio un error en el request');
                    }
                });
        }


    @else
        console.log('no entra en nada');
    @endif
</script>
@endsection


