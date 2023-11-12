@extends('back.layouts.backend')

@push('css_before')
    <link href="{{ asset('vendor/file-manager/css/file-manager.css') }}" rel="stylesheet">
@endpush

@section('content')

    <div class="bg-body-light">
        <div class="content content-full">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill font-size-h2 font-w400 mt-2 mb-0 mb-sm-2">File Manager</h1>
            </div>
        </div>
    </div>

    @include('back.layouts.partials.session')

    <div class="content content-full">
        <div id="fm-main-block">
            <div id="fm"></div>
        </div>
    </div>

@endsection

@push('js_after')
    <script src="{{ asset('vendor/file-manager/js/file-manager.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('fm-main-block').setAttribute('style', 'height:' + window.innerHeight + 'px');

            fm.$store.commit('fm/setFileCallBack', function(fileUrl) {
                window.opener.fmSetLink(fileUrl);
                window.close();
            });
        });
    </script>
@endpush
