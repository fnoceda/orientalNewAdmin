@extends('layouts.admin')

@section('main-content')

@php
    if (App::isLocale('en')) {
    echo "ingles";
}else {
    echo "español";
}
@endphp

@endsection