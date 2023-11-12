<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <title> @yield('title') </title>
    <!-- SEO Meta Tags-->
    <meta name="description" content="@yield('description')">
    <meta name="author" content="@yield('title')">
    @stack('meta_tags')
    <!-- Viewport-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <!-- Favicon and Touch Icons-->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ config('settings.images_domain') . 'favicon-32x32.png' }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ config('settings.images_domain') . 'favicon-32x32.png' }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ config('settings.images_domain') . 'favicon-16x16.png' }}">
    <link rel="mask-icon" href="{{ config('settings.images_domain') . 'safari-pinned-tab.svg' }}" color="#18326d">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="theme-color" content="#ffffff">
    <!-- Font Imports -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <link rel="stylesheet" media="screen" href="{{ asset('css/koros.css') }}">
    <link rel="stylesheet" media="screen" href="{{ asset('css/font-icons.css?v=1.0') }}">
    <style>
        [v-cloak] { display:none !important; }
    </style>
</head>
<!-- Body-->
<body class="stretched search-overlay dark">

<div id="agapp">
    <div id="wrapper" class="clearfix">
        @include('front.layouts.widgets.header')
        @yield('content')
        @include('front.layouts.widgets.footer')
    </div>
</div>

<!-- Back To Top Button-->
<div id="gotoTop" class="uil uil-angle-up"></div>

<script src="{{ asset('js/jquery.js') }}"></script>
<script src="{{ asset('js/plugins.min.js') }}"></script>
<script src="{{ asset('js/functions.bundle.js') }}"></script>

<script>
    jQuery(document).ready( function(){
        $('#search-input').on('keyup', (e) => {
            if (e.keyCode == 13) {
                e.preventDefault();
                $('search-form').submit();
            }
        });

        $('input[type="file"]').change(function(e){
            var fileName = e.target.files[0].name;
            $('.custom-file-label').html(fileName);
        });

        $(function(){
            if(window.location.hash) {
                var hash = window.location.hash;
                $(hash).modal('toggle');
            }
        });
    });
</script>

@stack('js_after')

</body>
</html>
