<?php
    $captchaSiteKey = trim((string) ($storeSettings['captcha']['recaptcha_v3_site_key'] ?? ''));
    $captchaEnabled = (bool) ($storeSettings['captcha']['recaptcha_v3_enabled'] ?? false) && $captchaSiteKey !== '';
    $contactEmail = trim((string) ($storeSettings['footer']['email_support'] ?? '')) ?: 'info@alphacapitalis.com';
    $contactPhone = trim((string) ($storeSettings['footer']['phone'] ?? '')) ?: '+385 (1) 580 6656';
    $contactPhoneHref = preg_replace('/\s+/', '', $contactPhone);
    $financeSprite = asset('front-theme/fonts/sprites/solid.svg');
    $meetingFormLabels = $meetingSection['form_labels'] ?? [];
    $maSalePhases = array_values($maSection['sale']['phases'] ?? []);
    $valuationBody = array_values($valuationsSection['body'] ?? []);
    $capitalBody = array_values($capitalRaisingSection['body'] ?? []);
    $capitalBodyLead = array_slice($capitalBody, 1, 2);
    $capitalBodyTail = array_slice($capitalBody, 3);
    $restructuringBody = array_values($restructuringSection['body'] ?? []);
    $pandeaBody = array_values($pandeaSection['body'] ?? []);
    $pandeaLeadParagraph = trim((string) ($pandeaBody[0] ?? ''));
    $pandeaSecondaryParagraph = trim((string) ($pandeaBody[1] ?? ''));
    $pandeaHeadline = trim((string) \Illuminate\Support\Str::before($pandeaLeadParagraph, ','));
    $isCroatianLocale = str_starts_with(strtolower((string) $locale), 'hr');
    $phaseTableLabels = $isCroatianLocale
        ? ['step' => 'Faza', 'focus' => 'Fokus', 'activities' => 'Ključne aktivnosti']
        : ['step' => 'Phase', 'focus' => 'Focus', 'activities' => 'Key activities'];
    $financeSectionMeta = [
        'ma' => [
            'number' => '01',
            'icon_view_box' => '0 0 512 512',
            'icon_href' => $financeSprite.'#building-columns',
        ],
        'due_diligence' => [
            'number' => '02',
            'icon_view_box' => '0 0 512 512',
            'icon_href' => $financeSprite.'#magnifying-glass',
        ],
        'valuations' => [
            'number' => '03',
            'icon_view_box' => '0 0 512 512',
            'icon_href' => $financeSprite.'#chart-column',
        ],
        'capital_raising' => [
            'number' => '04',
            'icon_view_box' => '0 0 576 512',
            'icon_href' => $financeSprite.'#hand-holding-dollar',
        ],
        'restructuring' => [
            'number' => '05',
            'icon_view_box' => '0 0 512 512',
            'icon_href' => $financeSprite.'#arrow-right-arrow-left',
        ],
    ];
    $financeListBullet = [
        'view_box' => '0 0 256 512',
        'href' => $financeSprite.'#angle-right',
    ];
    $financeCtaIcon = [
        'view_box' => '0 0 320 512',
        'href' => $financeSprite.'#angle-down',
    ];
    $servicesIntroTitle = trim((string) ($servicesIntroSection['title'] ?? ''));
    $servicesIntroTitleLines = [$servicesIntroTitle];

    $servicesIntroWords = preg_split('/\s+/u', $servicesIntroTitle, -1, PREG_SPLIT_NO_EMPTY) ?: [];
    if (count($servicesIntroWords) > 2 && mb_strlen($servicesIntroTitle) > 28) {
        $bestSplitIndex = null;
        $bestScore = null;
        $lastWordIndex = count($servicesIntroWords) - 1;

        for ($index = 1; $index < $lastWordIndex; $index++) {
            $left = implode(' ', array_slice($servicesIntroWords, 0, $index));
            $right = implode(' ', array_slice($servicesIntroWords, $index));
            $score = max(mb_strlen($left), mb_strlen($right)) * 100 + abs(mb_strlen($left) - mb_strlen($right));

            if ($bestScore === null || $score < $bestScore) {
                $bestScore = $score;
                $bestSplitIndex = $index;
            }
        }

        if ($bestSplitIndex !== null) {
            $servicesIntroTitleLines = [
                implode(' ', array_slice($servicesIntroWords, 0, $bestSplitIndex)),
                implode(' ', array_slice($servicesIntroWords, $bestSplitIndex)),
            ];
        }
    }

    $networkName = 'Pandea Global M&A';
?>

<?php $__env->startSection('title', $servicePageMetaTitle !== '' ? $servicePageMetaTitle : ($servicePageTitle ?? 'Financije')); ?>
<?php $__env->startSection('main_class', 'w-full px-0 py-0'); ?>

<?php $__env->startSection('content'); ?>
    <div class="ac-family-business-page ac-finance-page">
        <section class="ac-family-hero">
            <div class="ac-family-hero-media" aria-hidden="true" style="background-image: url('<?php echo e($heroBackgroundUrl); ?>');"></div>
            <div class="ac-family-hero-overlay"></div>

            <div class="mx-auto w-full max-w-[1240px] px-5 lg:px-8">
                <div class="ac-family-hero-content">
                    <div class="ac-family-hero-shell">
                        <div class="ac-family-hero-copy">
                            <h1 class="ac-family-hero-title">
                                <span class="is-brand"><?php echo e($heroSection['brand_title'] ?? 'ALPHA CAPITALIS'); ?></span>
                                <span class="is-subtitle">
                                    <span class="is-subtitle-lead"><?php echo e($heroSection['subtitle_lead'] ?? 'Savjetnici za'); ?></span>
                                    <span class="is-subtitle-accent"><?php echo e($heroSection['subtitle_accent'] ?? 'financije'); ?></span>
                                </span>
                            </h1>

                            <p class="ac-family-hero-intro"><?php echo e($heroSection['intro'] ?? ''); ?></p>

                            <div class="ac-family-hero-actions">
                                <a href="<?php echo e($heroSection['cta_url'] ?? '#finance-usluge'); ?>" class="front-action-cta">
                                    <span><?php echo e($heroSection['cta_label'] ?? 'Pregledajte usluge'); ?></span>
                                    <svg viewBox="<?php echo e($financeCtaIcon['view_box']); ?>" fill="currentColor" aria-hidden="true">
                                        <use href="<?php echo e($financeCtaIcon['href']); ?>"></use>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="mx-auto w-full max-w-[1240px] px-5 lg:px-8">
            <section class="ac-family-section ac-family-section--intro ac-finance-network-section">
                <div class="ac-family-ffi-banner">
                    <div class="ac-family-ffi-banner-copy">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pandeaHeadline !== ''): ?>
                            <h3><?php echo e($pandeaHeadline); ?></h3>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pandeaLeadParagraph !== ''): ?>
                            <p><?php echo e($pandeaLeadParagraph); ?></p>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pandeaSecondaryParagraph !== ''): ?>
                            <p><?php echo e($pandeaSecondaryParagraph); ?></p>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <div class="ac-family-ffi-logo-wrap">
                        <img src="<?php echo e($pandeaLogoUrl); ?>" alt="<?php echo e($pandeaSection['logo_alt'] ?? $networkName); ?>" class="ac-family-ffi-logo">
                    </div>
                </div>
            </section>
        </div>

        <section id="finance-usluge" class="ac-finance-editorial-wrap" aria-labelledby="ac-finance-services-title">
            <div class="mx-auto w-full max-w-[1240px] px-5 lg:px-8">
                <div class="ac-support-story-hero">
                    <div class="ac-support-story-shell">
                        <div class="ac-services-head ac-support-story-head">
                            <div class="ac-services-eyebrow">
                                <span class="ac-services-eyebrow-line" aria-hidden="true"></span>
                                <p class="ac-services-kicker"><?php echo e($servicesIntroSection['kicker'] ?? 'USLUGE'); ?></p>
                                <span class="ac-services-eyebrow-line" aria-hidden="true"></span>
                            </div>
                            <h2 id="ac-finance-services-title">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $servicesIntroTitleLines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <span><?php echo e($line); ?></span>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </h2>
                            <p class="ac-services-intro"><?php echo e($servicesIntroSection['intro'] ?? ''); ?></p>
                            <div class="ac-services-divider" aria-hidden="true">
                                <span class="ac-services-divider-line"></span>
                                <span class="ac-services-divider-glyph"></span>
                                <span class="ac-services-divider-line"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ac-finance-editorial-shell">
                    <?php($maMeta = $financeSectionMeta['ma'])
                    <article class="ac-finance-editorial-section">
                        <div class="ac-finance-editorial-head">
                            <div class="ac-finance-editorial-title">
                                <div class="ac-finance-editorial-badge">
                                    <span class="ac-finance-editorial-icon" aria-hidden="true">
                                        <svg viewBox="{{ $maMeta['icon_view_box'] }}" fill="currentColor">
                                            <use href="{{ $maMeta['icon_href'] }}"></use>
                                        </svg>
                                    </span>
                                    <span class="ac-finance-editorial-index">{{ $maMeta['number'] }}</span>
                                </div>
                                <div class="ac-finance-editorial-heading">
                                    <h2>{{ $maSection['title'] ?? '' }}</h2>
                                </div>
                            </div>
                            <div class="ac-finance-editorial-intro">
                                <p>{{ $maSection['intro'] ?? '' }}</p>
                            </div>
                        </div>

                        <div class="ac-finance-columns ac-finance-columns--single">
                            <article class="ac-finance-column ac-finance-column--spacious">
                                <h3>{{ $maSection['sale']['title'] ?? '' }}</h3>
                                <p>{{ $maSection['sale']['body'] ?? '' }}</p>
                            </article>
                        </div>

                        @if ($maSalePhases !== [])
                            <div class="ac-finance-followup">
                                <p class="ac-family-section-kicker">{{ $maSection['sale']['process_title'] ?? '' }}</p>

                                <div class="ac-finance-phase-table-shell">
                                    <table class="ac-finance-phase-table">
                                        <thead>
                                            <tr>
                                                <th scope="col">{{ $phaseTableLabels['step'] }}</th>
                                                <th scope="col">{{ $phaseTableLabels['focus'] }}</th>
                                                <th scope="col">{{ $phaseTableLabels['activities'] }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($maSalePhases as $phase)
                                                <tr>
                                                    <th scope="row">
                                                        <span class="ac-finance-phase-step">{{ $phase['title'] ?? '' }}</span>
                                                    </th>
                                                    <td>
                                                        <p class="ac-finance-phase-focus">{{ $phase['label'] ?? '' }}</p>
                                                    </td>
                                                    <td>
                                                        <ul class="ac-finance-phase-list">
                                                            @foreach (($phase['items'] ?? []) as $item)
                                                                <li>
                                                                    <span class="ac-finance-list-bullet" aria-hidden="true">
                                                                        <svg viewBox="{{ $financeListBullet['view_box'] }}" fill="currentColor">
                                                                            <use href="{{ $financeListBullet['href'] }}"></use>
                                                                        </svg>
                                                                    </span>
                                                                    <span>{{ $item }}</span>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif

                        <div class="ac-finance-followup">
                            <div class="ac-finance-columns ac-finance-columns--single">
                                <article class="ac-finance-column ac-finance-column--spacious">
                                    <h3>{{ $maSection['acquisition']['title'] ?? '' }}</h3>
                                    <p>{{ $maSection['acquisition']['body'] ?? '' }}</p>
                                </article>
                            </div>
                        </div>
                    </article>

                    @php($dueMeta = $financeSectionMeta['due_diligence'])
                    <article class="ac-finance-editorial-section">
                        <div class="ac-finance-editorial-head">
                            <div class="ac-finance-editorial-title">
                                <div class="ac-finance-editorial-badge">
                                    <span class="ac-finance-editorial-icon" aria-hidden="true">
                                        <svg viewBox="{{ $dueMeta['icon_view_box'] }}" fill="currentColor">
                                            <use href="{{ $dueMeta['icon_href'] }}"></use>
                                        </svg>
                                    </span>
                                    <span class="ac-finance-editorial-index">{{ $dueMeta['number'] }}</span>
                                </div>
                                <div class="ac-finance-editorial-heading">
                                    <h2>{{ $dueDiligenceSection['title'] ?? '' }}</h2>
                                </div>
                            </div>
                            <div class="ac-finance-editorial-intro">
                                <p>{{ $dueDiligenceSection['intro'] ?? '' }}</p>
                            </div>
                        </div>

                        <div class="ac-finance-columns ac-finance-columns--two-wide">
                            <article class="ac-finance-column">
                                <p class="ac-family-section-kicker">{{ $dueDiligenceSection['help_title'] ?? '' }}</p>
                                <ul class="ac-finance-list">
                                    @foreach (($dueDiligenceSection['help_items'] ?? []) as $item)
                                        <li>
                                            <span class="ac-finance-list-bullet" aria-hidden="true">
                                                <svg viewBox="{{ $financeListBullet['view_box'] }}" fill="currentColor">
                                                    <use href="{{ $financeListBullet['href'] }}"></use>
                                                </svg>
                                            </span>
                                            <span>{{ $item }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </article>

                            <article class="ac-finance-column">
                                <p>{{ $dueDiligenceSection['closing'] ?? '' }}</p>
                            </article>
                        </div>
                    </article>

                    @php($valuationMeta = $financeSectionMeta['valuations'])
                    <article class="ac-finance-editorial-section">
                        <div class="ac-finance-editorial-head">
                            <div class="ac-finance-editorial-title">
                                <div class="ac-finance-editorial-badge">
                                    <span class="ac-finance-editorial-icon" aria-hidden="true">
                                        <svg viewBox="{{ $valuationMeta['icon_view_box'] }}" fill="currentColor">
                                            <use href="{{ $valuationMeta['icon_href'] }}"></use>
                                        </svg>
                                    </span>
                                    <span class="ac-finance-editorial-index">{{ $valuationMeta['number'] }}</span>
                                </div>
                                <div class="ac-finance-editorial-heading">
                                    <h2>{{ $valuationsSection['title'] ?? '' }}</h2>
                                </div>
                            </div>
                            <div class="ac-finance-editorial-intro">
                                <p>{{ $valuationBody[0] ?? '' }}</p>
                            </div>
                        </div>

                        <div class="ac-finance-columns ac-finance-columns--two-wide">
                            <article class="ac-finance-column">
                                @foreach (array_slice($valuationBody, 1) as $paragraph)
                                    <p>{{ $paragraph }}</p>
                                @endforeach
                            </article>

                            <article class="ac-finance-column">
                                <p class="ac-family-section-kicker">{{ $valuationsSection['methods_title'] ?? '' }}</p>
                                <ul class="ac-finance-list">
                                    @foreach (($valuationsSection['methods'] ?? []) as $method)
                                        <li>
                                            <span class="ac-finance-list-bullet" aria-hidden="true">
                                                <svg viewBox="{{ $financeListBullet['view_box'] }}" fill="currentColor">
                                                    <use href="{{ $financeListBullet['href'] }}"></use>
                                                </svg>
                                            </span>
                                            <span>{{ $method }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </article>
                        </div>
                    </article>

                    @php($capitalMeta = $financeSectionMeta['capital_raising'])
                    <article class="ac-finance-editorial-section">
                        <div class="ac-finance-editorial-head">
                            <div class="ac-finance-editorial-title">
                                <div class="ac-finance-editorial-badge">
                                    <span class="ac-finance-editorial-icon" aria-hidden="true">
                                        <svg viewBox="{{ $capitalMeta['icon_view_box'] }}" fill="currentColor">
                                            <use href="{{ $capitalMeta['icon_href'] }}"></use>
                                        </svg>
                                    </span>
                                    <span class="ac-finance-editorial-index">{{ $capitalMeta['number'] }}</span>
                                </div>
                                <div class="ac-finance-editorial-heading">
                                    <h2>{{ $capitalRaisingSection['title'] ?? '' }}</h2>
                                </div>
                            </div>
                            <div class="ac-finance-editorial-intro">
                                <p>{{ $capitalBody[0] ?? '' }}</p>
                            </div>
                        </div>

                        <div class="ac-finance-columns ac-finance-columns--single">
                            <article class="ac-finance-column ac-finance-column--spacious">
                                @foreach ($capitalBodyLead as $paragraph)
                                    <p>{{ $paragraph }}</p>
                                @endforeach
                            </article>
                        </div>

                        <div class="ac-finance-followup">
                            <div class="ac-finance-columns ac-finance-columns--two-wide">
                                <article class="ac-finance-column">
                                    <p class="ac-family-section-kicker">{{ $capitalRaisingSection['sources_title'] ?? '' }}</p>
                                    <ul class="ac-finance-list">
                                        @foreach (($capitalRaisingSection['sources'] ?? []) as $source)
                                            <li>
                                                <span class="ac-finance-list-bullet" aria-hidden="true">
                                                    <svg viewBox="{{ $financeListBullet['view_box'] }}" fill="currentColor">
                                                        <use href="{{ $financeListBullet['href'] }}"></use>
                                                    </svg>
                                                </span>
                                                <span>{{ $source }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </article>

                                <article class="ac-finance-column">
                                    @foreach ($capitalBodyTail as $paragraph)
                                        <p>{{ $paragraph }}</p>
                                    @endforeach
                                </article>
                            </div>
                        </div>

                        @if ($capitalBodyLead === [] && $capitalBodyTail === [])
                            <div class="ac-finance-followup">
                                <div class="ac-finance-columns ac-finance-columns--single">
                                    <article class="ac-finance-column">
                                        <p class="ac-family-section-kicker">{{ $capitalRaisingSection['sources_title'] ?? '' }}</p>
                                        <ul class="ac-finance-list">
                                            @foreach (($capitalRaisingSection['sources'] ?? []) as $source)
                                                <li>
                                                    <span class="ac-finance-list-bullet" aria-hidden="true">
                                                        <svg viewBox="{{ $financeListBullet['view_box'] }}" fill="currentColor">
                                                            <use href="{{ $financeListBullet['href'] }}"></use>
                                                        </svg>
                                                    </span>
                                                    <span>{{ $source }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </article>
                                </div>
                            </div>
                        @endif
                    </article>

                    @php($restructuringMeta = $financeSectionMeta['restructuring'])
                    <article class="ac-finance-editorial-section">
                        <div class="ac-finance-editorial-head">
                            <div class="ac-finance-editorial-title">
                                <div class="ac-finance-editorial-badge">
                                    <span class="ac-finance-editorial-icon" aria-hidden="true">
                                        <svg viewBox="{{ $restructuringMeta['icon_view_box'] }}" fill="currentColor">
                                            <use href="{{ $restructuringMeta['icon_href'] }}"></use>
                                        </svg>
                                    </span>
                                    <span class="ac-finance-editorial-index">{{ $restructuringMeta['number'] }}</span>
                                </div>
                                <div class="ac-finance-editorial-heading">
                                    <h2>{{ $restructuringSection['title'] ?? '' }}</h2>
                                </div>
                            </div>
                            <div class="ac-finance-editorial-intro">
                                <p>{{ $restructuringBody[0] ?? '' }}</p>
                            </div>
                        </div>

                        <div class="ac-finance-columns ac-finance-columns--single">
                            <article class="ac-finance-column ac-finance-column--spacious">
                                @foreach (array_slice($restructuringBody, 1) as $paragraph)
                                    <p>{{ $paragraph }}</p>
                                @endforeach
                            </article>
                        </div>

                        <div class="ac-finance-followup">
                            <p class="ac-family-section-kicker">{{ $restructuringSection['prebankruptcy_title'] ?? '' }}</p>
                            <div class="ac-finance-columns ac-finance-columns--single">
                                <article class="ac-finance-column">
                                    <p>{{ $restructuringSection['prebankruptcy_body'] ?? '' }}</p>
                                </article>
                            </div>
                        </div>

                        <div class="ac-finance-columns ac-finance-columns--three">
                            <article class="ac-finance-column">
                                <p class="ac-family-section-kicker">{{ $restructuringSection['options_title'] ?? '' }}</p>
                                <ul class="ac-finance-list">
                                    @foreach (($restructuringSection['options'] ?? []) as $item)
                                        <li>
                                            <span class="ac-finance-list-bullet" aria-hidden="true">
                                                <svg viewBox="{{ $financeListBullet['view_box'] }}" fill="currentColor">
                                                    <use href="{{ $financeListBullet['href'] }}"></use>
                                                </svg>
                                            </span>
                                            <span>{{ $item }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </article>

                            <article class="ac-finance-column">
                                <p class="ac-family-section-kicker">{{ $restructuringSection['reasons_title'] ?? '' }}</p>
                                <ul class="ac-finance-list">
                                    @foreach (($restructuringSection['reasons'] ?? []) as $item)
                                        <li>
                                            <span class="ac-finance-list-bullet" aria-hidden="true">
                                                <svg viewBox="{{ $financeListBullet['view_box'] }}" fill="currentColor">
                                                    <use href="{{ $financeListBullet['href'] }}"></use>
                                                </svg>
                                            </span>
                                            <span>{{ $item }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </article>

                            <article class="ac-finance-column">
                                <p class="ac-family-section-kicker">{{ $restructuringSection['team_services_title'] ?? '' }}</p>
                                <ul class="ac-finance-list">
                                    @foreach (($restructuringSection['team_services'] ?? []) as $item)
                                        <li>
                                            <span class="ac-finance-list-bullet" aria-hidden="true">
                                                <svg viewBox="{{ $financeListBullet['view_box'] }}" fill="currentColor">
                                                    <use href="{{ $financeListBullet['href'] }}"></use>
                                                </svg>
                                            </span>
                                            <span>{{ $item }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </article>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <div class="mx-auto w-full max-w-[1240px] px-5 lg:px-8">
            <section id="finance-sastanak" class="ac-family-section pb-16 md:pb-24" aria-labelledby="ac-finance-meeting-title">
                <div class="ac-family-team-showcase-head">
                    <p class="ac-family-section-kicker">{{ $meetingSection['kicker'] ?? 'KONTAKT' }}</p>
                    <h2 id="ac-finance-meeting-title">{{ $meetingSection['title'] ?? '' }}</h2>
                    <p>{{ $meetingSection['intro'] ?? '' }}</p>
                </div>

                <div class="mt-10 grid gap-5 lg:grid-cols-[300px_minmax(0,1fr)] lg:items-start">
                    <aside class="front-contact-sidebar">
                        <div class="front-contact-panel front-contact-panel--direct">
                            <h2>{{ $meetingSection['visit_title'] ?? 'Posjetite nas' }}</h2>
                            <div class="mt-4 space-y-1 text-[0.89rem] leading-6 text-slate-700">
                                <p style="white-space: nowrap;">{{ $meetingSection['visit_lines'][0] ?? '' }}</p>
                                <p>{{ $meetingSection['visit_lines'][1] ?? '' }}</p>
                            </div>
                        </div>

                        <div class="front-contact-panel front-contact-panel--direct">
                            <h2>{{ $meetingSection['contact_title'] ?? 'Kontaktirajte nas' }}</h2>
                            <ul class="front-contact-direct-list">
                                <li>
                                    <span>{{ $meetingSection['direct_phone_label'] ?? 'Telefon' }}</span>
                                    <a href="tel:{{ $contactPhoneHref }}">{{ $contactPhone }}</a>
                                </li>
                                <li>
                                    <span>{{ $meetingSection['direct_email_label'] ?? 'Email' }}</span>
                                    <a href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a>
                                </li>
                            </ul>
                        </div>
                    </aside>

                    <form
                        method="POST"
                        action="{{ route('contact.store') }}"
                        class="front-contact-form"
                        novalidate
                        data-contact-form
                        data-msg-name-required="{{ __('contact.validation.inline.name_required') }}"
                        data-msg-email-required="{{ __('contact.validation.inline.email_required') }}"
                        data-msg-email-invalid="{{ __('contact.validation.inline.email_invalid') }}"
                        data-msg-message-required="{{ __('contact.validation.inline.message_required') }}"
                        data-msg-message-min="{{ __('contact.validation.inline.message_min') }}"
                        data-msg-accept-terms="{{ __('contact.validation.inline.accept_terms') }}"
                        @if($captchaEnabled) data-recaptcha-form data-recaptcha-site-key="{{ $captchaSiteKey }}" data-recaptcha-action="contact_form" @endif
                    >
                        @csrf
                        <input type="hidden" name="recaptcha_token" value="" data-recaptcha-token>
                        <input type="hidden" name="redirect_to" value="{{ route('finance.show') }}#finance-sastanak">

                        @if (session('status'))
                            <div class="front-contact-status" role="status">
                                {{ session('status') }}
                            </div>
                        @endif

                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500" for="finance-first-name">{{ $meetingFormLabels['first_name'] ?? 'Ime' }}</label>
                                <input id="finance-first-name" type="text" name="first_name" value="{{ old('first_name') }}" class="front-contact-input h-11 w-full text-sm" required>
                                <p class="mt-2 text-xs font-semibold text-rose-600 {{ $errors->has('first_name') ? '' : 'hidden' }}" data-field-error="first_name">@error('first_name'){{ $message }}@enderror</p>
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500" for="finance-last-name">{{ $meetingFormLabels['last_name'] ?? 'Prezime' }}</label>
                                <input id="finance-last-name" type="text" name="last_name" value="{{ old('last_name') }}" class="front-contact-input h-11 w-full text-sm">
                                <p class="mt-2 text-xs font-semibold text-rose-600 {{ $errors->has('last_name') ? '' : 'hidden' }}" data-field-error="last_name">@error('last_name'){{ $message }}@enderror</p>
                            </div>
                        </div>

                        <div class="mt-4 grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500" for="finance-company">{{ $meetingFormLabels['company'] ?? 'Tvrtka' }}</label>
                                <input id="finance-company" type="text" name="company" value="{{ old('company') }}" class="front-contact-input h-11 w-full text-sm">
                                <p class="mt-2 text-xs font-semibold text-rose-600 {{ $errors->has('company') ? '' : 'hidden' }}" data-field-error="company">@error('company'){{ $message }}@enderror</p>
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500" for="finance-phone">{{ $meetingFormLabels['phone'] ?? 'Broj telefona' }}</label>
                                <input id="finance-phone" type="text" name="phone" value="{{ old('phone') }}" class="front-contact-input h-11 w-full text-sm">
                                <p class="mt-2 text-xs font-semibold text-rose-600 {{ $errors->has('phone') ? '' : 'hidden' }}" data-field-error="phone">@error('phone'){{ $message }}@enderror</p>
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500" for="finance-email">{{ $meetingFormLabels['email'] ?? 'Email' }}</label>
                            <input id="finance-email" type="email" name="email" value="{{ old('email', auth()->user()?->email) }}" class="front-contact-input h-11 w-full text-sm" required>
                            <p class="mt-2 text-xs font-semibold text-rose-600 {{ $errors->has('email') ? '' : 'hidden' }}" data-field-error="email">@error('email'){{ $message }}@enderror</p>
                        </div>

                        <div class="mt-4">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500" for="finance-subject">{{ $meetingFormLabels['subject'] ?? 'Naslov poruke' }}</label>
                            <input id="finance-subject" type="text" name="subject" value="{{ old('subject') }}" class="front-contact-input h-11 w-full text-sm">
                            <p class="mt-2 text-xs font-semibold text-rose-600 {{ $errors->has('subject') ? '' : 'hidden' }}" data-field-error="subject">@error('subject'){{ $message }}@enderror</p>
                        </div>

                        <div class="mt-4">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500" for="finance-message">{{ $meetingFormLabels['message'] ?? 'Poruka' }}</label>
                            <textarea id="finance-message" name="message" rows="8" class="front-contact-textarea w-full text-sm" required>{{ old('message') }}</textarea>
                            <p class="mt-2 text-xs font-semibold text-rose-600 {{ $errors->has('message') ? '' : 'hidden' }}" data-field-error="message">@error('message'){{ $message }}@enderror</p>
                        </div>

                        <div class="front-contact-consent-wrap">
                            <label class="front-contact-consent">
                                <input type="checkbox" name="accept_terms" value="1" class="front-contact-checkbox mt-0.5 h-4 w-4 border-slate-300 text-slate-900 focus:ring-0" @checked((bool) old('accept_terms'))>
                                <span>{{ __('contact.form.accept_terms') }}</span>
                            </label>
                            <p class="mt-2 text-xs font-semibold text-rose-600 {{ $errors->has('accept_terms') ? '' : 'hidden' }}" data-field-error="accept_terms">@error('accept_terms'){{ $message }}@enderror</p>
                        </div>

                        <div class="front-contact-form-actions">
                            <button type="submit" class="front-contact-submit inline-flex h-11 items-center justify-center px-6 text-sm font-semibold text-white transition">
                                {{ $meetingSection['submit'] ?? 'Pošalji' }}
                            </button>
                            <p class="text-xs font-semibold text-rose-600 {{ $errors->has('recaptcha_token') ? '' : 'hidden' }}" data-field-error="recaptcha_token">@error('recaptcha_token'){{ $message }}@enderror</p>
                        </div>
                    </form>
                </div>
            </section>
        </div>

        @if (($financePosts ?? collect())->isNotEmpty())
            <section class="ac-support-story ac-home-blog ac-blog-related-section ac-family-blog-section" aria-labelledby="ac-finance-blog-title">
                <div class="mx-auto w-full max-w-[1240px] px-6 lg:px-10">
                    <div class="ac-support-story-hero">
                        <div class="ac-support-story-shell">
                            <div class="ac-services-head ac-support-story-head">
                                <h2 id="ac-finance-blog-title">
                                    <span>{{ $blogSection['title'] ?? '' }}</span>
                                </h2>
                                <p class="ac-services-intro">{{ $blogSection['intro'] ?? '' }}</p>
                                <div class="ac-services-divider" aria-hidden="true">
                                    <span class="ac-services-divider-line"></span>
                                    <span class="ac-services-divider-glyph"></span>
                                    <span class="ac-services-divider-line"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="ac-home-blog-carousel">
                        <div id="ac-finance-blog-splide" class="splide ac-home-blog-splide" data-finance-blog-splide>
                            <div class="splide__track">
                                <ul class="splide__list">
                                    @foreach ($financePosts as $post)
                                        @php
                                            $translation = $post->translations->firstWhere('locale', $locale)
                                                ?? $post->translations->firstWhere('locale', $fallbackLocale);
                                            $postSlug = trim((string) ($translation?->slug ?? ''));
                                            $postUrl = $postSlug !== '' ? route('blog.show', ['slug' => $postSlug]) : route('blog.index');
                                            $postTitle = trim((string) ($translation?->title ?? $post->code));
                                            $postExcerpt = trim((string) ($translation?->excerpt ?? '')) ?: __('ui.blog.excerpt_fallback');
                                            $postExcerpt = \Illuminate\Support\Str::limit($postExcerpt, 180, '...', true);
                                            $postImage = $post->getFirstMedia('blog_cover');
                                            $postImageUrl = $postImage?->getUrl();
                                            $primaryCategory = $post->categories
                                                ->sortByDesc(fn ($category) => (int) ($category->pivot->is_primary ?? false))
                                                ->first();
                                            $categoryTranslation = $primaryCategory?->translations->firstWhere('locale', $locale)
                                                ?? $primaryCategory?->translations->firstWhere('locale', $fallbackLocale);
                                            $categoryLabel = trim((string) ($categoryTranslation?->name ?? 'Novosti'));
                                            $publishedLabel = ($post->published_at ?? $post->created_at)?->translatedFormat('j. F Y.');
                                        ?>
                                        <li class="splide__slide ac-home-blog-slide">
                                            <article class="ac-home-blog-card">
                                                <a href="<?php echo e($postUrl); ?>" class="ac-home-blog-card-link" aria-label="Otvori blog post: <?php echo e($postTitle); ?>">
                                                    <div class="ac-home-blog-card-media">
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($postImageUrl): ?>
                                                            <img
                                                                src="<?php echo e($postImageUrl); ?>"
                                                                alt="<?php echo e($postTitle); ?>"
                                                                class="ac-home-blog-card-image"
                                                                loading="lazy"
                                                                decoding="async"
                                                            >
                                                        <?php else: ?>
                                                            <div class="ac-home-blog-card-placeholder">
                                                                <span><?php echo e(__('ui.blog.title')); ?></span>
                                                            </div>
                                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                                        <div class="ac-home-blog-card-overlay">
                                                            <span class="ac-home-blog-card-overlay-kicker">
                                                                <?php echo e(\Illuminate\Support\Str::upper(\Illuminate\Support\Str::limit($categoryLabel, 22, ''))); ?>

                                                            </span>
                                                            <span class="ac-home-blog-card-overlay-line" aria-hidden="true"></span>
                                                        </div>
                                                    </div>

                                                    <div class="ac-home-blog-card-body">
                                                        <h3 class="ac-home-blog-card-title"><?php echo e($postTitle); ?></h3>
                                                        <p class="ac-home-blog-card-excerpt"><?php echo e($postExcerpt); ?></p>
                                                    </div>

                                                    <div class="ac-home-blog-card-meta">
                                                        <span class="ac-home-blog-card-meta-link">
                                                            <span>Opširnije</span>
                                                            <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                                <path d="M4 12L12 4"></path>
                                                                <path d="M6 4h6v6"></path>
                                                            </svg>
                                                        </span>
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($publishedLabel): ?>
                                                            <span class="ac-home-blog-card-meta-date"><?php echo e($publishedLabel); ?></span>
                                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    </div>
                                                </a>
                                            </article>
                                        </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php if (! $__env->hasRenderedOnce('90f2151f-f78f-43e4-94d6-efcda6f4af3a')): $__env->markAsRenderedOnce('90f2151f-f78f-43e4-94d6-efcda6f4af3a'); ?>
    <?php $__env->startPush('styles'); ?>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/css/splide.min.css">
    <?php $__env->stopPush(); ?>
<?php endif; ?>

<?php $__env->startPush('styles'); ?>
    <style>
        .ac-finance-network-section {
            margin-top: clamp(2rem, 3.6vw, 2.8rem);
            padding-top: 0;
            padding-bottom: clamp(2rem, 3.6vw, 2.8rem);
        }

        .ac-finance-network-section .ac-family-ffi-banner {
            margin-top: 0;
        }

        .ac-finance-editorial-wrap {
            position: relative;
            overflow: hidden;
            padding: clamp(1.5rem, 2.5vw, 2rem) 0 clamp(1.7rem, 2.6vw, 2.2rem);
            background: #f7f3eb;
            border-top: 1px solid rgba(171, 141, 82, 0.14);
            border-bottom: 1px solid rgba(171, 141, 82, 0.12);
        }

        .ac-finance-editorial-wrap::before {
            content: '';
            position: absolute;
            inset: 0;
            background: repeating-linear-gradient(90deg, rgba(15, 42, 67, 0.05) 0 1px, transparent 1px 104px);
            opacity: 0.72;
            pointer-events: none;
        }

        .ac-finance-editorial-wrap > .mx-auto {
            position: relative;
            z-index: 1;
        }

        .ac-finance-editorial-wrap .ac-support-story-head {
            max-width: 54rem;
            margin: 0 auto;
            padding-top: clamp(0.4rem, 0.9vw, 0.7rem);
            text-align: center;
        }

        .ac-finance-editorial-wrap .ac-services-eyebrow {
            justify-content: center;
        }

        .ac-finance-editorial-wrap .ac-services-eyebrow-line {
            display: none;
        }

        .ac-finance-editorial-wrap .ac-services-kicker {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 2.55rem;
            padding: 0.45rem 1.15rem;
            border: 1px solid rgba(148, 163, 184, 0.28);
            border-radius: 999px;
            background: #ffffff;
            color: #274265;
            letter-spacing: 0.14em;
        }

        .ac-finance-editorial-wrap .ac-services-intro {
            max-width: 48rem;
            margin-right: auto;
            margin-left: auto;
            color: #617184;
        }

        .ac-finance-editorial-wrap .ac-services-divider {
            max-width: 32rem;
            margin: 1.7rem auto 0;
        }

        .ac-finance-editorial-wrap .ac-services-divider-line {
            background: rgba(193, 202, 214, 0.9);
        }

        .ac-finance-editorial-wrap .ac-services-divider-glyph {
            width: 2.55rem;
            height: 2.55rem;
            border: 1px solid rgba(216, 196, 160, 0.45);
            background: #ffffff;
        }

        .ac-finance-editorial-shell {
            display: grid;
            gap: 0;
            margin-top: clamp(1.8rem, 3vw, 2.35rem);
            border-top: 1px solid rgba(216, 196, 160, 0.32);
        }

        .ac-finance-editorial-section {
            padding: clamp(2.2rem, 3vw, 3rem) 0;
            border-bottom: 1px solid rgba(216, 196, 160, 0.32);
        }

        .ac-finance-editorial-section:last-child {
            padding-bottom: 0;
            border-bottom: none;
        }

        .ac-finance-editorial-head {
            display: grid;
            gap: 1.75rem;
            align-items: start;
        }

        .ac-finance-editorial-title {
            display: flex;
            gap: 1.15rem;
            align-items: flex-start;
        }

        .ac-finance-editorial-badge {
            display: grid;
            gap: 0.65rem;
            justify-items: center;
            flex: none;
        }

        .ac-finance-editorial-icon {
            display: inline-flex;
            width: 3.6rem;
            height: 3.6rem;
            flex: none;
            align-items: center;
            justify-content: center;
            border-radius: 18px;
            background: #0f1b2d;
            color: #fff;
            border: 1px solid rgba(15, 27, 45, 0.06);
        }

        .ac-finance-editorial-index {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 2.55rem;
            min-height: 1.65rem;
            padding: 0.2rem 0.55rem;
            border-radius: 999px;
            background: #f4ede0;
            color: #8d6a2d;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.16em;
            text-transform: uppercase;
        }

        .ac-finance-editorial-icon svg {
            width: 1.55rem;
            height: 1.55rem;
        }

        .ac-finance-editorial-heading {
            min-width: 0;
        }

        .ac-finance-editorial-title h2 {
            font-family: 'Playfair Display', serif;
            font-size: clamp(1.6rem, 2.3vw, 2.3rem);
            line-height: 1.15;
            font-weight: 600;
            color: #0f172a;
            text-wrap: balance;
        }

        .ac-finance-editorial-intro {
            max-width: 40rem;
            font-size: 0.98rem;
            line-height: 1.7;
            color: #314050;
            text-wrap: pretty;
        }

        .ac-finance-columns {
            display: grid;
            gap: 1.15rem;
            margin-top: 2rem;
        }

        .ac-finance-columns--two {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .ac-finance-columns--two-wide {
            grid-template-columns: minmax(0, 1.05fr) minmax(0, 0.95fr);
        }

        .ac-finance-columns--three {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .ac-finance-columns--single {
            grid-template-columns: minmax(0, 1fr);
        }

        .ac-finance-column {
            min-width: 0;
            padding: 1.35rem 1.45rem;
            border: 1px solid rgba(216, 196, 160, 0.18);
            border-radius: 1.45rem;
            background: #ffffff;
        }

        .ac-finance-column--spacious {
            max-width: none;
        }

        .ac-finance-column h3 {
            font-size: 1.12rem;
            font-weight: 700;
            line-height: 1.5rem;
            color: #0f172a;
        }

        .ac-finance-column p {
            margin-top: 0.85rem;
            font-size: 0.98rem;
            line-height: 1.72;
            color: #314050;
        }

        .ac-finance-column p + p {
            margin-top: 0.95rem;
        }

        .ac-finance-list {
            margin-top: 1rem;
            display: grid;
            gap: 0.8rem;
            padding-left: 0;
            list-style: none;
            color: #314050;
        }

        .ac-finance-list li,
        .ac-finance-phase-list li {
            display: grid;
            grid-template-columns: auto minmax(0, 1fr);
            gap: 0.72rem;
            align-items: start;
        }

        .ac-finance-list li {
            line-height: 1.7rem;
        }

        .ac-finance-followup {
            margin-top: 1.75rem;
        }

        .ac-finance-phase-table-shell {
            overflow-x: auto;
            border-radius: 1.75rem;
            background: #ffffff;
            border: 1px solid rgba(148, 163, 184, 0.12);
            -webkit-overflow-scrolling: touch;
        }

        .ac-finance-phase-table {
            width: 100%;
            min-width: 44rem;
            border-collapse: collapse;
        }

        .ac-finance-phase-table thead th {
            padding: 1rem 1.25rem;
            background: #f4efe6;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            text-align: left;
            color: #64748b;
        }

        .ac-finance-phase-table tbody tr:nth-child(even) {
            background: #fbfaf7;
        }

        .ac-finance-phase-table tbody th,
        .ac-finance-phase-table tbody td {
            padding: 1.2rem 1.25rem;
            vertical-align: top;
        }

        .ac-finance-phase-step {
            display: inline-flex;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: #9a773d;
        }

        .ac-finance-phase-focus {
            font-size: 1rem;
            font-weight: 700;
            line-height: 1.55;
            color: #0f172a;
        }

        .ac-finance-phase-list {
            display: grid;
            gap: 0.75rem;
            padding-left: 0;
            list-style: none;
            color: #314050;
        }

        .ac-finance-phase-list li {
            line-height: 1.7rem;
        }

        .ac-finance-list-bullet {
            display: inline-flex;
            width: 1.15rem;
            height: 1.15rem;
            align-items: center;
            justify-content: center;
            margin-top: 0.22rem;
            border-radius: 999px;
            background: rgba(216, 196, 160, 0.18);
            color: #9a773d;
        }

        .ac-finance-list-bullet svg {
            width: 0.42rem;
            height: 0.68rem;
        }

        .ac-finance-page .ac-home-blog-card,
        .ac-finance-page .ac-home-blog-card-link,
        .ac-finance-page .ac-finance-column,
        .ac-finance-page .ac-finance-phase-table-shell {
            background: #ffffff;
            box-shadow: none;
        }

        .ac-finance-page #finance-sastanak {
            margin-top: clamp(1.2rem, 2vw, 1.6rem);
        }

        .ac-finance-page .front-contact-input:focus,
        .ac-finance-page .front-contact-textarea:focus {
            box-shadow: none;
            outline: 2px solid rgba(171, 141, 82, 0.22);
            outline-offset: 0;
        }

        @media (min-width: 960px) {
            .ac-finance-editorial-head {
                grid-template-columns: minmax(0, 0.86fr) minmax(0, 1fr);
                gap: 2.4rem;
            }
        }

        @media (max-width: 900px) {
            .ac-finance-columns--two,
            .ac-finance-columns--two-wide,
            .ac-finance-columns--three {
                grid-template-columns: minmax(0, 1fr);
            }
        }

        @media (max-width: 640px) {
            .ac-finance-network-section {
                margin-top: 1.35rem;
                padding-bottom: 1.35rem;
            }

            .ac-finance-editorial-wrap {
                padding: 2.1rem 0;
            }

            .ac-finance-editorial-section {
                padding: 1.7rem 0;
                border-radius: 0;
            }

            .ac-finance-editorial-title {
                align-items: flex-start;
            }

            .ac-finance-editorial-badge {
                gap: 0.45rem;
            }

            .ac-finance-editorial-icon {
                width: 3.15rem;
                height: 3.15rem;
                border-radius: 16px;
            }

            .ac-finance-editorial-icon svg {
                width: 1.35rem;
                height: 1.35rem;
            }

            .ac-finance-column {
                padding: 1.1rem 1.05rem;
            }

            .ac-finance-phase-table {
                min-width: 37rem;
            }

            .ac-finance-phase-table thead th,
            .ac-finance-phase-table tbody th,
            .ac-finance-phase-table tbody td {
                padding-right: 1rem;
                padding-left: 1rem;
            }
        }
    </style>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('front.desktop.contact.partials.form-script', [
    'captchaEnabled' => $captchaEnabled,
    'captchaSiteKey' => $captchaSiteKey,
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php if (! $__env->hasRenderedOnce('4d146da2-4cd3-4f35-82d3-6e8e4ea9b919')): $__env->markAsRenderedOnce('4d146da2-4cd3-4f35-82d3-6e8e4ea9b919'); ?>
    <?php $__env->startPush('scripts'); ?>
        <script defer src="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/js/splide.min.js"></script>
    <?php $__env->stopPush(); ?>
<?php endif; ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        (function () {
            const shouldFocusSection = <?php echo e(($errors->any() || session('status')) ? 'true' : 'false'); ?>;
            const section = document.getElementById('finance-sastanak');

            if (shouldFocusSection && section) {
                requestAnimationFrame(function () {
                    section.scrollIntoView({ behavior: 'smooth', block: 'start' });
                });
            }

            const initFinanceBlogSlider = function () {
                if (typeof window.Splide !== 'function') {
                    return false;
                }

                document.querySelectorAll('[data-finance-blog-splide]').forEach(function (el) {
                    if (el.dataset.splideReady === '1') {
                        return;
                    }

                    el.dataset.splideReady = '1';

                    const count = el.querySelectorAll('.splide__slide').length;
                    const slider = new window.Splide(el, {
                        type: count > 1 ? 'loop' : 'slide',
                        perPage: Math.min(3, Math.max(1, count)),
                        perMove: 1,
                        gap: '1.25rem',
                        drag: count > 1,
                        snap: true,
                        pagination: count > 1,
                        arrows: count > 1,
                        updateOnMove: true,
                        speed: 520,
                        breakpoints: {
                            1180: { perPage: Math.min(2, Math.max(1, count)) },
                            760: { perPage: 1, gap: '1rem' },
                        },
                    });

                    slider.mount();
                });

                return true;
            };

            if (initFinanceBlogSlider()) {
                return;
            }

            let attempts = 0;
            const timer = window.setInterval(function () {
                attempts += 1;
                if (initFinanceBlogSlider() || attempts > 40) {
                    window.clearInterval(timer);
                }
            }, 120);
        }());
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('front.desktop.layouts.store', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/tomek/Herd/info/resources/views/front/desktop/pages/finance.blade.php ENDPATH**/ ?>