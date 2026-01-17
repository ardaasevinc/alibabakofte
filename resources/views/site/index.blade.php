@extends('site.layouts.site')

@section('content')
@include('site.components.banner')
@include('site.components.about')
@include('site.components.images') 
{{-- @include('site.components.menu') --}}
@include('site.components.special')
@include('site.components.video')
@include('site.components.instagram')
@include('site.components.contact')
@include('site.components.whatsapp')
@endsection