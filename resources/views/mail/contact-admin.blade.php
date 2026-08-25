<x-mail::message>
{{ __('mail.contact_admin.eyebrow') }}

# {{ __('mail.contact_admin.heading') }}

{{ __('mail.contact_admin.intro') }}

<x-mail::panel>
**{{ __('mail.contact_admin.labels.form_type') }}**<br>
{{ $formLabel }}

**{{ __('mail.contact_admin.labels.subject') }}**<br>
{!! $subjectHtml !!}
</x-mail::panel>

## {{ __('mail.contact_admin.contact_heading') }}

**{{ __('mail.contact_admin.labels.name') }}**<br>
{!! $nameHtml !!}

@if ($companyHtml)
**{{ __('mail.contact_admin.labels.company') }}**<br>
{!! $companyHtml !!}

@endif
**{{ __('mail.contact_admin.labels.email') }}**<br>
{!! $emailHtml !!}

**{{ __('mail.contact_admin.labels.phone') }}**<br>
{!! $phoneHtml !!}

## {{ __('mail.contact_admin.message_heading') }}

<x-mail::panel>
{!! $messageHtml !!}
</x-mail::panel>

## {{ __('mail.contact_admin.details_heading') }}

<x-mail::panel>
@if ($submittedAt)
**{{ __('mail.contact_admin.labels.submitted_at') }}**<br>
{{ $submittedAt }}

@endif
@if ($sourceLabelHtml)
**{{ __('mail.contact_admin.labels.source') }}**<br>
{!! $sourceLabelHtml !!}

@endif
**{{ __('mail.contact_admin.labels.locale') }}**<br>
{{ $localeLabel }}

@if ($reference)
**{{ __('mail.contact_admin.labels.reference') }}**<br>
{{ $reference }}
@endif
</x-mail::panel>

@if ($replyUrl)
<x-mail::button :url="$replyUrl" color="primary">
{{ __('mail.contact_admin.actions.reply') }}
</x-mail::button>
@endif

<x-mail::button :url="$adminUrl" color="secondary">
{{ __('mail.contact_admin.actions.open_admin') }}
</x-mail::button>

{{ __('mail.contact_admin.footer_note') }}
</x-mail::message>
