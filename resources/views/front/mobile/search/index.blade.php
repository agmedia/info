@extends('front.desktop.layouts.store')

@section('title', __('ui.search.page_title'))
@section('main_class', 'w-full px-0 py-0')

@section('content')
    @include('front.desktop.search.index-content', [
        'searchQuery' => $searchQuery,
        'searchSections' => $searchSections,
        'searchTotalResults' => $searchTotalResults,
    ])
@endsection
