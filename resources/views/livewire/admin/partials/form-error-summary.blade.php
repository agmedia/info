@if ($errors->any())
    <div class="admin-form-error-summary" role="alert" aria-live="polite" data-admin-error-summary>
        <div class="admin-form-error-summary__icon" aria-hidden="true">
            <i class="fa-light fa-triangle-exclamation"></i>
        </div>
        <div>
            <p class="admin-form-error-summary__title">{{ __('Neke promjene nije moguće spremiti') }}</p>
            <p class="admin-form-error-summary__intro">{{ __('Provjerite označena polja i pokušajte ponovno.') }}</p>
            <ul class="admin-form-error-summary__list">
                @foreach (array_slice(array_values(array_unique($errors->all())), 0, 6) as $message)
                    <li>{{ $message }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
