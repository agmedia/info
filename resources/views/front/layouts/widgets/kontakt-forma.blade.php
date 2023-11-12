<div class="form-widget">
    <div class="form-result"></div>
    <form class="nobottommargin" action="{{ route('poruka') }}" method="post">
        @csrf
        <div class="form-process"></div>

        <div class="row">
            <div class="col-md-4 form-group">
                <label for="contactform-name">Ime <span style="color: red;">*</span></label>
                <input type="text" name="name" value="" class="form-control required" />
            </div>
            <div class="col-md-4 form-group">
                <label for="contactform-email">Email <span style="color: red;">*</span></label>
                <input type="email" name="email" value="" class="required email form-control" />
            </div>
            <div class="col-md-4 form-group">
                <label for="contactform-subject">Telefon</label>
                <input type="text" name="phone" value="" class="form-control" />
            </div>
            <div class="clear"></div>
            <div class="col-md-12">
                <label for="contactform-message_content">Upit <span style="color: red;">*</span></label>
                <textarea class="required form-control" name="message" rows="6" cols="30"></textarea>
            </div>
            <div class="col-md-12 hidden">

            </div>
            <div class="col-md-12">
                <button class="button button-3d m-0" type="submit">Pošaljite upit</button>
            </div>
        </div>

        <input type="hidden" name="recaptcha" id="recaptcha">
    </form>
</div>
