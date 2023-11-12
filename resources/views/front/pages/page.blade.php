@extends('front.layouts.app')

@section('content')

    @if (isset($page) && $page)
        {!! $page->description !!}
    @endif

@endsection
