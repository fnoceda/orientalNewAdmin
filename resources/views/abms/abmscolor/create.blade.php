@extends('layouts.admin')
@section('main-content')
@if (session('status'))
<div class="alert alert-success">
    {{ session('status') }}
</div>
@endif
<div class="contaier align-top">
    <div class="card" style="width: 50rem;">
        <div class="card-header">
            Nuevo Color
        </div>

        <form action="{{url('/abms/colores/colors/guardar')}}" enctype="multipart/form-data" method="POST">
            <div class="card-body">
                @csrf
                <div class="col col-12 mt-9">
                    <p>
                        Seleccione un color
                        <button data-jscolor="{
                            onChange: 'update(this, \'#pr1\')',
                            alpha:0.7, value:'CCFFAA'}"></button>
                    </p>
                    <em id="pr1" style="display:inline-block; padding:1em;">change event</em>
                    <input type="hidden" id="name"  name="name">
                </div>
            </div>
            <div class="card-footer text-right">
                <button type="submit" class="btn btn-success ml-2 mb-3">Guardar</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/jscolor.js') }}">
</script>
<script>
function update(picker, selector) {
    $('#name').attr('value',picker.toHEXString());
    document.querySelector(selector).style.background = picker.toBackground()
}
jscolor.trigger('input change');
</script>
@endsection
