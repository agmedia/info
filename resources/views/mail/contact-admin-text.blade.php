{{ __('mail.contact_admin.eyebrow') }}

{{ __('mail.contact_admin.heading') }}
{{ __('mail.contact_admin.intro') }}

{{ __('mail.contact_admin.labels.form_type') }}: {{ $formLabel }}
{{ __('mail.contact_admin.labels.subject') }}: {!! $subjectText !!}

{{ __('mail.contact_admin.contact_heading') }}
{{ __('mail.contact_admin.labels.name') }}: {!! $nameText !!}
@if ($companyText)
{{ __('mail.contact_admin.labels.company') }}: {!! $companyText !!}
@endif
{{ __('mail.contact_admin.labels.email') }}: {!! $emailText !!}
{{ __('mail.contact_admin.labels.phone') }}: {!! $phoneText !!}

{{ __('mail.contact_admin.message_heading') }}
{!! $messageText !!}

{{ __('mail.contact_admin.details_heading') }}
@if ($submittedAt)
{{ __('mail.contact_admin.labels.submitted_at') }}: {{ $submittedAt }}
@endif
@if ($sourceLabelText)
{{ __('mail.contact_admin.labels.source') }}: {!! $sourceLabelText !!}
@endif
{{ __('mail.contact_admin.labels.locale') }}: {{ $localeLabel }}
@if ($reference)
{{ __('mail.contact_admin.labels.reference') }}: {{ $reference }}
@endif

@if ($replyUrl)
{{ __('mail.contact_admin.actions.reply') }}: {{ $replyUrl }}
@endif
{{ __('mail.contact_admin.actions.open_admin') }}: {{ $adminUrl }}

{{ __('mail.contact_admin.footer_note') }}
