<!-- Footer -->
<footer id="footer" class="dark">
    <div class="container">
        <!-- Footer Widgets -->
        <div class="footer-widgets-wrap">
            <div class="row col-mb-50">
                <div class="col-lg-8">
                    <div class="row col-mb-50">
                        <div class="col-md-6">
                            <div class="widget">
                                <h2 class="mb-3"><span>SK</span>Koros</h2>
                                <p>Vjerujemo u <strong>Jednostavan</strong>, <strong>Kreativan</strong> &amp; <strong>Fleksibilni</strong> Režim Treninga.</p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="widget widget_links">
                                <div class="row">
                                    @if (isset($categories) && $categories['docs'])
                                        <div class="col-md-6">
                                            <h4 class="mb-2">{{ $categories['docs'][0]['subgroup'] }}</h4>
                                            <ul>
                                                @foreach ($categories['docs'] as $cat)
                                                    <li><a href="{{ route('front.page', ['page' => $cat['slug']]) }}">{{ $cat['title'] }}</a></li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    @if (isset($categories) && $categories['links'])
                                        <div class="col-md-6">
                                            <h4 class="mb-2">{{ $categories['links'][0]['subgroup'] }}</h4>
                                            <ul>
                                                @foreach ($categories['links'] as $cat)
                                                    <li><a href="{{ route('front.page', ['page' => $cat['slug']]) }}">{{ $cat['title'] }}</a></li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="row col-mb-50">
                        <div class="col-md-5 col-lg-12">
                            <div class="widget subscribe-widget">
                                <h5><strong>Prijavi se</strong> na newsletter našeg kluba i doznaj odmah sve novosti i događanja...</h5>
                                <div class="widget-subscribe-form-result"></div>
                                <form id="widget-subscribe-form" action="include/subscribe.php" method="post" class="mt-4 mb-0">
                                    <div class="input-group mx-auto">
                                        <div class="input-group-text"><i class="bi-envelope-plus"></i></div>
                                        <input type="email" id="widget-subscribe-form-email" name="widget-subscribe-form-email" class="form-control required email" placeholder="Unesi svoj Email">
                                        <button class="btn btn-success" type="submit">Prijavi se</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Copyrights -->
    <div id="copyrights">
        <div class="container">
            <div class="row col-mb-30">
                <div class="col-md-6 text-center text-md-start">
                    SK Koros © Sva prava pridržana. Web by <a class="text-light" href="https://www.agmedia.hr" target="_blank" rel="noopener">AG media</a><br>
                    <div class="copyright-links">
                        @foreach ($categories['gdpr'] as $cat)
                            <a href="{{ route('front.page', ['page' => $cat['slug']]) }}">{{ $cat['title'] }}</a>
                            @if ( ! $loop->last) / @endif
                        @endforeach
                    </div>
                </div>

                <div class="col-md-6 text-center text-md-end">
                    <div class="d-flex justify-content-center justify-content-md-end mb-2">
                        <a href="#" class="social-icon border-transparent si-small h-bg-facebook">
                            <i class="fa-brands fa-facebook-f"></i>
                            <i class="fa-brands fa-facebook-f"></i>
                        </a>
                    </div>
                    <i class="bi-envelope"></i> info@sk-koros.com <span class="middot">&middot;</span> <i class="fa-solid fa-phone"></i> +1-11-6541-6369 <span class="middot">&middot;</span> <i class="bi-skype"></i> SK Koros
                </div>
            </div>
        </div>
    </div>
</footer>