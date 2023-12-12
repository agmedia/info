@extends('front.layouts.app')

@section('content')

    @include('front.layouts.partials.session')

    @if (isset($page) && $page)
        {!! $page->description !!}
    @endif

@endsection
