<?php

namespace App\Support\Content;

class CareerPageDefaults
{
    /**
     * @return array<string, mixed>
     */
    public static function merge(mixed $payload, string $locale): array
    {
        $defaults = self::defaultsForLocale($locale);
        $source = is_array($payload) ? $payload : [];

        $intro = is_array($source['intro'] ?? null) ? $source['intro'] : [];
        $process = is_array($source['process'] ?? null) ? $source['process'] : [];
        $application = is_array($source['application'] ?? null) ? $source['application'] : [];
        $form = is_array($source['form'] ?? null) ? $source['form'] : [];
        $sourceProcessSteps = is_array($process['steps'] ?? null) ? array_values($process['steps']) : [];
        $sourceIntroBody = is_array($intro['body'] ?? null) ? array_values($intro['body']) : [];
        $sourceApplicationParagraphs = is_array($application['paragraphs'] ?? null) ? array_values($application['paragraphs']) : [];

        $processSteps = [];
        foreach ((array) ($defaults['process']['steps'] ?? []) as $stepIndex => $defaultStep) {
            $sourceStep = is_array($sourceProcessSteps[$stepIndex] ?? null) ? $sourceProcessSteps[$stepIndex] : [];

            $processSteps[] = [
                'step' => self::valueOrDefault($sourceStep, 'step', (string) ($defaultStep['step'] ?? '')),
                'title' => self::valueOrDefault($sourceStep, 'title', (string) ($defaultStep['title'] ?? '')),
                'description' => self::valueOrDefault($sourceStep, 'description', (string) ($defaultStep['description'] ?? '')),
            ];
        }

        $introBody = [];
        foreach ((array) ($defaults['intro']['body'] ?? []) as $bodyIndex => $defaultParagraph) {
            $sourceParagraph = array_key_exists($bodyIndex, $sourceIntroBody)
                ? trim((string) $sourceIntroBody[$bodyIndex])
                : (string) $defaultParagraph;

            $introBody[] = $sourceParagraph;
        }

        $applicationParagraphs = [];
        foreach ((array) ($defaults['application']['paragraphs'] ?? []) as $bodyIndex => $defaultParagraph) {
            $sourceParagraph = array_key_exists($bodyIndex, $sourceApplicationParagraphs)
                ? trim((string) $sourceApplicationParagraphs[$bodyIndex])
                : (string) $defaultParagraph;

            $applicationParagraphs[] = $sourceParagraph;
        }

        return [
            'intro' => [
                'title' => self::valueOrDefault($intro, 'title', (string) ($defaults['intro']['title'] ?? '')),
                'highlight' => self::valueOrDefault($intro, 'highlight', (string) ($defaults['intro']['highlight'] ?? '')),
                'body' => $introBody,
            ],
            'process' => [
                'kicker' => self::valueOrDefault($process, 'kicker', (string) ($defaults['process']['kicker'] ?? '')),
                'title_line_one' => self::valueOrDefault($process, 'title_line_one', (string) ($defaults['process']['title_line_one'] ?? '')),
                'title_line_two' => self::valueOrDefault($process, 'title_line_two', (string) ($defaults['process']['title_line_two'] ?? '')),
                'intro' => self::valueOrDefault($process, 'intro', (string) ($defaults['process']['intro'] ?? '')),
                'steps' => $processSteps,
            ],
            'application' => [
                'title' => self::valueOrDefault($application, 'title', (string) ($defaults['application']['title'] ?? '')),
                'highlight' => self::valueOrDefault($application, 'highlight', (string) ($defaults['application']['highlight'] ?? '')),
                'paragraphs' => $applicationParagraphs,
            ],
            'form' => [
                'title' => self::valueOrDefault($form, 'title', (string) ($defaults['form']['title'] ?? '')),
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function defaultsForLocale(string $locale): array
    {
        return self::isCroatian($locale)
            ? self::croatianDefaults()
            : self::englishDefaults();
    }

    /**
     * @return array<string, mixed>
     */
    private static function croatianDefaults(): array
    {
        return [
            'intro' => [
                'title' => 'Postani dio tima',
                'highlight' => 'ALPHA CAPITALIS postoji od 2012. godine s ciljem pružanja podrške klijentima u svijetu financija kroz sve faze razvoja poslovanja.',
                'body' => [
                    'Oformili smo tim stručnjaka iz područja financija, revizije, računovodstva i poreza koji kroz zajedničko djelovanje nude cjelokupno rješenje za investitore, poduzetnike i menadžere. Članovi tima ALPHA CAPITALIS posjeduju višegodišnje iskustvo u investicijskom bankarstvu, financijskom savjetovanju, EU fonodvima, reviziji, restrukturiranju, kontrolingu i menadžerskom računovodstvu.',
                ],
            ],
            'process' => [
                'kicker' => 'Proces prijave',
                'title_line_one' => 'Selekcijski proces u',
                'title_line_two' => 'ALPHA CAPITALISU',
                'intro' => 'Proces je jasan, strukturiran i fokusiran na kvalitetno upoznavanje kandidata i tima.',
                'steps' => [
                    [
                        'step' => 'Korak 01',
                        'title' => 'Ispunjavanje prijave',
                        'description' => 'Predaja prijave stiže u naš odjel ljudskih potencijala koji je ocjenjuje i poziva kandidata na razgovor u slučaju poklapanja profila i otvorene pozicije.',
                    ],
                    [
                        'step' => 'Korak 02',
                        'title' => 'Testiranje znanja',
                        'description' => 'Poziv i dolazak na opće i tehničko testiranje znanja kojim provjeravamo stručnost, pristup problemima i usklađenost s otvorenom pozicijom.',
                    ],
                    [
                        'step' => 'Korak 03',
                        'title' => 'Razgovori',
                        'description' => 'Ljudski potencijali kontaktiraju osobe koje su zadovoljile očekivane kriterije na testiranju, nakon čega slijedi razgovor s timom i višim menadžmentom odjela.',
                    ],
                    [
                        'step' => 'Korak 04',
                        'title' => 'Ponuda za zaposlenje i onboarding',
                        'description' => 'Kada osoba završi razgovore, slijedi završni korak selekcijskog procesa: potpis ugovora i onboarding kroz koji upoznaje naše poslovanje, vrijednosti, kulturu i kolege.',
                    ],
                ],
            ],
            'application' => [
                'title' => 'Pridružite se timu ALPHA CAPITALIS!',
                'highlight' => 'Bez obzira jeste li iskusni profesionalac koji želi karijeru podići na novu razinu ili ste tek diplomirali, ALPHA CAPITALIS nudi mogućnosti za osobni i profesionalni napredak te dinamično radno okruženje koje će Vam omogućiti da postignete svoj puni potencijal.',
                'paragraphs' => [
                    'Potičemo polaganje stručnih ispita, razmjenu znanja kroz interne edukacije te rotacijski program uz stručno mentorstvo za stjecanje znanja iz područja financija, revizije, računovodstva i poreza.',
                    'Tražimo motivirane i izvrsne osobe koje imaju želju za napretkom i stjecanjem novih znanja, a čiji je sustav vrijednosti u skladu s vrijednostima organizacije.',
                    'Upoznajte nas i postanite dio tima ALPHA CAPITALIS.',
                ],
            ],
            'form' => [
                'title' => 'Pošaljite nam svoj CV',
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function englishDefaults(): array
    {
        return [
            'intro' => [
                'title' => 'Join our team',
                'highlight' => 'ALPHA CAPITALIS has been operating since 2012 with a clear goal: supporting clients in the world of finance through every stage of business growth.',
                'body' => [
                    'We have built a team of experts in finance, audit, accounting and tax who work together to provide an integrated solution for investors, entrepreneurs and managers. Our team members bring years of experience in investment banking, financial advisory, EU funds, audit, restructuring, controlling and management accounting.',
                ],
            ],
            'process' => [
                'kicker' => 'Hiring process',
                'title_line_one' => 'Selection process at',
                'title_line_two' => 'ALPHA CAPITALIS',
                'intro' => 'The process is clear, structured and focused on helping candidates and the team get to know each other properly.',
                'steps' => [
                    [
                        'step' => 'Step 01',
                        'title' => 'Application review',
                        'description' => 'Your application reaches our people team, which reviews it and invites candidates to an interview when the profile matches an open position.',
                    ],
                    [
                        'step' => 'Step 02',
                        'title' => 'Knowledge assessment',
                        'description' => 'Candidates are invited to general and technical knowledge testing so we can assess expertise, problem-solving and fit for the open role.',
                    ],
                    [
                        'step' => 'Step 03',
                        'title' => 'Interviews',
                        'description' => 'Our people team contacts candidates who met the expected criteria in testing, followed by interviews with the team and senior management.',
                    ],
                    [
                        'step' => 'Step 04',
                        'title' => 'Offer and onboarding',
                        'description' => 'After the interviews, the final stage of the selection process follows: contract signing and onboarding through which you get to know our business, values, culture and colleagues.',
                    ],
                ],
            ],
            'application' => [
                'title' => 'Join the ALPHA CAPITALIS team!',
                'highlight' => 'Whether you are an experienced professional ready to take your career to the next level or a recent graduate, ALPHA CAPITALIS offers opportunities for personal and professional growth in a dynamic work environment that helps you reach your full potential.',
                'paragraphs' => [
                    'We encourage professional certifications, knowledge sharing through internal education and a rotation program with expert mentoring across finance, audit, accounting and tax.',
                    'We are looking for motivated, high-performing people who want to keep learning and growing, and whose values align with the values of our organisation.',
                    'Get to know us and become part of the ALPHA CAPITALIS team.',
                ],
            ],
            'form' => [
                'title' => 'Send us your CV',
            ],
        ];
    }

    private static function valueOrDefault(array $values, string $key, string $default): string
    {
        if (! array_key_exists($key, $values)) {
            return $default;
        }

        return trim((string) $values[$key]);
    }

    private static function isCroatian(string $locale): bool
    {
        return str_starts_with(strtolower($locale), 'hr');
    }
}
