<?php

namespace Tests\Feature\Front;

use App\Models\Content\Career\JobOpening;
use App\Models\Content\Page\InfoPage;
use App\Models\Settings\Local\Language;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CareerJobOpeningsFeatureTest extends TestCase
{
    use RefreshDatabase;

    private const SLUG = 'racunovoda-asistent-u-racunovodstvu';

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('app.locale', 'hr');
        config()->set('app.fallback_locale', 'hr');
        $this->travelTo(CarbonImmutable::create(2026, 9, 2, 10, 0, 0, 'Europe/Zagreb'));

        Language::query()->updateOrCreate(['code' => 'hr'], [
            'locale' => 'hr_HR',
            'name' => 'Croatian',
            'native_name' => 'Hrvatski',
            'is_default' => true,
            'is_active' => true,
            'sort_order' => 1,
        ]);
        Language::query()->updateOrCreate(['code' => 'en'], [
            'locale' => 'en_GB',
            'name' => 'English',
            'native_name' => 'English',
            'is_default' => false,
            'is_active' => true,
            'sort_order' => 2,
        ]);
    }

    public function test_career_page_lists_the_published_opening_with_locations_date_and_link(): void
    {
        $url = route('career.openings.show', ['slug' => self::SLUG]);

        $this->get('/karijera')
            ->assertOk()
            ->assertSee('Računovođa / Asistent u računovodstvu (m/ž)')
            ->assertSee('Zagreb | Rijeka | Vinkovci')
            ->assertSee('class="ac-career-job-list"', false)
            ->assertSee('href="'.$url.'"', false)
            ->assertSee('datetime="2026-09-02"', false)
            ->assertSee('2. rujna 2026.');
    }

    public function test_opening_detail_renders_full_copy_seo_breadcrumbs_and_job_posting_schema(): void
    {
        $response = $this->get(route('career.openings.show', ['slug' => self::SLUG]))
            ->assertOk()
            ->assertSee('Računovođa / Asistent u računovodstvu (m/ž)')
            ->assertSee('Kako izgleda računovodstvo kada spojiš znanje, dobar tim i tehnologiju')
            ->assertSee('Što ćeš raditi?')
            ->assertSee('knjiženje i kontrola poslovne dokumentacije')
            ->assertSee('Kako izgleda rad kod nas?')
            ->assertSee('Normalno radno vrijeme')
            ->assertSee('Što ti nudimo?')
            ->assertSee('mentorstvo i podršku iskusnog tima')
            ->assertSee('href="mailto:hr@alphacapitalis.com"', false)
            ->assertSee('front-theme/styles/pages/career-opening.css', false)
            ->assertSee('<meta name="description" content="Otvorena pozicija', false)
            ->assertSee('href="'.route('pages.show', ['slug' => 'karijera']).'"', false);

        preg_match_all(
            '#<script type="application/ld\+json">(.*?)</script>#s',
            $response->getContent(),
            $schemaMatches,
        );
        $jobPosting = collect($schemaMatches[1] ?? [])
            ->map(static fn (string $json): mixed => json_decode($json, true))
            ->firstWhere('@type', 'JobPosting');

        $this->assertIsArray($jobPosting);
        $this->assertSame('Računovođa / Asistent u računovodstvu (m/ž)', $jobPosting['title']);
        $this->assertSame('2026-09-02', $jobPosting['datePosted']);
        $this->assertStringContainsString('<h2>Što ćeš raditi?</h2>', $jobPosting['description']);
        $this->assertStringContainsString('mentorstvo i podršku iskusnog tima', $jobPosting['description']);
        $this->assertSame(
            ['Zagreb', 'Rijeka', 'Vinkovci'],
            collect($jobPosting['jobLocation'])->pluck('address.addressLocality')->all(),
        );
        $this->assertSame(url('/').'#organization', data_get($jobPosting, 'hiringOrganization.@id'));
    }

    public function test_inactive_and_future_openings_are_not_publicly_visible(): void
    {
        $opening = JobOpening::query()->where('code', self::SLUG)->firstOrFail();
        $url = route('career.openings.show', ['slug' => self::SLUG]);

        $opening->update(['published_at' => now()->addDay()]);

        $this->get('/karijera')->assertOk()->assertDontSee('href="'.$url.'"', false);
        $this->get($url)->assertNotFound();

        $opening->update([
            'is_active' => false,
            'published_at' => now()->subDay(),
        ]);

        $this->get('/karijera')->assertOk()->assertDontSee('href="'.$url.'"', false);
        $this->get($url)->assertNotFound();
    }

    public function test_english_routes_do_not_fall_back_to_the_croatian_job_translation(): void
    {
        $careerPage = InfoPage::query()->where('code', 'career')->firstOrFail();
        $careerPage->update(['is_active' => true, 'published_at' => now()->subDay()]);
        $careerPage->translations()->updateOrCreate(['locale' => 'en'], [
            'title' => 'Careers',
            'slug' => 'careers',
            'payload' => ['career_page' => ['intro' => ['title' => 'Careers']]],
        ]);

        $this->get('/careers')
            ->assertOk()
            ->assertDontSee('Računovođa / Asistent u računovodstvu (m/ž)');

        $this->get(route('career.openings.show.en', ['slug' => self::SLUG]))
            ->assertNotFound();

        $englishCareerUrl = route('pages.show', ['slug' => 'careers']);
        $englishSwitchUrl = route('front.locale.switch', [
            'code' => 'en',
            'redirect' => $englishCareerUrl,
        ]);

        $this->get(route('career.openings.show', ['slug' => self::SLUG]))
            ->assertOk()
            ->assertSee('href="'.e($englishSwitchUrl).'"', false);
    }

    public function test_detail_links_follow_the_current_career_page_slug(): void
    {
        $careerPage = InfoPage::query()->where('code', 'career')->firstOrFail();
        $careerPage->translation('hr')->firstOrFail()->update(['slug' => 'rad-s-nama']);
        $careerUrl = route('pages.show', ['slug' => 'rad-s-nama']);

        $response = $this->get(route('career.openings.show', ['slug' => self::SLUG]))
            ->assertOk()
            ->assertSee('href="'.$careerUrl.'"', false)
            ->assertSee('href="'.$careerUrl.'#career-open-positions"', false);

        preg_match_all(
            '#<script type="application/ld\+json">(.*?)</script>#s',
            $response->getContent(),
            $schemaMatches,
        );
        $breadcrumbs = collect($schemaMatches[1] ?? [])
            ->map(static fn (string $json): mixed => json_decode($json, true))
            ->firstWhere('@type', 'BreadcrumbList');

        $this->assertIsArray($breadcrumbs);
        $this->assertSame($careerUrl, data_get($breadcrumbs, 'itemListElement.1.item'));
    }

    public function test_an_exact_english_translation_is_listed_and_has_its_own_detail_url(): void
    {
        $careerPage = InfoPage::query()->where('code', 'career')->firstOrFail();
        $careerPage->update(['is_active' => true, 'published_at' => now()->subDay()]);
        $careerPage->translations()->updateOrCreate(['locale' => 'en'], [
            'title' => 'Careers',
            'slug' => 'careers',
            'payload' => ['career_page' => ['application' => ['title' => 'Open positions']]],
        ]);

        $opening = JobOpening::query()->where('code', self::SLUG)->firstOrFail();
        $opening->translations()->create([
            'locale' => 'en',
            'title' => 'Accountant / Accounting Assistant (m/f)',
            'slug' => 'accountant-accounting-assistant',
            'locations' => 'Zagreb | Rijeka | Vinkovci',
            'excerpt' => 'Join our accounting team.',
            'body_html' => '<p>Build your career in modern accounting.</p>',
        ]);
        $detailUrl = route('career.openings.show.en', ['slug' => 'accountant-accounting-assistant']);

        $this->get('/careers')
            ->assertOk()
            ->assertSee('Accountant / Accounting Assistant (m/f)')
            ->assertSee('href="'.$detailUrl.'"', false)
            ->assertSee('September 2, 2026');

        $this->get($detailUrl)
            ->assertOk()
            ->assertSee('Accountant / Accounting Assistant (m/f)')
            ->assertSee('Build your career in modern accounting.');
    }

    public function test_job_detail_and_sitemap_require_an_exact_published_career_parent(): void
    {
        InfoPage::query()
            ->where('code', 'career')
            ->firstOrFail()
            ->translations()
            ->where('locale', 'en')
            ->delete();

        $opening = JobOpening::query()->where('code', self::SLUG)->firstOrFail();
        $opening->translations()->create([
            'locale' => 'en',
            'title' => 'Accountant / Accounting Assistant (m/f)',
            'slug' => 'accountant-accounting-assistant',
            'locations' => 'Zagreb | Rijeka | Vinkovci',
            'excerpt' => 'Join our accounting team.',
            'body_html' => '<p>Build your career in modern accounting.</p>',
        ]);

        $detailUrl = route('career.openings.show.en', ['slug' => 'accountant-accounting-assistant']);

        $this->get($detailUrl)->assertNotFound();
        $this->get(route('sitemap'))->assertOk()->assertDontSee($detailUrl);
    }

    public function test_career_links_keep_their_physical_language_route_when_english_is_default(): void
    {
        Language::query()->where('code', 'hr')->update([
            'is_default' => false,
            'sort_order' => 2,
        ]);
        Language::query()->where('code', 'en')->update([
            'is_default' => true,
            'sort_order' => 1,
        ]);

        $careerPage = InfoPage::query()->where('code', 'career')->firstOrFail();
        $careerPage->update(['is_active' => true, 'published_at' => now()->subDay()]);
        $careerPage->translations()->updateOrCreate(['locale' => 'en'], [
            'title' => 'Careers',
            'slug' => 'careers',
            'payload' => ['career_page' => ['application' => ['title' => 'Open positions']]],
        ]);

        $opening = JobOpening::query()->where('code', self::SLUG)->firstOrFail();
        $opening->translations()->create([
            'locale' => 'en',
            'title' => 'Accountant / Accounting Assistant (m/f)',
            'slug' => 'accountant-accounting-assistant',
            'locations' => 'Zagreb | Rijeka | Vinkovci',
            'excerpt' => 'Join our accounting team.',
            'body_html' => '<p>Build your career in modern accounting.</p>',
        ]);

        $detailUrl = route('career.openings.show.en', ['slug' => 'accountant-accounting-assistant']);

        $this->get('/careers')
            ->assertOk()
            ->assertSee('href="'.$detailUrl.'"', false)
            ->assertSee('action="'.route('career.applications.store.en').'"', false)
            ->assertDontSee('href="'.route('career.openings.show', ['slug' => 'accountant-accounting-assistant']).'"', false);

        $this->get($detailUrl)
            ->assertOk()
            ->assertSee('Accountant / Accounting Assistant (m/f)');
    }
}
