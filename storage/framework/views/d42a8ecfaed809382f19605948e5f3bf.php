<?php
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

    $slides = $translation->payload['slides'] ?? $block->payload['slides'] ?? [];

    if (!is_array($slides) || $slides === []) {
        $slides = [[
            'title' => $translation?->title ?? $block->name,
            'subtitle' => $translation?->subtitle ?? '',
            'url' => $translation?->cta_url ?? '#',
            'label' => $translation?->cta_label ?? 'Open',
        ]];
    }
?>

<section class="rounded-2xl border border-slate-200 bg-white p-4 md:p-5">
    <div class="flex snap-x gap-3 overflow-x-auto pb-1">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $slides; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $slide): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $slideUrl = $resolveRouteUrl(
                    (string) ($slide['route'] ?? ''),
                    $slide['route_params'] ?? [],
                    (string) ($slide['url'] ?? '#')
                );
            ?>
            <article class="min-w-[88%] snap-start rounded-xl border border-slate-200 bg-gradient-to-br from-slate-50 to-white p-6 md:min-w-[70%]">
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Slide <?php echo e($loop->iteration); ?></p>
                <h2 class="mt-2 text-2xl font-semibold tracking-tight text-slate-900"><?php echo e($slide['title'] ?? ($translation?->title ?? $block->name)); ?></h2>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($slide['subtitle'])): ?>
                    <p class="mt-2 text-sm text-slate-600"><?php echo e($slide['subtitle']); ?></p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($slideUrl !== ''): ?>
                    <a href="<?php echo e($slideUrl); ?>" class="mt-4 inline-flex rounded-xl bg-slate-900 px-4 py-2 text-xs font-semibold text-white hover:bg-slate-800">
                        <?php echo e($slide['label'] ?? 'Open'); ?>

                    </a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </article>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</section>
<?php /**PATH /Users/tomek/Herd/info/resources/views/front/content-blocks/types/hero_slider.blade.php ENDPATH**/ ?>