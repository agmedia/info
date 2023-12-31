<header id="header" class="full-header {{ request()->is(['/', 'info/what-we-offer']) ? 'transparent-header' : '' }} dark header-size-lg" data-bs-theme="dark"
        data-sticky-shrink-offset="1">

    <div id="header-wrap">
        <div class="container">
            <div class="header-row">

                <!-- Logo -->
                <div id="logo">
                    <a href="{{ route('index') }}">
                        @if (request()->is(['/']))
                            <img class="logo-default" 
                                 src="{{ asset('img/logo/logo.svg') }}" alt="Focus branding logo">
                        @else
                            <img class="logo-default"  src="{{ asset('img/logo/logo-dark.svg') }}" alt="Focus branding logo">
                        @endif
                        <img class="logo-sticky"
                             
                             src="{{ asset('img/logo/logo-dark-sticky.svg') }}" alt="Focus branding logo">
                        <img class="logo-mobile" 
                             src="{{ asset('img/logo/logo-dark.svg') }}" alt="Focus branding logo">
                    </a>
                </div><!-- #logo end -->


                <div class="primary-menu-trigger">
                    <button class="cnvs-hamburger" type="button" title="Open Mobile Menu">
                        <span class="cnvs-hamburger-box"><span class="cnvs-hamburger-inner"></span></span>
                    </button>
                </div>

                <!-- Primary Navigation -->
                <nav class="primary-menu">

                    <ul class="menu-container">
                        <li class="menu-item {{ request()->is(['/']) ? 'current' : '' }}">
                            <a class="menu-link" href="{{ route('index') }}">
                                <div>Home</div>
                            </a>
                        </li>
                        <li class="menu-item {{ request()->is(['info/what-we-offer']) ? 'current' : '' }}">
                            <a class="menu-link" href="{{ route('front.page', ['page' => 'what-we-offer']) }}">
                                <div>What we offer</div>
                            </a>
                        </li>
                        <li class="menu-item {{ request()->is(['info/who-are-we']) ? 'current' : '' }}">
                            <a class="menu-link" href="{{ route('front.page', ['page' => 'who-are-we']) }}">
                                <div>Who are we</div>
                            </a>
                        </li>
                        <li class="menu-item {{ request()->is(['/contact-us']) ? 'current' : '' }}">
                            <a class="menu-link" href="{{ route('front.contact') }}">
                                <div>Contact us</div>
                            </a>
                        </li>
                        <div class="morph-marker"></div>
                    </ul>

                </nav><!-- #primary-menu end -->

                <form class="top-search-form" action="search.html" method="get">
                    <input type="text" name="q" class="form-control" value=""
                           placeholder="Type &amp; Hit Enter.." autocomplete="off">
                </form>

            </div>
        </div>
    </div>
    <div class="header-wrap-clone"></div>
</header>