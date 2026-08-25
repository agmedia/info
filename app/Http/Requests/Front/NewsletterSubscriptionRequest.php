<?php

namespace App\Http\Requests\Front;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class NewsletterSubscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email:rfc', 'max:191'],
            'website' => ['nullable', 'string', 'max:191'],
            'consent' => [
                Rule::excludeIf(fn (): bool => trim((string) $this->input('website', '')) !== ''),
                'required',
                'accepted',
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.required' => __('newsletter.validation.email_required'),
            'email.email' => __('newsletter.validation.email_invalid'),
            'email.max' => __('newsletter.validation.email_invalid'),
            'consent.required' => __('newsletter.validation.consent_required'),
            'consent.accepted' => __('newsletter.validation.consent_required'),
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => Str::lower(trim((string) $this->input('email', ''))),
        ]);
    }

    protected function failedValidation(Validator $validator): void
    {
        if (! $this->expectsJson()) {
            $this->session()->flash('newsletter_error', $validator->errors()->first());
        }

        parent::failedValidation($validator);
    }
}
