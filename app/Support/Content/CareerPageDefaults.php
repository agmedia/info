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

        if (self::looksLikeLegacyDefaultPayload($source)) {
            $source = [];
        }

        $intro = is_array($source['intro'] ?? null) ? $source['intro'] : [];
        $process = is_array($source['process'] ?? null) ? $source['process'] : [];
        $application = is_array($source['application'] ?? null) ? $source['application'] : [];
        $form = is_array($source['form'] ?? null) ? $source['form'] : [];
        $sourceValues = is_array($source['values'] ?? null) ? array_values($source['values']) : [];
        $sourceStories = is_array($source['stories'] ?? null) ? array_values($source['stories']) : [];
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

        $values = [];
        foreach ((array) ($defaults['values'] ?? []) as $valueIndex => $defaultValue) {
            $sourceValue = array_key_exists($valueIndex, $sourceValues)
                ? trim((string) $sourceValues[$valueIndex])
                : (string) $defaultValue;

            if ($sourceValue !== '') {
                $values[] = $sourceValue;
            }
        }

        $stories = [];
        foreach ((array) ($defaults['stories'] ?? []) as $storyIndex => $defaultStory) {
            $sourceStory = is_array($sourceStories[$storyIndex] ?? null) ? $sourceStories[$storyIndex] : [];
            $defaultParagraphs = is_array($defaultStory['paragraphs'] ?? null) ? array_values($defaultStory['paragraphs']) : [];
            $sourceParagraphs = is_array($sourceStory['paragraphs'] ?? null) ? array_values($sourceStory['paragraphs']) : [];
            $defaultList = is_array($defaultStory['list'] ?? null) ? array_values($defaultStory['list']) : [];
            $sourceList = is_array($sourceStory['list'] ?? null) ? array_values($sourceStory['list']) : [];

            $paragraphs = [];
            foreach ($defaultParagraphs as $paragraphIndex => $defaultParagraph) {
                $paragraph = array_key_exists($paragraphIndex, $sourceParagraphs)
                    ? trim((string) $sourceParagraphs[$paragraphIndex])
                    : (string) $defaultParagraph;

                if ($paragraph !== '') {
                    $paragraphs[] = $paragraph;
                }
            }

            $list = [];
            foreach ($defaultList as $itemIndex => $defaultItem) {
                $item = array_key_exists($itemIndex, $sourceList)
                    ? trim((string) $sourceList[$itemIndex])
                    : (string) $defaultItem;

                if ($item !== '') {
                    $list[] = $item;
                }
            }

            $stories[] = [
                'kicker' => self::valueOrDefault($sourceStory, 'kicker', (string) ($defaultStory['kicker'] ?? '')),
                'title' => self::valueOrDefault($sourceStory, 'title', (string) ($defaultStory['title'] ?? '')),
                'paragraphs' => $paragraphs,
                'list' => $list,
            ];
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
            'values' => $values,
            'stories' => $stories,
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
                'title' => 'Mjesto gdje karijera stvarno raste',
                'highlight' => 'Ne tražimo samo zaposlenike.',
                'body' => [
                    'Tražimo ljude koji žele učiti, razvijati se, preuzimati odgovornost i zajedno s nama graditi nešto dugoročno.',
                    'ALPHA CAPITALIS danas okuplja više od 70 stručnjaka iz područja računovodstva, financija, revizije, EU fondova i savjetovanja. Ono što nas povezuje nisu samo znanje i iskustvo, već način na koji radimo - zajedno, odgovorno i s jasnim ciljem razvoja.',
                    'Kod nas ćeš raditi na stvarnim poslovnim izazovima, surađivati s iskusnim stručnjacima i imati priliku razvijati se puno brže nego u klasičnom korporativnom okruženju.',
                ],
            ],
            'process' => [
                'kicker' => 'Zašto ALPHA CAPITALIS?',
                'title_line_one' => 'Razvoj koji nije',
                'title_line_two' => 'samo fraza',
                'intro' => 'Vjerujemo da se potencijal razvija kroz iskustvo, mentorstvo i prilike. Zato naši zaposlenici od prvog dana aktivno sudjeluju u projektima, surađuju s klijentima i razvijaju stručna znanja kroz rad s različitim industrijama i poslovnim izazovima.',
                'steps' => [
                    [
                        'step' => '01',
                        'title' => 'Povjerenje',
                        'description' => 'Od prvog dana dobivaš prostor sudjelovati u stvarnim projektima i preuzimati odgovornost uz jasnu podršku tima.',
                    ],
                    [
                        'step' => '02',
                        'title' => 'Podrška',
                        'description' => 'Učiš kroz mentorstvo, suradnju s iskusnim stručnjacima i otvoreno dijeljenje znanja unutar tima.',
                    ],
                    [
                        'step' => '03',
                        'title' => 'Prilika za razvoj',
                        'description' => 'Radiš s različitim industrijama i poslovnim izazovima, bez čekanja godinama da pokažeš što znaš.',
                    ],
                    [
                        'step' => '04',
                        'title' => 'Prostor za ideje',
                        'description' => 'Cijenimo proaktivnost, nova rješenja i ljude koji žele aktivno graditi bolji način rada.',
                    ],
                ],
            ],
            'application' => [
                'title' => 'Otvorene pozicije',
                'highlight' => 'Pronađi svoje mjesto u našem timu',
                'paragraphs' => [
                    'Tražimo ambiciozne, odgovorne i proaktivne ljude koji žele razvijati svoje znanje i karijeru u okruženju koje potiče rast.',
                    'Ne vidiš otvorenu poziciju?',
                    'Uvijek smo otvoreni za kvalitetne ljude. Ako vjeruješ da bi bio dobar dio ALPHA CAPITALIS tima, pošalji nam svoj životopis i predstavi se. Možda upravo ti budeš naše sljedeće veliko pojačanje.',
                ],
            ],
            'form' => [
                'title' => 'Pošalji nam svoj životopis',
            ],
            'values' => [
                'povjerenje',
                'podršku',
                'priliku za razvoj',
                'prostor za ideje',
                'tim koji ih gura naprijed',
            ],
            'stories' => [
                [
                    'kicker' => 'Tim',
                    'title' => 'Ljudi zbog kojih ostaješ',
                    'paragraphs' => [
                        'Možeš imati odličan posao, ali bez dobrog tima ništa nema smisla.',
                        'U ALPHA CAPITALISU gradimo kulturu međusobnog poštovanja, suradnje i otvorene komunikacije. Vjerujemo u dijeljenje znanja, podršku među kolegama i atmosferu u kojoj ljudi mogu biti profesionalni, ali i svoji.',
                        'Ozbiljni smo u poslu, ali vjerujemo da dobra atmosfera i kvalitetni odnosi čine veliku razliku.',
                    ],
                    'list' => [],
                ],
                [
                    'kicker' => 'Izazovi',
                    'title' => 'Okruženje koje te potiče na više',
                    'paragraphs' => [
                        'Radimo s poduzetnicima, obiteljskim tvrtkama i kompanijama koje rastu i razvijaju se. Zato ni naš posao nije rutinski.',
                        'Svaki projekt donosi nove izazove, nova znanja i priliku da razvijaš širu poslovnu perspektivu.',
                        'Ako voliš dinamiku, odgovornost i kontinuirani razvoj - osjećat ćeš se kao doma.',
                    ],
                    'list' => [],
                ],
                [
                    'kicker' => 'Rast',
                    'title' => 'Rastemo zajedno',
                    'paragraphs' => [
                        'ALPHA CAPITALIS nije mjesto gdje samo dolaziš odraditi posao.',
                        'Naš cilj je stvoriti okruženje u kojem ljudi dugoročno žele ostati, razvijati se i biti ponosni na ono što zajedno gradimo.',
                    ],
                    'list' => [
                        'znanje',
                        'iskustvo',
                        'odnose',
                        'samostalnost',
                        'karijeru',
                    ],
                ],
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
                'title' => 'A place where careers really grow',
                'highlight' => 'We are not just looking for employees.',
                'body' => [
                    'We are looking for people who want to learn, develop, take responsibility and build something long-term with us.',
                    'Today, ALPHA CAPITALIS brings together more than 70 experts in accounting, finance, audit, EU funds and advisory. What connects us is not only knowledge and experience, but the way we work - together, responsibly and with a clear development goal.',
                    'With us, you will work on real business challenges, collaborate with experienced professionals and have the opportunity to grow faster than in a classic corporate environment.',
                ],
            ],
            'process' => [
                'kicker' => 'Why ALPHA CAPITALIS?',
                'title_line_one' => 'Development that is',
                'title_line_two' => 'more than a phrase',
                'intro' => 'We believe potential grows through experience, mentorship and opportunity. From day one, our people actively participate in projects, collaborate with clients and build expertise through work across industries and business challenges.',
                'steps' => [
                    [
                        'step' => '01',
                        'title' => 'Trust',
                        'description' => 'From day one, you get the space to participate in real projects and take responsibility with clear team support.',
                    ],
                    [
                        'step' => '02',
                        'title' => 'Support',
                        'description' => 'You learn through mentorship, collaboration with experienced professionals and open knowledge sharing.',
                    ],
                    [
                        'step' => '03',
                        'title' => 'Growth opportunity',
                        'description' => 'You work with different industries and business challenges without waiting years to show what you can do.',
                    ],
                    [
                        'step' => '04',
                        'title' => 'Room for ideas',
                        'description' => 'We value proactivity, new solutions and people who want to actively build better ways of working.',
                    ],
                ],
            ],
            'application' => [
                'title' => 'Open positions',
                'highlight' => 'Find your place in our team',
                'paragraphs' => [
                    'We are looking for ambitious, responsible and proactive people who want to develop their knowledge and career in an environment that encourages growth.',
                    'Do not see an open position?',
                    'We are always open to quality people. If you believe you would be a good part of the ALPHA CAPITALIS team, send us your CV and introduce yourself.',
                ],
            ],
            'form' => [
                'title' => 'Send us your CV',
            ],
            'values' => [
                'trust',
                'support',
                'development opportunities',
                'room for ideas',
                'a team that pushes them forward',
            ],
            'stories' => [
                [
                    'kicker' => 'Team',
                    'title' => 'People who make you stay',
                    'paragraphs' => [
                        'You can have a great job, but without a good team it does not mean much.',
                        'At ALPHA CAPITALIS, we build a culture of mutual respect, collaboration and open communication. We believe in knowledge sharing, support among colleagues and an atmosphere where people can be professional and still be themselves.',
                        'We are serious about work, but we believe a good atmosphere and quality relationships make a real difference.',
                    ],
                    'list' => [],
                ],
                [
                    'kicker' => 'Challenges',
                    'title' => 'An environment that pushes you further',
                    'paragraphs' => [
                        'We work with entrepreneurs, family businesses and growing companies. That is why our work is not routine.',
                        'Every project brings new challenges, new knowledge and an opportunity to develop a broader business perspective.',
                        'If you like dynamics, responsibility and continuous development, you will feel at home.',
                    ],
                    'list' => [],
                ],
                [
                    'kicker' => 'Growth',
                    'title' => 'We grow together',
                    'paragraphs' => [
                        'ALPHA CAPITALIS is not a place where you only come to finish tasks.',
                        'Our goal is to create an environment where people want to stay long-term, develop and be proud of what we build together.',
                    ],
                    'list' => [
                        'knowledge',
                        'experience',
                        'relationships',
                        'independence',
                        'career',
                    ],
                ],
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

    private static function looksLikeLegacyDefaultPayload(array $source): bool
    {
        $intro = is_array($source['intro'] ?? null) ? $source['intro'] : [];
        $process = is_array($source['process'] ?? null) ? $source['process'] : [];
        $form = is_array($source['form'] ?? null) ? $source['form'] : [];

        return in_array(trim((string) ($intro['title'] ?? '')), ['Postani dio tima', 'Join our team'], true)
            || in_array(trim((string) ($process['title_line_one'] ?? '')), ['Selekcijski proces u', 'Selection process at'], true)
            || in_array(trim((string) ($form['title'] ?? '')), ['Pošaljite nam svoj CV', 'Send us your CV'], true);
    }

    private static function isCroatian(string $locale): bool
    {
        return str_starts_with(strtolower($locale), 'hr');
    }
}
