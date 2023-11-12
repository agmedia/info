<!-- Header ============================================= -->
<header id="header" class="full-header transparent-header">
    <div id="header-wrap">
        <div class="container">
            <div class="header-row">
                <!-- Logo -->
                <div id="logo">
                    <a href="{{ route('index') }}">
                        <img class="logo-default" srcset="{{ asset('media/logo.png') }}, {{ asset('media/logo@2x.png') }}" src="{{ asset('media/logo@2x.png') }}" alt="{{ env('APP_NAME') }} Logo">
                        <img class="logo-dark" srcset="{{ asset('media/logo-dark.png') }}, {{ asset('media/logo-dark@2x.png') }}" src="{{ asset('media/logo-dark@2x.png') }}" alt="{{ env('APP_NAME') }} Logo">
                    </a>
                </div>
                <!-- Top Icons -->
                <div class="header-misc">
                    <div id="top-search" class="header-misc-icon">
                        <a href="#" id="top-search-trigger"><i class="uil uil-search"></i><i class="bi-x-lg"></i></a>
                    </div>
                    <div id="top-search" class="header-misc-icon">
                        <button type="button" class="btn body-scheme-toggle" data-bodyclass-toggle="dark" data-add-html="<i class='uil uil-sun'></i><span class='visually-hidden'>Light Mode</span>" data-remove-html="<i class='uil uil-moon'></i><span class='visually-hidden'>Dark Mode</span>"><span class="visually-hidden">Dark Toggle</span></button>
                    </div>
                </div>

                <div class="primary-menu-trigger">
                    <button class="cnvs-hamburger" type="button" title="Open Mobile Menu">
                        <span class="cnvs-hamburger-box"><span class="cnvs-hamburger-inner"></span></span>
                    </button>
                </div>

                <!-- Navigation -->
                <nav class="primary-menu">
                    <ul class="menu-container">
                        <li class="menu-item">
                            <a class="menu-link" href="{{ route('index') }}"><div>Home</div></a>
                        </li>
                        <li class="menu-item">
                            <a class="menu-link" href="{{ route('catalog.route.blog') }}"><div>Novosti</div></a>
                        </li>
                        <li class="menu-item">
                            <a class="menu-link" href="{{ route('catalog.route.galleries') }}"><div>Galerije</div></a>
                        </li>
                        <li class="menu-item">
                            <a class="menu-link" href="javascript:void(0)"><div>Info</div></a>
                            <ul class="sub-menu-container">
                                @if (isset($categories) && $categories['info'])
                                    @foreach ($categories['info'] as $cat)
                                        <li class="menu-item">
                                            <a class="menu-link" href="{{ route('front.page', ['page' => $cat['slug']]) }}"><div>{{ $cat['title'] }}</div></a>
                                        </li>
                                    @endforeach
                                @endif
                            </ul>
                        </li>
                        <li class="menu-item mega-menu">
                            <a class="menu-link" href="{{ route('front.page', ['page' => 'kontakt']) }}"><div>Kontakt</div></a>
                        </li>
                    </ul>
                </nav>

                <form class="top-search-form" action="search.html" method="get">
                    <input type="text" name="q" class="form-control" value="" placeholder="Utipkaj &amp; Pritisni Enter.." autocomplete="off">
                </form>
            </div>
        </div>
    </div>
    <div class="header-wrap-clone"></div>
</header>