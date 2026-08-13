@if ($captchaEnabled)
    @push('scripts')
        <script src="https://www.google.com/recaptcha/api.js?render={{ $captchaSiteKey }}"></script>
    @endpush
@endif

@push('scripts')
    <script defer src="{{ asset('front-theme/scripts/contact-form.js') }}?v={{ filemtime(public_path('front-theme/scripts/contact-form.js')) }}"></script>
@endpush
