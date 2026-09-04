<?php

namespace App\Support\Content;

class AboutPageDefaults
{
    /**
     * @return array<string, mixed>
     */
    public static function merge(mixed $payload, string $locale): array
    {
        $source = is_array($payload) ? $payload : [];
        $usesCroatianDefaults = self::isCroatian($locale);
        $merged = self::mergeValues(self::forLocale($locale), $source);

        $storySource = is_array($source['story'] ?? null) ? $source['story'] : [];
        if ($usesCroatianDefaults || $storySource !== []) {
            $merged['story']['body_html'] = array_key_exists('body_html', $storySource)
                || array_key_exists('paragraphs', $storySource)
                    ? self::bodyHtml($storySource, (array) data_get($merged, 'story.paragraphs', []))
                    : ($usesCroatianDefaults ? self::croatianStoryBodyHtml() : '');
        } else {
            unset($merged['story']);
        }

        foreach ((array) data_get($merged, 'values.items', []) as $itemIndex => $item) {
            $sourceItem = is_array(data_get($source, 'values.items.'.$itemIndex))
                ? data_get($source, 'values.items.'.$itemIndex)
                : [];

            $merged['values']['items'][$itemIndex]['body_html'] = self::bodyHtml(
                $sourceItem,
                [
                    data_get($item, 'lead', ''),
                    ...(array) data_get($item, 'paragraphs', []),
                ],
            );
        }

        foreach (['why', 'culture', 'responsibility', 'references'] as $section) {
            $sectionSource = is_array($source[$section] ?? null) ? $source[$section] : [];
            if (! $usesCroatianDefaults && $sectionSource === []) {
                unset($merged[$section]);

                continue;
            }

            $merged[$section]['body_html'] = self::bodyHtml(
                $sectionSource,
                (array) data_get($merged, $section.'.paragraphs', []),
            );
        }

        $teamSource = is_array($source['team'] ?? null) ? $source['team'] : [];
        if ($usesCroatianDefaults || $teamSource !== []) {
            $merged['team']['body_html'] = self::bodyHtml(
                $teamSource,
                [data_get($merged, 'team.intro', ''), data_get($merged, 'team.body', '')],
            );
        } else {
            unset($merged['team']);
        }

        return $merged;
    }

    /**
     * @return array<string, mixed>
     */
    public static function forLocale(string $locale): array
    {
        return self::isCroatian($locale)
            ? self::croatianDefaults()
            : [];
    }

    /**
     * @return array<string, mixed>
     */
    private static function croatianDefaults(): array
    {
        return [
            'hero' => [
                'eyebrow' => 'O nama',
                'title' => 'Naša priča',
                'lead' => 'Više od stručne podrške. Partner kroz svaku fazu poslovanja.',
                'image_alt' => '',
                'stat_value' => '700',
                'stat_label' => 'klijenata kojima svakodnevno pružamo podršku',
            ],
            'story' => [
                'kicker' => 'Naša priča',
                'title' => 'Više od stručne podrške. Partner kroz svaku fazu poslovanja.',
                'paragraphs' => [
                    'ALPHA CAPITALIS okuplja stručnjake iz područja računovodstva, revizije i savjetovanja s jednom zajedničkom idejom – pomoći poduzetnicima da sigurnije prolaze kroz sve faze poslovanja.',
                    'Jer poslovanje nije ravna linija.',
                    'Tvrtke rastu, mijenjaju se, ulaze u nova tržišta, zapošljavaju, ulažu, prolaze kroz izazove i donose odluke koje mogu odrediti njihov sljedeći korak. Ponekad je potrebno pronaći priliku za rast. Ponekad zaštititi ono što se godinama gradilo. A ponekad pronaći rješenje kada stvari ne idu prema planu.',
                    'Upravo zato postojimo.',
                    'Želimo biti uz poduzetnike kada donose važne odluke, kada prelaze iz jedne faze poslovanja u drugu i kada se susreću s problemima za koje nije dovoljno jedno mišljenje ili jedno područje stručnosti.',
                    'Jer jedna osoba ne može znati i riješiti sve.',
                    'Ali snažan multidisciplinarni tim može sagledati poslovanje iz više perspektiva, povezati različita znanja i pomoći pronaći cjelovitije rješenje.',
                    'Zato naši klijenti uz sebe nemaju samo jednog savjetnika. Imaju tim stručnjaka koji razumije različite dijelove poslovanja i koji može pružiti podršku onda kada je ona najpotrebnija, od svakodnevnog računovodstva i poreznih pitanja do financijskih odluka, revizije, rasta, promjena i složenijih poslovnih izazova.',
                ],
            ],
            'values' => [
                'kicker' => 'Naše vrijednosti',
                'label' => 'Naše vrijednosti',
                'title' => 'Jednostavni principi koji vode svaki dan',
                'intro' => 'U ALPHA CAPITALISU vrijednosti nisu samo riječi - one određuju kako razmišljamo, kako radimo i kako gradimo odnose. One su prisutne u svakodnevnim odlukama, u načinu na koji surađujemo unutar tima i u odnosu koji gradimo s klijentima.',
                'items' => [
                    [
                        'title' => 'Learn fast',
                        'lead' => 'Volimo ljude koji žele učiti, pitati, istraživati i brzo se razvijati.',
                        'paragraphs' => [
                            'Radimo u okruženju koje se stalno mijenja - tržište, zakoni, tehnologija, potrebe klijenata. Zato vjerujemo da je sposobnost brzog učenja jedna od najvažnijih stvari koje možemo imati kao tim.',
                            'Kod nas nije problem ne znati. Problem je ne htjeti naučiti.',
                            'Zato dijelimo znanje, učimo jedni od drugih, razvijamo se kroz praksu i ne čekamo savršeni trenutak da preuzmemo odgovornost. Učimo brzo jer želimo biti bolji - za sebe, za tim i za klijente.',
                        ],
                    ],
                    [
                        'title' => 'Work smart, not hard',
                        'lead' => 'Ne vjerujemo u kulturu "ostani duže pa će izgledati da puno radiš".',
                        'paragraphs' => [
                            'Vjerujemo u pametan rad. To znači da razmišljamo unaprijed, postavljamo prioritete, tražimo bolja rješenja i ne radimo stvari samo zato što su se uvijek tako radile.',
                            'Volimo ljude koji prepoznaju problem, ali još više one koji predlažu rješenje. Za nas produktivnost nije kaos, nego fokus. Nije više sati, nego bolji način rada.',
                            'Želimo stvarati rezultate bez nepotrebne kompleksnosti - kvalitetno, odgovorno i s jasnim ciljem.',
                        ],
                    ],
                    [
                        'title' => 'Relationship over transaction',
                        'lead' => 'Ljudi su uvijek važniji od procesa.',
                        'paragraphs' => [
                            'Ne gradimo odnose koji traju jedan projekt ili jedan e-mail. Gradimo partnerstva.',
                            'To vrijedi za klijente, ali i za tim. Vjerujemo da se povjerenje gradi dostupnošću, iskrenošću, kvalitetnom komunikacijom i spremnošću da budemo tu kada je važno.',
                            'Volimo dugoročne odnose jer vjerujemo da najbolje stvari nastaju kada postoji povjerenje i kada ljudi stvarno razumiju jedni druge. Na kraju dana, brojke jesu važne, ali ljudi su razlog zašto posao ima smisla.',
                        ],
                    ],
                ],
            ],
            'why' => [
                'kicker' => 'Zašto postojimo',
                'title' => 'Uz vas prije, tijekom i nakon svake važne odluke',
                'quote' => 'Kvalitetna stručna podrška nije samo odgovor kada se problem već pojavi.',
                'paragraphs' => [
                    'Naš je cilj pomoći klijentima da prevladaju prepreke prije nego što postanu ozbiljan problem, da izbjegnu najteže scenarije kada je to moguće i da, ako se ipak nađu u zahtjevnoj situaciji, zajedno pronađemo put dalje.',
                    'Vjerujemo da kvalitetna stručna podrška znači imati prave ljude uz sebe prije, tijekom i nakon svake važne poslovne odluke.',
                ],
            ],
            'team' => [
                'kicker' => 'TIM',
                'label' => 'Naš tim',
                'title' => 'Tim stručnjaka na jednom mjestu',
                'intro' => 'Različiti izazovi zahtijevaju različita znanja. Svako poslovanje je drugačije, a ono što je jednoj tvrtki potrebno u fazi osnivanja nije isto što joj je potrebno tijekom rasta, širenja, reorganizacije ili prijenosa poslovanja na novu generaciju.',
                'body' => 'Zato u ALPHA CAPITALISU povezujemo stručnjake iz računovodstva, revizije, poreza, financija i poslovnog savjetovanja. Kada je potrebno, za istim izazovom okupljamo različite perspektive i znanja kako bi klijent dobio povezan tim koji razumije širi kontekst njegova poslovanja i može pružiti podršku kroz različite faze razvoja.',
                'stats' => [
                    ['value' => '75', 'label' => 'stručnjaka'],
                    ['value' => '9', 'label' => 'članova uprave'],
                    ['value' => '700', 'label' => 'klijenata'],
                    ['value' => '3', 'label' => 'ureda u Zagrebu, Vinkovcima i Rijeci'],
                ],
                'button_label' => 'Upoznaj cijeli tim',
            ],
            'culture' => [
                'kicker' => 'Naša kultura',
                'title' => 'Kvalitetno poslovanje počinje kvalitetnim odnosima',
                'quote' => 'U ALPHA CAPITALISU vjerujemo da kvalitetno poslovanje počinje kvalitetnim odnosima.',
                'paragraphs' => [
                    'Gradimo kulturu koja potiče suradnju, profesionalni razvoj, otvorenu komunikaciju i međusobno poštovanje.',
                    'Naš tim čine ljudi različitih iskustava i stručnosti koje povezuje zajednički cilj - pružiti klijentima najbolju moguću podršku.',
                    'Potičemo kontinuirano učenje, razmjenu znanja i razvoj novih ideja jer vjerujemo da upravo ljudi čine najveću razliku.',
                    'Uz profesionalnost, jednako nam je važna pozitivna radna atmosfera, osjećaj pripadnosti i zajednički rast.',
                ],
            ],
            'responsibility' => [
                'kicker' => 'Društveno odgovorno poslovanje',
                'title' => 'Udruga AUXILIUM CAPITALIS - ulaganje u budućnost',
                'quote' => 'Vjerujemo da uspjeh ima najveću vrijednost kada stvara prilike za druge.',
                'image_alt' => 'Udruga AUXILIUM CAPITALIS pruža podršku mladima kroz obrazovanje i razvoj.',
                'paragraphs' => [
                    'Zato smo pokrenuli AUXILIUM CAPITALIS - inicijativu usmjerenu na stipendiranje učenika i pružanje podrške mladima kroz obrazovanje, razvoj i financijsku pismenost.',
                    'Naš cilj je pomoći talentiranim i perspektivnim mladim ljudima da lakše ostvare svoj potencijal, bez obzira na okolnosti iz kojih dolaze.',
                    'Vjerujemo da ulaganje u znanje, prilike i mlade generacije dugoročno mijenja zajednicu na bolje.',
                    'AUXILIUM CAPITALIS nije samo projekt - to je način na koji želimo vraćati zajednici i stvarati konkretan, dugoročan utjecaj.',
                ],
                'cta_intro' => 'Želite biti dio ove priče?',
                'cta_text' => 'Pozivamo pojedince, partnere i tvrtke da nam se pridruže u stvaranju novih prilika za mlade i zajedno pomognemo graditi bolju budućnost.',
                'cta_button_label' => 'Kontaktirajte nas',
                'cta_card_title' => 'Zajedno možemo više.',
                'cta_status' => 'Otvoreni smo za razgovor i nova partnerstva.',
            ],
            'references' => [
                'kicker' => 'Reference',
                'label' => 'Naše reference',
                'title' => 'Povjerenje klijenata potvrđuje kvalitetu našeg rada',
                'paragraphs' => [
                    'Povjerenje 700 klijenata iz različitih industrija i sektora potvrda je kvalitete i stručnosti koju svakodnevno pružamo.',
                    'Surađujemo s malim, srednjim i velikim poduzećima kojima pružamo podršku u području računovodstva, revizije i poslovnog savjetovanja.',
                    'Naši dugoročni odnosi s klijentima temelje se na povjerenju, dostupnosti, stručnosti i razumijevanju njihovih poslovnih ciljeva.',
                    'Uspjeh naših klijenata ujedno je i najveća potvrda našeg rada.',
                ],
                'button_label' => 'Sve reference',
            ],
        ];
    }

    /**
     * @param  array<string|int, mixed>  $defaults
     * @param  array<string|int, mixed>  $source
     * @return array<string|int, mixed>
     */
    private static function mergeValues(array $defaults, array $source): array
    {
        $merged = $defaults;

        foreach ($source as $key => $value) {
            if (array_key_exists($key, $defaults) && is_array($defaults[$key])) {
                if (is_array($value)) {
                    $merged[$key] = self::mergeValues($defaults[$key], $value);
                }

                continue;
            }

            if (array_key_exists($key, $defaults) && is_array($value)) {
                continue;
            }

            if (is_array($value)) {
                $merged[$key] = self::mergeValues([], $value);

                continue;
            }

            if (is_scalar($value) || $value === null) {
                $merged[$key] = trim((string) $value);
            }
        }

        return $merged;
    }

    /**
     * @param  array<string|int, mixed>  $source
     * @param  array<int|string, mixed>  $paragraphs
     */
    private static function bodyHtml(array $source, array $paragraphs): string
    {
        if (array_key_exists('body_html', $source)) {
            return trim((string) $source['body_html']);
        }

        return StructuredRichText::fromParagraphs($paragraphs);
    }

    private static function croatianStoryBodyHtml(): string
    {
        return <<<'HTML'
<p>ALPHA CAPITALIS okuplja stručnjake iz područja računovodstva, revizije i savjetovanja s jednom zajedničkom idejom – pomoći poduzetnicima da sigurnije prolaze kroz sve faze poslovanja.</p>
<p><strong>Jer poslovanje nije ravna linija.</strong></p>
<p>Tvrtke rastu, mijenjaju se, ulaze u nova tržišta, zapošljavaju, ulažu, prolaze kroz izazove i donose odluke koje mogu odrediti njihov sljedeći korak. Ponekad je potrebno pronaći priliku za rast. Ponekad zaštititi ono što se godinama gradilo. A ponekad pronaći rješenje kada stvari ne idu prema planu.</p>
<p><strong>Upravo zato postojimo.</strong></p>
<p>Želimo biti uz poduzetnike kada donose važne odluke, kada prelaze iz jedne faze poslovanja u drugu i kada se susreću s problemima za koje nije dovoljno jedno mišljenje ili jedno područje stručnosti.</p>
<p><strong>Jer jedna osoba ne može znati i riješiti sve.</strong></p>
<p>Ali <strong>snažan multidisciplinarni tim</strong> može sagledati poslovanje iz više perspektiva, povezati različita znanja i pomoći pronaći cjelovitije rješenje.</p>
<p>Zato naši klijenti uz sebe nemaju samo jednog savjetnika. <strong>Imaju tim stručnjaka koji razumije različite dijelove poslovanja</strong> i koji može pružiti podršku onda kada je ona najpotrebnija, od svakodnevnog računovodstva i poreznih pitanja do financijskih odluka, revizije, rasta, promjena i složenijih poslovnih izazova.</p>
HTML;
    }

    private static function isCroatian(string $locale): bool
    {
        return str_starts_with(strtolower($locale), 'hr');
    }
}
