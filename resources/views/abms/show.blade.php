@extends('layouts.admin')

@section('main-content')
    @foreach($reg as $key => $val)

    <div class='col-sm-6'>
        <div class="form-group row">
            <label class="col-sm-2 col-form-label col-form-label-sm">{{ ucwords(str_replace('_',' ',$key)) }}</label>
            <div class="col-sm-4 col-form-label col-form-label-sm">
                {{ $val }}
            </div>
        </div>
    </div>
    
    @endforeach
@endsection
