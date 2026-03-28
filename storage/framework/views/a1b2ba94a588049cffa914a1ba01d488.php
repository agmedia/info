<?php
    $basePayload = is_array($block->payload ?? null) ? $block->payload : [];
    $translationPayload = is_array($translation?->payload ?? null) ? $translation->payload : [];
    $payload = array_merge($basePayload, $translationPayload);

    $sectionClass = (string) ($payload['section_class'] ?? 'relative overflow-hidden rounded-3xl border border-slate-200/70 p-8 md:p-12');
    $contentClass = (string) ($payload['content_class'] ?? 'relative z-10 max-w-3xl');
    $textClass = (string) ($payload['text_class'] ?? 'text-slate-900');
    $titleClass = (string) ($payload['title_class'] ?? 'text-4xl font-extrabold tracking-tight md:text-5xl');
    $subtitleClass = (string) ($payload['subtitle_class'] ?? 'mt-4 text-lg text-slate-700');
    $ctaClass = trim('front-link-cta '.(string) ($payload['cta_class'] ?? 'mt-8 inline-flex rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white hover:bg-slate-800'));
    $overlayClass = (string) ($payload['overlay_class'] ?? 'absolute inset-0 bg-gradient-to-br from-white/80 via-white/60 to-white/40');
    $bgCss = trim((string) ($payload['bg_css'] ?? ''));

    $backgroundUrl = $block->getFirstMediaUrl('block_background', 'hero_1440x480');
    if ($backgroundUrl === '') {
        $backgroundUrl = $block->getFirstMediaUrl('block_background');
    }

    $bgStyle = $bgCss;
    if ($backgroundUrl !== '') {
        $bgImageCss = "background-image:url('".e($backgroundUrl)."');background-size:cover;background-position:center;";
        $bgStyle = trim($bgImageCss.' '.$bgCss);
    }

    $allowedRoutes = config('content_blocks.route_whitelist', []);
    $resolveRouteUrl = function (?string $routeName, mixed $routeParams, string $fallbackUrl = '#') use ($allowedRoutes): string {
        $name = trim((string) $routeName);
        if ($name === '') {
            return $fallbackUrl;
        }
        $isAllowed = $allowedRoutes === []
            || collect($allowedRoutes)->contains(fn ($pattern) => \Illuminate\Support\Str::is((string) $pattern, $name));
        if (! $isAllowed || !\Illuminate\Support\Facades\Route::has($name)) {
            return $fallbackUrl;
        }
        $params = is_array($routeParams) ? $routeParams : [];
        try {
            return route($name, $params);
        } catch (\Throwable) {
            return $fallbackUrl;
        }
    };

    $ctaLabel = (string) ($translation?->cta_label ?? '');
    $ctaFallbackUrl = (string) ($translation?->cta_url ?? '#');
    $ctaRoute = (string) ($payload['cta_route'] ?? '');
    $ctaRouteParams = $payload['cta_route_params'] ?? [];
    $ctaUrl = $resolveRouteUrl($ctaRoute, $ctaRouteParams, $ctaFallbackUrl);
?>

<section class="<?php echo e($sectionClass); ?>" <?php if($bgStyle !== ''): ?> style="<?php echo e($bgStyle); ?>" <?php endif; ?>>
    <div class="<?php echo e($overlayClass); ?>"></div>
    <div class="<?php echo e($contentClass); ?> <?php echo e($textClass); ?>">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($translation?->title)): ?>
            <h2 class="<?php echo e($titleClass); ?>"><?php echo nl2br(e($translation->title)); ?></h2>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($translation?->subtitle)): ?>
            <p class="<?php echo e($subtitleClass); ?>"><?php echo e($translation->subtitle); ?></p>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ctaLabel !== '' && $ctaUrl !== ''): ?>
            <a href="<?php echo e($ctaUrl); ?>" class="<?php echo e($ctaClass); ?>"><?php echo e($ctaLabel); ?></a>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</section>
<?php /**PATH /Users/tomek/Herd/info/resources/views/front/content-blocks/types/banner.blade.php ENDPATH**/ ?>