<section id="content">
    <div class="content-wrap pt-5 pt-lg-6" style="background: linear-gradient(to bottom, #F8F9FA 720px, var(--cnvs-contrast-0) 720px);">
        <div class="container mw-lg">

            <div class="row justify-content-between col-mb-30">
                <div class="col-lg-5">
                    <h1 class="mb-3 display-1 mb-xl-5" data-animate="fadeInDownSmall">Contact us</h1>
                    <p class="text-larger" data-animate="fadeInUpSmall">Lorem ipsum dolor sit amet consectetur adipisicing elit. Commodi similique autem rerum corporis totam consectetur beatae maiores obcaecati harum eligendi.</p>

                    <div data-animate="fadeInUpSmall">
                        <p class="d-flex mb-3" ><i class="me-3 mt-1 fa-solid fa-phone"></i> <a href="#" class="text-larger text-contrast-1000 fw-medium"> 00353838731575</a></p>
                        <p class="d-flex mb-3"><i class="me-3 mt-1 fa-solid fa-envelope"></i> <a href="#" class="text-larger text-contrast-1000 fw-medium">Info@focusbranding.ie</a></p>
                        <p class="d-flex mb-4"><i class="me-3 mt-1 fa-solid fa-location-dot"></i> <a href="#" class="text-larger text-contrast-1000 fw-medium">659 Leonard C Taylor Pkwy<br>Green Cove, State 32043</a></p>
                    </div>
                </div>

                <div class="col-lg-6" data-animate="fadeInRightSmall" data-delay="150">
                    <div class="card bg-contrast-0 border-0 shadow">
                        <div class="card-body p-5">

                            <div class="form-widget">

                                <div class="form-result">@include('front.layouts.partials.session')</div>

                                <form class="mb-0" action="{{ route('poruka') }}" method="post">
                                    {{ csrf_field() }}
                                    <div class="form-process">
                                        <div class="css3-spinner">
                                            <div class="css3-spinner-scaler"></div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12 form-group">
                                            <label for="template-contactform-name">Name <small>*</small></label>
                                            <input type="text" id="template-contactform-name" name="template-contactform-name" value="" class="form-control required">
                                        </div>

                                        <div class="col-12 form-group">
                                            <label for="template-contactform-email">Email <small>*</small></label>
                                            <input type="email" id="template-contactform-email" name="template-contactform-email" value="" class="required email form-control">
                                        </div>

                                        <div class="col-12 form-group">
                                            <label for="template-contactform-phone">Phone <small>*</small></label>
                                            <input type="text" id="template-contactform-phone" name="template-contactform-phone" value="" class="form-control required">
                                        </div>

                                        <div class="col-12 form-group">
                                            <label for="template-contactform-company">Company</label>
                                            <input type="text" id="template-contactform-company" name="company" value="" class="form-control">
                                        </div>

                                        <div class="col-12 form-group">
                                            <label for="template-contactform-subject">Subjects</label>
                                            <select id="template-contactform-subject" name="template-contactform-subject" class="form-select">
                                                <option value="" disabled selected>Select One</option>
                                                <option value="Signage">Signage</option>
                                                <option value="Wall graphics">Wall graphics</option>
                                                <option value="Glass graphics">Glass graphics</option>
                                                <option value="Acoustics">Acoustics</option>
                                                <option value="Other">Other</option>
                                            </select>
                                        </div>

                                        <div class="w-100"></div>

                                        <div class="col-12 form-group">
                                            <label for="template-contactform-message">Message</label>
                                            <textarea class="form-control" id="template-contactform-message" name="template-contactform-message" rows="5" cols="30"></textarea>
                                        </div>

                                        <div class="col-12 form-group d-none">
                                            <input type="text" id="template-contactform-botcheck" name="template-contactform-botcheck" value="" class="form-control">
                                        </div>

                                        <div class="col-12">
                                            <button class="button button-large button-dark rounded m-0 mt-5" type="submit" id="template-contactform-submit" name="template-contactform-submit" value="submit">Submit Now</button>
                                        </div>
                                    </div>

                                    <input type="hidden" name="prefix" value="template-contactform-">

                                </form>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
</section>