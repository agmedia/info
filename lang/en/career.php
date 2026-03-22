<?php

return [
    'sent_status' => 'Thanks. Your application has been sent successfully.',
    'captcha_failed' => 'Security verification failed. Please try again.',
    'validation' => [
        'required' => 'The :attribute field is required.',
        'email' => 'The :attribute field must be a valid email address.',
        'accepted' => 'You must accept :attribute.',
        'mimes' => 'The CV must be a PDF, DOC, or DOCX file.',
        'max_file' => 'The CV may not be greater than 5 MB.',
        'max_string' => 'The :attribute field may not be greater than :max characters.',
        'security_check' => 'security check',
        'inline' => [
            'first_name_required' => 'Please enter your first name.',
            'last_name_required' => 'Please enter your last name.',
            'email_required' => 'Please enter your email address.',
            'email_invalid' => 'Please enter a valid email address.',
            'cv_required' => 'Please upload your CV.',
            'accept_terms' => 'You must accept data processing consent.',
        ],
    ],
    'form' => [
        'eyebrow' => 'Application',
        'title' => 'Send us your CV',
        'intro' => 'Fill in your basic details and upload your resume so we can contact you when your profile matches an open position.',
        'first_name' => 'First name',
        'last_name' => 'Last name',
        'email' => 'Email',
        'message' => 'Message (optional)',
        'cv' => 'CV upload',
        'cv_help' => 'Supported formats: PDF, DOC, and DOCX. Maximum file size is 5 MB.',
        'accept_terms' => 'I agree to the processing of personal data for recruitment purposes.',
        'submit' => 'Send application',
    ],
];
