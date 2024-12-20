@extends('adminlte::page')

@section('htmlheader_title')
	Change Title here!
@endsection


@section('main-content')

<div class="callout callout-danger">
	<h4>Error!</h4> 
	<p>{!! $foo['error'] !!}</p>
</div>   

	
@endsection
