<?php

use Carbon\CarbonImmutable;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function (): void {
            if (DB::table('content_job_openings')->where('code', 'racunovoda-asistent-u-racunovodstvu')->exists()) {
                return;
            }

            if (DB::table('content_job_opening_translations')
                ->where('locale', 'hr')
                ->where('slug', 'racunovoda-asistent-u-racunovodstvu')
                ->exists()) {
                return;
            }

            $publishedAt = CarbonImmutable::create(2026, 9, 2, 9, 3, 0, 'Europe/Zagreb')->utc();
            $timestamps = CarbonImmutable::now('UTC');

            $openingId = DB::table('content_job_openings')->insertGetId([
                'code' => 'racunovoda-asistent-u-racunovodstvu',
                'is_active' => true,
                'published_at' => $publishedAt,
                'sort_order' => 0,
                'created_by' => null,
                'updated_by' => null,
                'created_at' => $timestamps,
                'updated_at' => $timestamps,
            ]);

            DB::table('content_job_opening_translations')->insert([
                'job_opening_id' => $openingId,
                'locale' => 'hr',
                'title' => 'Računovođa / Asistent u računovodstvu (m/ž)',
                'slug' => 'racunovoda-asistent-u-racunovodstvu',
                'locations' => 'Zagreb | Rijeka | Vinkovci',
                'excerpt' => 'Tražimo osobe s minimalno godinu dana iskustva u računovodstvu koje žele učiti, napredovati i graditi karijeru u modernom računovodstvu.',
                'body_html' => <<<'HTML'
<p><strong>Kako izgleda računovodstvo kada spojiš znanje, dobar tim i tehnologiju koja ti stvarno olakšava posao?</strong></p>
<p>U ALPHA CAPITALIS-u već godinama gradimo upravo takvo okruženje.</p>
<p>Okruženje u kojem računovođe imaju prostor učiti, razvijati se i preuzimati sve više odgovornosti – dok istovremeno kontinuirano radimo na digitalizaciji i automatizaciji svega onoga što im nepotrebno oduzima vrijeme.</p>
<p>Jer vjerujemo da najbolji računovođe svoje vrijeme trebaju koristiti za <strong>razmišljanje, kontrolu, analizu, komunikaciju s klijentima i razvoj svog znanja</strong>, a što manje za ručno prepisivanje podataka i ponavljajuće operativne zadatke.</p>
<p>Danas ALPHA CAPITALIS okuplja više od 75 stručnjaka iz područja računovodstva, revizije, poreznog savjetovanja i financijsko-savjetodavnih usluga, a naš računovodstveni tim broji više od 40 ljudi u Zagrebu, Rijeci i Vinkovcima.</p>
<p>I nastavljamo rasti.</p>
<p>Zato tražimo nove kolege u sva tri grada u kojima poslujemo za pozicije:</p>
<h2>RAČUNOVOĐA / ASISTENT U RAČUNOVODSTVU (m/ž)</h2>
<p>Tražimo osobe s <strong>minimalno godinu dana iskustva u računovodstvu</strong> koje žele napraviti sljedeći korak u svojoj karijeri.</p>
<p>Ne očekujemo da znaš sve, važno nam je da imaš dobre temelje, da si odgovoran/a i precizan/a, da želiš učiti i da računovodstvo vidiš kao područje u kojem se želiš razvijati.</p>
<h2>Što ćeš raditi?</h2>
<p>Ovisno o svom dosadašnjem iskustvu i znanju:</p>
<ul>
<li>knjiženje i kontrola poslovne dokumentacije</li>
<li>priprema i kontrola PDV-a i drugih poreznih evidencija</li>
<li>usklađenja i kontrola računovodstvenih podataka</li>
<li>sudjelovanje u mjesečnim i godišnjim zatvaranjima</li>
<li>komunikacija s klijentima</li>
<li>priprema izvještaja i analiza</li>
<li>postupno preuzimanje sve veće samostalnosti i odgovornosti za svoje klijente</li>
</ul>
<p>Razinu odgovornosti prilagodit ćemo tvom iskustvu – cilj nam je da uz podršku tima kontinuirano napreduješ.</p>
<h2>Kako izgleda rad kod nas?</h2>
<h3>Mentorstvo i kontinuirano učenje</h3>
<p>Imat ćeš podršku iskusnijih kolega, interne edukacije i tim u kojem se znanje dijeli. Bez obzira imaš li godinu, tri ili više godina iskustva, uvijek postoji prostor za sljedeću razinu znanja.</p>
<h3>Računovodstvo koje ide naprijed</h3>
<p>Snažno ulažemo u digitalizaciju, automatizaciju i razvoj naših procesa kako bismo što više manualnih i repetitivnih poslova prepustili tehnologiji.</p>
<p>Ne uvodimo tehnologiju radi tehnologije. Uvodimo je kako bismo našim ljudima oslobodili vrijeme za posao koji zahtijeva njihovo znanje i iskustvo.</p>
<p><strong>Želimo da naši računovođe budu računovođe – a ne operateri za unos podataka.</strong></p>
<h3>Prostor za razvoj i napredovanje</h3>
<p>Želimo da ljudi koji nam se pridruže dugoročno rastu s nama – od asistenta prema samostalnom računovođi, a zatim prema seniorskim i menadžerskim pozicijama.</p>
<h3>Tim koji radi zajedno</h3>
<p>Vjerujemo u otvorenu komunikaciju, međusobnu podršku i dijeljenje znanja. Ne želimo okruženje u kojem je svatko prepušten sam sebi.</p>
<h3>Normalno radno vrijeme</h3>
<p>Vjerujemo da se vrhunski posao može raditi bez kulture prekovremenih sati. Dobra organizacija, tehnologija i kvalitetni procesi trebaju omogućiti ljudima da imaju i kvalitetan privatni život.</p>
<h2>Što ti nudimo?</h2>
<ul>
<li>mentorstvo i podršku iskusnog tima</li>
<li>kontinuirane edukacije i razvoj stručnog znanja</li>
<li>moderne digitalne alate i sve više automatiziranih procesa</li>
<li>rad s različitim i kvalitetnim klijentima</li>
<li>postupno preuzimanje većih odgovornosti</li>
<li>mogućnost razvoja i napredovanja</li>
<li>normalno radno vrijeme bez kulture prekovremenih sati</li>
<li>okruženje koje kontinuirano raste, mijenja se i razvija</li>
</ul>
<p>Ne tražimo savršene životopise, tražimo ljude koji već imaju prve temelje i iskustvo, ali žele <strong>učiti, napredovati i graditi svoju karijeru u modernom računovodstvu.</strong></p>
<p>Ako želiš raditi u računovodstvu u kojem se cijene <strong>znanje i ljudi, a tehnologija služi tome da im posao bude bolji i kvalitetniji</strong>, voljeli bismo te upoznati.</p>
<p>Pošalji nam svoj životopis na <a href="mailto:hr@alphacapitalis.com"><strong>hr@alphacapitalis.com</strong></a> i postani dio ALPHA CAPITALIS tima.</p>
HTML,
                'meta_title' => 'Računovođa / Asistent u računovodstvu | ALPHA CAPITALIS',
                'meta_description' => 'Otvorena pozicija za računovođu ili asistenta u računovodstvu u Zagrebu, Rijeci i Vinkovcima. Pridruži se ALPHA CAPITALIS timu.',
                'created_at' => $timestamps,
                'updated_at' => $timestamps,
            ]);
        });
    }

    public function down(): void
    {
        // Insert-only content migration: keep any editor changes on rollback.
    }
};
