<?php

namespace Tests\Feature\Front;

use App\Models\Content\Page\InfoPage;
use App\Models\Content\Resource\ResourceDocument;
use App\Models\Content\Resource\ResourceDocumentTranslation;
use App\Models\Content\Support\ContactMessage;
use App\Services\Settings\SystemSettingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PublicFormsRecaptchaFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_public_submission_forms_render_recaptcha_when_it_is_configured(): void
    {
        $this->enableRecaptcha();
        $this->createResourceDocument();
        $this->enableCareerApplicationForm();

        $forms = [
            '/kontakt' => 'contact_form',
            '/karijera' => 'career_application_form',
            '/ac-forma-robot' => 'collaboration_assessment_form',
            '/eu-fondovi/upitnik' => 'eu_funds_questionnaire_form',
            '/resources/recaptcha-test-resource' => 'resource_download_request',
        ];

        foreach ($forms as $url => $action) {
            $content = (string) $this->get($url)
                ->assertOk()
                ->getContent();

            foreach ([
                'data-recaptcha-form',
                'data-recaptcha-site-key="public-recaptcha-site-key"',
                'data-recaptcha-action="'.$action.'"',
                'https://www.google.com/recaptcha/api.js?render=public-recaptcha-site-key',
                'front-recaptcha-disclosure',
                'https://policies.google.com/privacy',
                'https://policies.google.com/terms',
            ] as $expected) {
                $this->assertTrue(
                    str_contains($content, $expected),
                    $url.' is missing '.$expected,
                );
            }

            $this->assertFalse(
                str_contains($content, 'private-recaptcha-secret-key'),
                $url.' exposed the private reCAPTCHA secret',
            );
        }
    }

    public function test_all_public_submission_endpoints_require_recaptcha_token_when_it_is_configured(): void
    {
        $this->enableRecaptcha();
        $this->createResourceDocument();

        $endpoints = [
            '/kontakt',
            '/karijera/prijava',
            '/ac-forma-robot',
            '/eu-fondovi/upitnik',
            '/resources/recaptcha-test-resource/request',
        ];

        foreach ($endpoints as $endpoint) {
            $this->post($endpoint)->assertSessionHasErrors('recaptcha_token');
        }
    }

    public function test_career_page_does_not_load_recaptcha_when_the_application_form_is_hidden(): void
    {
        $this->enableRecaptcha();
        $this->disableCareerApplicationForm();

        $content = (string) $this->get('/karijera')
            ->assertOk()
            ->getContent();

        $this->assertStringNotContainsString('data-recaptcha-form', $content);
        $this->assertStringNotContainsString('https://www.google.com/recaptcha/api.js', $content);
        $this->assertStringNotContainsString('front-recaptcha-disclosure', $content);
    }

    public function test_recaptcha_response_must_match_the_expected_form_action(): void
    {
        $this->enableRecaptcha();

        Http::fake([
            'www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => true,
                'score' => 0.99,
                'action' => 'different_form_action',
            ]),
        ]);

        $this->post('/kontakt', [
            'name' => 'Sigurnosni test',
            'email' => 'recaptcha@example.test',
            'message' => 'Provjera da token iz druge forme nije prihvaćen.',
            'accept_terms' => '1',
            'recaptcha_token' => 'valid-looking-token',
        ])->assertSessionHasErrors('recaptcha_token');

        $this->assertDatabaseMissing((new ContactMessage)->getTable(), [
            'email' => 'recaptcha@example.test',
        ]);
    }

    private function enableRecaptcha(): void
    {
        app(SystemSettingsService::class)->putMany([
            'store_captcha_recaptcha_v3_enabled' => true,
            'store_captcha_recaptcha_v3_site_key' => 'public-recaptcha-site-key',
            'store_captcha_recaptcha_v3_secret_key' => 'private-recaptcha-secret-key',
            'store_captcha_recaptcha_v3_min_score' => 0.7,
        ]);
    }

    private function createResourceDocument(): void
    {
        $document = ResourceDocument::query()->create([
            'code' => 'recaptcha-test-resource',
            'group_code' => 'downloads',
            'is_active' => true,
            'sort_order' => 1,
            'download_url' => 'https://example.test/resource.pdf',
        ]);

        ResourceDocumentTranslation::query()->create([
            'document_id' => $document->id,
            'locale' => 'hr',
            'title' => 'reCAPTCHA test resource',
            'slug' => 'recaptcha-test-resource',
            'excerpt' => 'Testni dokument za provjeru zaštite javne forme.',
        ]);
    }

    private function enableCareerApplicationForm(): void
    {
        InfoPage::query()
            ->where('code', 'career')
            ->firstOrFail()
            ->translation('hr')
            ->firstOrFail()
            ->update([
                'payload' => [
                    'career_page' => [
                        'application' => [
                            'title' => 'CMS test application',
                        ],
                        'form' => [
                            'title' => 'CMS test application form',
                            'intro' => 'CMS test form introduction.',
                        ],
                    ],
                ],
            ]);
    }

    private function disableCareerApplicationForm(): void
    {
        InfoPage::query()
            ->where('code', 'career')
            ->firstOrFail()
            ->translation('hr')
            ->firstOrFail()
            ->update([
                'payload' => [
                    'career_page' => [
                        'application' => [],
                        'form' => [],
                    ],
                ],
            ]);
    }
}
