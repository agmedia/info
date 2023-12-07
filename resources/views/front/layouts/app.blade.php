<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <meta http-equiv="x-ua-compatible" content="IE=edge">
    <meta name="author" content="Focus Branding">

    @if (isset($page) && ! empty($page))
        <meta name="description" content="{{ isset($page->meta_description) ? $page->meta_description : '' }}">
        <title>{{ isset($page->meta_title) ? $page->meta_title : '' }}</title>
    @else
        <meta name="description" content="Focus Branding">
        <title>Focus Branding</title>
    @endif

    <!-- Font Imports -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Semi+Condensed:wght@100;400;700&family=Inter:wght@100;400;700&display=swap" rel="stylesheet">

    <!-- Favicon -->
    <link rel="manifest" href="{{ asset('img/favicon/manifest.webmanifest') }}">
    <link rel="icon" href="{{ asset('img/favicon/favicon.ico') }}" sizes="any">
    <link rel="icon" href="{{ asset('img/favicon/icon.svg') }}" type="image/svg+xml">
    <link rel="apple-touch-icon" href="{{ asset('img/favicon/apple-touch-icon.png') }}">

    <!-- Core Style -->
    <link rel="stylesheet" href="{{ asset('style.css?v=1.0') }}">

    <!-- Font Icons -->
    <link rel="stylesheet" href="{{ asset('css/font-icons.css') }}">

    <!-- Plugins/Components CSS -->
    <link rel="stylesheet" href="{{ asset('css/swiper.css') }}">

    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<!-- Body-->
<body class="stretched">

<div id="agapp">
    <div id="wrapper">
        @include('front.layouts.widgets.header')
        @yield('content')
        @include('front.layouts.widgets.footer')
    </div>
</div>

<!-- JavaScripts
	============================================= -->
<script src="{{ asset('js/plugins.min.js') }}"></script>
<script src="{{ asset('js/functions.bundle.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js"></script>


<script>
    jQuery(window).on('load', function () {
        jQuery('.primary-menu').each(function () {
            let menuEl  = jQuery(this),
                marker  = menuEl.find('.morph-marker'),
                current = menuEl.find('.current');

            // Initialize the marker position and the active class
            current.addClass('active');

            marker.css({
                // Place the marker in the middle of the border
                bottom: -(marker.height() / 2),
                left:   current.position().left,
                width:  current.outerWidth(),
            });


            if (Modernizr.csstransitions) {
                // console.log("using css3 transitions");
                menuEl.find('.menu-item').mouseover(function () {
                    var self       = jQuery(this),
                        offsetLeft = self.position().left,

                        // Use the element under the pointer OR the current page item
                        width      = self.outerWidth() || current.outerWidth(),
                        // Ternary operator, because if using OR when offsetLeft is 0, it is considered a falsy value, thus causing a bug for the first element.
                        left       = offsetLeft == 0 ? 0 : offsetLeft || current.position().left;
                    // Play with the active class
                    menuEl.find('.active').removeClass('active');
                    self.addClass('active');
                    marker.css({
                        left:  left,
                        width: width,
                    });
                });

                // When the mouse leaves the menu
                menuEl.find('.menu-container').mouseleave(function () {
                    // remove all active classes, add active class to the current page item
                    menuEl.find('.active').removeClass('active');
                    current.addClass('active');
                    // reset the marker to the current page item position and width
                    marker.css({
                        left:  current.position().left,
                        width: current.outerWidth()
                    });
                });

            } else {

                menuEl.find('.menu-item').mouseover(function () {
                    var self       = jQuery(this),
                        offsetLeft = self.position().left,
                        // Use the element under the pointer OR the current page item
                        width      = self.outerWidth() || current.outerWidth(),
                        // Ternary operator, because if using OR when offsetLeft is 0, it is considered a falsy value, thus causing a bug for the first element.
                        left       = offsetLeft == 0 ? 0 : offsetLeft || current.position().left;
                    // Play with the active class
                    menuEl.find('.active').removeClass('active');
                    self.addClass('active');
                    marker.stop().animate({
                        left:  left,
                        width: width,
                    }, 300);
                });

                // When the mouse leaves the menu
                menuEl.find('.menu-container').mouseleave(function () {
                    // remove all active classes, add active class to the current page item
                    menuEl.find('.active').removeClass('active');
                    current.addClass('active');
                    // reset the marker to the current page item position and width
                    marker.stop().animate({
                        left:  current.position().left,
                        width: current.outerWidth()
                    }, 300);
                });
            }
        });
    });
</script>

@stack('js_after')

</body>
</html>
