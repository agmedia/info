@extends('front.layouts.app')

@section('content')

    <section id="slider" class="slider-element" style="background-image: url('{{ asset('img/banners/banner1.jpg') }}'); background-size: cover; background-position: center center;">
        <div class="container">
            <div class="row h-100 py-3">
                <div class="col-lg-6 d-flex align-self-center flex-column pt-5 pb-0 py-lg-6 mb-0 my-lg-4">
                    <h2 class="display-4 dark" style="font-weight: 800">Kontakt SK Koros</h2>
                    <div>
                        <a href="#" class="button button-large border border-width-2 bg-alt py-2 rounded-1 fw-medium text-transform-none ls-0 ms-0 ms-sm-1 h-op-09">
                            <i class="bi-file-earmark-plus"></i>Prijavi se u Klub
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 d-flex align-self-end flex-column">
                </div>
            </div>
        </div>
    </section>



    <!-- Contact -->
    <section id="content">
        <div class="content-wrap">
            <div class="container">
                <div class="row gx-5 col-mb-80">
                    <main class="col-lg-9">
                        <h3>Pošaljite nam upit</h3>
                        <form class="mb-0" action="{{ route('poruka') }}" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-sm-12 mb-4">
                                    <label class="form-label" for="cf-name">Vaše ime:&nbsp;@include('back.layouts.partials.required-star')</label>
                                    <input class="form-control" type="text" name="name" id="cf-name" placeholder="">
                                    @error('name')<div class="text-danger font-size-sm">Molimo upišite vaše ime!</div>@enderror
                                </div>
                                <div class="col-sm-6 mb-4">
                                    <label class="form-label" for="cf-email">Email adresa:&nbsp;@include('back.layouts.partials.required-star')</label>
                                    <input class="form-control" type="email" id="cf-email" placeholder="" name="email">
                                    @error('email')<div class="text-danger font-size-sm">Molimo upišite ispravno email adresu!</div>@enderror
                                </div>
                                <div class="col-sm-6 mb-4">
                                    <label class="form-label" for="cf-phone">Broj telefona:&nbsp;@include('back.layouts.partials.required-star')</label>
                                    <input class="form-control" type="text" id="cf-phone" placeholder="" name="phone">
                                    @error('phone')<div class="text-danger font-size-sm">Molimo upišite broj telefona!</div>@enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label" for="cf-message">Upit:&nbsp;@include('back.layouts.partials.required-star')</label>
                                    <textarea class="form-control" id="cf-message" rows="6" placeholder="" name="message"></textarea>
                                    @error('message')<div class="text-danger font-size-sm">Molimo upišite poruku!</div>@enderror
                                    <button class="btn btn-primary mt-4" type="submit">Pošaljite upit </button>
                                </div>
                            </div>
                            <input type="hidden" name="recaptcha" id="recaptcha">
                        </form>
                    </main>

                    <!-- Sidebar -->
                    <aside class="sidebar col-lg-3">
                        <address>
                            <strong>Sjedište:</strong><br>
                            795 Folsom Ave, Suite 600<br>
                            San Francisco<br>
                            CA 94107<br>
                        </address>
                        <abbr title="Phone Number"><strong>Tel:</strong></abbr> (1) 8547 632521<br>
                        <abbr title="Email Address"><strong>Email:</strong></abbr> info@sk-koros.hr

                        <div class="widget border-0 pt-0">
                            <a href="#" class="social-icon si-small bg-dark h-bg-facebook">
                                <i class="fa-brands fa-facebook-f"></i>
                                <i class="fa-brands fa-facebook-f"></i>
                            </a>
                        </div>
                    </aside>
                </div>

            </div>
        </div>
    </section>

@endsection

@push('js_after')
    @include('front.layouts.partials.recaptcha-js')
@endpush
