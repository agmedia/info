<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        $pageId = DB::table('content_info_pages')
            ->where('code', 'career')
            ->value('id');

        if (! $pageId) {
            $pageId = DB::table('content_info_page_translations')
                ->where('locale', 'hr')
                ->where('slug', 'karijera')
                ->value('page_id');
        }

        $pageData = [
            'code' => 'career',
            'layout' => 'career',
            'is_active' => true,
            'show_in_footer' => false,
            'published_at' => $now,
            'sort_order' => 25,
            'payload' => null,
            'updated_by' => null,
            'updated_at' => $now,
        ];

        if ($pageId) {
            DB::table('content_info_pages')
                ->where('id', $pageId)
                ->update($pageData);
        } else {
            $pageId = DB::table('content_info_pages')->insertGetId($pageData + [
                'created_by' => null,
                'created_at' => $now,
            ]);
        }

        $this->upsertTranslation(
            pageId: (int) $pageId,
            locale: 'hr',
            title: 'Karijera',
            slug: 'karijera',
            excerpt: 'Regrutacija, zapošljavanje i usmjeravanje zaposlenika.',
            bodyHtml: $this->careerHtmlHr(),
            metaTitle: 'Karijera | Alpha Capitalis',
            metaDescription: 'Karijera u Alpha Capitalisu: upoznajte naš selekcijski proces i pošaljite nam svoj životopis.'
        );

        $this->upsertTranslation(
            pageId: (int) $pageId,
            locale: 'en',
            title: 'Career',
            slug: 'karijera',
            excerpt: 'Recruitment, hiring and employee development.',
            bodyHtml: $this->careerHtmlEn(),
            metaTitle: 'Career | Alpha Capitalis',
            metaDescription: 'Career opportunities at Alpha Capitalis: explore our hiring process and send us your CV.'
        );

        if (Schema::hasTable('categories') && Schema::hasTable('content_info_page_category')) {
            $aboutCategoryId = DB::table('categories')
                ->where('scope', 'page')
                ->where('code', 'about')
                ->value('id');

            if ($aboutCategoryId) {
                $exists = DB::table('content_info_page_category')
                    ->where('page_id', $pageId)
                    ->where('category_id', $aboutCategoryId)
                    ->exists();

                if (! $exists) {
                    DB::table('content_info_page_category')->insert([
                        'page_id' => $pageId,
                        'category_id' => $aboutCategoryId,
                        'sort_order' => 0,
                        'is_primary' => true,
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        $page = DB::table('content_info_pages')
            ->where('code', 'career')
            ->where('layout', 'career')
            ->first(['id']);

        if (! $page) {
            return;
        }

        if (Schema::hasTable('content_info_page_category')) {
            DB::table('content_info_page_category')
                ->where('page_id', $page->id)
                ->delete();
        }

        DB::table('content_info_page_translations')
            ->where('page_id', $page->id)
            ->delete();

        DB::table('content_info_pages')
            ->where('id', $page->id)
            ->delete();
    }

    private function upsertTranslation(
        int $pageId,
        string $locale,
        string $title,
        string $slug,
        string $excerpt,
        string $bodyHtml,
        string $metaTitle,
        string $metaDescription
    ): void {
        $now = now();

        $query = DB::table('content_info_page_translations')
            ->where('page_id', $pageId)
            ->where('locale', $locale);

        if ($query->exists()) {
            $query->update([
                'title' => $title,
                'slug' => $slug,
                'excerpt' => $excerpt,
                'body_html' => $bodyHtml,
                'meta_title' => $metaTitle,
                'meta_description' => $metaDescription,
                'payload' => null,
                'updated_at' => $now,
            ]);

            return;
        }

        DB::table('content_info_page_translations')->insert([
            'page_id' => $pageId,
            'locale' => $locale,
            'title' => $title,
            'slug' => $slug,
            'excerpt' => $excerpt,
            'body_html' => $bodyHtml,
            'meta_title' => $metaTitle,
            'meta_description' => $metaDescription,
            'payload' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    private function careerHtmlHr(): string
    {
        return <<<'HTML'
<div class="ac-career-layout">
    <section class="ac-career-section ac-career-section--intro">
        <p class="ac-career-eyebrow">Ljudski potencijali</p>
        <div class="ac-career-intro-grid">
            <div class="ac-career-intro-copy">
                <h2>Postani dio tima</h2>
                <p>ALPHA CAPITALIS postoji od 2012. godine s ciljem pružanja podrške klijentima u svijetu financija kroz sve faze razvoja poslovanja.</p>
                <p>Oformili smo tim stručnjaka iz područja financija, revizije, računovodstva i poreza koji kroz zajedničko djelovanje nude cjelokupno rješenje za investitore, poduzetnike i menadžere. Članovi tima ALPHA CAPITALIS posjeduju višegodišnje iskustvo u investicijskom bankarstvu, financijskom savjetovanju, EU fondovima, reviziji, restrukturiranju, kontrolingu i menadžerskom računovodstvu.</p>
            </div>

            <aside class="ac-career-aside">
                <p class="ac-career-aside-label">Fokus</p>
                <p class="ac-career-aside-value">Regrutacija, zapošljavanje i usmjeravanje zaposlenika.</p>
            </aside>
        </div>
    </section>

    <section class="ac-career-section">
        <div class="ac-career-heading">
            <p class="ac-career-kicker">Proces prijave</p>
            <h2>Selekcijski proces u ALPHA CAPITALISU</h2>
            <p>Proces je jasan, strukturiran i fokusiran na kvalitetno upoznavanje kandidata i tima.</p>
        </div>

        <div class="ac-career-steps">
            <article class="ac-career-step">
                <p class="ac-career-step-number">01</p>
                <h3>Ispunjavanje prijave</h3>
                <p>Predaja prijave stiže u naš odjel ljudskih potencijala koji ocjenjuje prijavu i kontaktira kandidate u slučaju poklapanja profila i otvorene pozicije.</p>
            </article>

            <article class="ac-career-step">
                <p class="ac-career-step-number">02</p>
                <h3>Testiranje znanja</h3>
                <p>Kandidate koji ulaze u sljedeći krug pozivamo na opće i tehničko testiranje kako bismo provjerili stručnost i način razmišljanja.</p>
            </article>

            <article class="ac-career-step">
                <p class="ac-career-step-number">03</p>
                <h3>Razgovori</h3>
                <p>Nakon uspješnog testiranja slijedi razgovor s ljudskim potencijalima, a zatim i daljnji razgovori s timom i višim menadžmentom odjela za koji se kandidat prijavljuje.</p>
            </article>

            <article class="ac-career-step">
                <p class="ac-career-step-number">04</p>
                <h3>Ponuda za zaposlenje i onboarding</h3>
                <p>Po završetku razgovora slijedi završni korak selekcijskog procesa: ponuda za zaposlenje, potpis ugovora i onboarding kroz koji upoznajete naše poslovanje, vrijednosti, kulturu i kolege.</p>
            </article>
        </div>
    </section>

    <section class="ac-career-section">
        <div class="ac-career-cta-grid">
            <div>
                <div class="ac-career-heading">
                    <p class="ac-career-kicker">Karijera u Alpha Capitalisu</p>
                    <h2>Pridružite se timu ALPHA CAPITALIS!</h2>
                </div>
            </div>

            <div class="ac-career-copy">
                <p>Bez obzira jeste li iskusni profesionalac koji želi karijeru podići na novu razinu ili ste tek diplomirali, ALPHA CAPITALIS nudi mogućnosti za osobni i profesionalni napredak te dinamično radno okruženje koje vam omogućuje da ostvarite svoj puni potencijal.</p>
                <p>Potičemo polaganje stručnih ispita, razmjenu znanja kroz interne edukacije te rotacijski program uz stručno mentorstvo za stjecanje znanja iz područja financija, revizije, računovodstva i poreza.</p>
                <p>Tražimo motivirane i izvrsne osobe koje imaju želju za napretkom i stjecanjem novih znanja, a čiji je sustav vrijednosti u skladu s vrijednostima organizacije.</p>
                <p>Upoznajte nas i postanite dio tima ALPHA CAPITALIS.</p>

                <div class="ac-career-actions">
                    <a class="ac-career-primary-link" href="__CAREER_MAILTO__">Pošaljite nam svoj CV</a>
                    <a class="ac-career-secondary-link" href="__CONTACT_URL__">Kontakt</a>
                </div>

                <p class="ac-career-direct-line">Životopis možete poslati i direktno na <a href="__CAREER_MAILTO__">info@alphacapitalis.com</a>.</p>
            </div>
        </div>
    </section>
</div>
HTML;
    }

    private function careerHtmlEn(): string
    {
        return <<<'HTML'
<div class="ac-career-layout">
    <section class="ac-career-section ac-career-section--intro">
        <p class="ac-career-eyebrow">Human potential</p>
        <div class="ac-career-intro-grid">
            <div class="ac-career-intro-copy">
                <h2>Join our team</h2>
                <p>ALPHA CAPITALIS has been operating since 2012 with a clear goal: supporting clients in the world of finance through every stage of business growth.</p>
                <p>We have built a team of experts in finance, audit, accounting and tax who work together to provide an integrated solution for investors, entrepreneurs and managers. Our team members bring years of experience in investment banking, financial advisory, EU funds, audit, restructuring, controlling and management accounting.</p>
            </div>

            <aside class="ac-career-aside">
                <p class="ac-career-aside-label">Focus</p>
                <p class="ac-career-aside-value">Recruitment, hiring and employee development.</p>
            </aside>
        </div>
    </section>

    <section class="ac-career-section">
        <div class="ac-career-heading">
            <p class="ac-career-kicker">Hiring process</p>
            <h2>The ALPHA CAPITALIS selection process</h2>
            <p>The process is clear, structured and designed to help both the candidate and the team get to know each other properly.</p>
        </div>

        <div class="ac-career-steps">
            <article class="ac-career-step">
                <p class="ac-career-step-number">01</p>
                <h3>Application review</h3>
                <p>Your application is reviewed by our people team, who assess the profile and contact candidates whose experience matches an open role.</p>
            </article>

            <article class="ac-career-step">
                <p class="ac-career-step-number">02</p>
                <h3>Knowledge assessment</h3>
                <p>Candidates who move forward are invited to general and technical testing so we can better understand their expertise and way of thinking.</p>
            </article>

            <article class="ac-career-step">
                <p class="ac-career-step-number">03</p>
                <h3>Interviews</h3>
                <p>After successful testing, selected candidates have an interview with our people team and then continue with conversations with the relevant team and senior management.</p>
            </article>

            <article class="ac-career-step">
                <p class="ac-career-step-number">04</p>
                <h3>Offer and onboarding</h3>
                <p>The final stage includes the employment offer, contract signing and onboarding, where you learn more about our business, values, culture and colleagues.</p>
            </article>
        </div>
    </section>

    <section class="ac-career-section">
        <div class="ac-career-cta-grid">
            <div>
                <div class="ac-career-heading">
                    <p class="ac-career-kicker">Career at Alpha Capitalis</p>
                    <h2>Become part of ALPHA CAPITALIS</h2>
                </div>
            </div>

            <div class="ac-career-copy">
                <p>Whether you are an experienced professional ready for the next step or a recent graduate looking for a strong start, ALPHA CAPITALIS offers room for personal and professional growth in a dynamic working environment.</p>
                <p>We encourage professional certifications, knowledge sharing through internal education and rotational development supported by expert mentoring in finance, audit, accounting and tax.</p>
                <p>We are looking for motivated, high-performing people who want to keep learning and growing, and whose values align with the values of our organisation.</p>
                <p>Get to know us and become part of the ALPHA CAPITALIS team.</p>

                <div class="ac-career-actions">
                    <a class="ac-career-primary-link" href="__CAREER_MAILTO__">Send us your CV</a>
                    <a class="ac-career-secondary-link" href="__CONTACT_URL__">Contact us</a>
                </div>

                <p class="ac-career-direct-line">You can also send your CV directly to <a href="__CAREER_MAILTO__">info@alphacapitalis.com</a>.</p>
            </div>
        </div>
    </section>
</div>
HTML;
    }
};
