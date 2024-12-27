
 {{-- @extends('admin.layout')

 @section('content') --}}

 @foreach( $producto as $product)
    {{--*/ @$producto = '"'.$product->name.'"' /*--}}

    <div>
        {!! DNS1D::getBarcodeHTML($product, "C128")!!}
    </div>
    <div style="padding-top: 50px;width: 24%;">
        {{ $product->name }}
    </div>


@endforeach





<style>
    .code {
        height: 80px !important;
    }
</style>
{{-- @endsection --}}