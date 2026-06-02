<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($seo)): ?>
        <?php if (isset($component)) { $__componentOriginal08b3fd23a8daa77ece22b0567b1ca51a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal08b3fd23a8daa77ece22b0567b1ca51a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.site.seo-meta','data' => ['seo' => $seo]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('site.seo-meta'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['seo' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($seo)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal08b3fd23a8daa77ece22b0567b1ca51a)): ?>
<?php $attributes = $__attributesOriginal08b3fd23a8daa77ece22b0567b1ca51a; ?>
<?php unset($__attributesOriginal08b3fd23a8daa77ece22b0567b1ca51a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal08b3fd23a8daa77ece22b0567b1ca51a)): ?>
<?php $component = $__componentOriginal08b3fd23a8daa77ece22b0567b1ca51a; ?>
<?php unset($__componentOriginal08b3fd23a8daa77ece22b0567b1ca51a); ?>
<?php endif; ?>
    <?php else: ?>
        <title><?php echo $__env->yieldContent('title', 'مجلة العرب'); ?></title>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;0,900;1,400&family=Cairo:wght@300;400;500;600;700;900&family=Amiri:ital,wght@0,400;0,700;1,400&family=Cinzel:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo e(asset('css/site/sidebar.css')); ?>">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (! (request()->routeIs('home'))): ?>
        <link rel="stylesheet" href="<?php echo e(asset('css/site/chrome.css')); ?>">
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showPreloader ?? false): ?>
        <?php if (isset($component)) { $__componentOriginal628b1b381d9607ec688a201eda9c2190 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal628b1b381d9607ec688a201eda9c2190 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.site.preloader','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('site.preloader'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal628b1b381d9607ec688a201eda9c2190)): ?>
<?php $attributes = $__attributesOriginal628b1b381d9607ec688a201eda9c2190; ?>
<?php unset($__attributesOriginal628b1b381d9607ec688a201eda9c2190); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal628b1b381d9607ec688a201eda9c2190)): ?>
<?php $component = $__componentOriginal628b1b381d9607ec688a201eda9c2190; ?>
<?php unset($__componentOriginal628b1b381d9607ec688a201eda9c2190); ?>
<?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if (isset($component)) { $__componentOriginal7dc9bc45102fcde6d25e4dc75d3eba1c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7dc9bc45102fcde6d25e4dc75d3eba1c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.site.cursor','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('site.cursor'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7dc9bc45102fcde6d25e4dc75d3eba1c)): ?>
<?php $attributes = $__attributesOriginal7dc9bc45102fcde6d25e4dc75d3eba1c; ?>
<?php unset($__attributesOriginal7dc9bc45102fcde6d25e4dc75d3eba1c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7dc9bc45102fcde6d25e4dc75d3eba1c)): ?>
<?php $component = $__componentOriginal7dc9bc45102fcde6d25e4dc75d3eba1c; ?>
<?php unset($__componentOriginal7dc9bc45102fcde6d25e4dc75d3eba1c); ?>
<?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showTicker ?? false): ?>
        <?php if (isset($component)) { $__componentOriginala994668ac9ed5f601c694759a1e44c58 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala994668ac9ed5f601c694759a1e44c58 = $attributes; } ?>
<?php $component = App\View\Components\Site\Ticker::resolve(['label' => $tickerLabel ?? null] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('site.ticker'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\Site\Ticker::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala994668ac9ed5f601c694759a1e44c58)): ?>
<?php $attributes = $__attributesOriginala994668ac9ed5f601c694759a1e44c58; ?>
<?php unset($__attributesOriginala994668ac9ed5f601c694759a1e44c58); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala994668ac9ed5f601c694759a1e44c58)): ?>
<?php $component = $__componentOriginala994668ac9ed5f601c694759a1e44c58; ?>
<?php unset($__componentOriginala994668ac9ed5f601c694759a1e44c58); ?>
<?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if (isset($component)) { $__componentOriginale7973a0cd111432859375f720ac31db5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale7973a0cd111432859375f720ac31db5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.site.header','data' => ['active' => $activeNav ?? null]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('site.header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($activeNav ?? null)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale7973a0cd111432859375f720ac31db5)): ?>
<?php $attributes = $__attributesOriginale7973a0cd111432859375f720ac31db5; ?>
<?php unset($__attributesOriginale7973a0cd111432859375f720ac31db5); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale7973a0cd111432859375f720ac31db5)): ?>
<?php $component = $__componentOriginale7973a0cd111432859375f720ac31db5; ?>
<?php unset($__componentOriginale7973a0cd111432859375f720ac31db5); ?>
<?php endif; ?>

    <?php if (isset($component)) { $__componentOriginal2541342eb26941c726309b2bb5688ad6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2541342eb26941c726309b2bb5688ad6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.site.page-sidebar','data' => ['activeNav' => $activeNav ?? null]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('site.page-sidebar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['active-nav' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($activeNav ?? null)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2541342eb26941c726309b2bb5688ad6)): ?>
<?php $attributes = $__attributesOriginal2541342eb26941c726309b2bb5688ad6; ?>
<?php unset($__attributesOriginal2541342eb26941c726309b2bb5688ad6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2541342eb26941c726309b2bb5688ad6)): ?>
<?php $component = $__componentOriginal2541342eb26941c726309b2bb5688ad6; ?>
<?php unset($__componentOriginal2541342eb26941c726309b2bb5688ad6); ?>
<?php endif; ?>

    <?php echo $__env->yieldContent('content'); ?>

    <?php if (isset($component)) { $__componentOriginal46dbe769f35622233881da74707b8907 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal46dbe769f35622233881da74707b8907 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.site.newsletter','data' => ['eyebrow' => $newsletterEyebrow ?? 'انضم لأكثر من 240,000 مشترك','headline' => $newsletterHeadline ?? null,'sub' => $newsletterSub ?? null]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('site.newsletter'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['eyebrow' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($newsletterEyebrow ?? 'انضم لأكثر من 240,000 مشترك'),'headline' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($newsletterHeadline ?? null),'sub' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($newsletterSub ?? null)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal46dbe769f35622233881da74707b8907)): ?>
<?php $attributes = $__attributesOriginal46dbe769f35622233881da74707b8907; ?>
<?php unset($__attributesOriginal46dbe769f35622233881da74707b8907); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal46dbe769f35622233881da74707b8907)): ?>
<?php $component = $__componentOriginal46dbe769f35622233881da74707b8907; ?>
<?php unset($__componentOriginal46dbe769f35622233881da74707b8907); ?>
<?php endif; ?>

    <?php if (isset($component)) { $__componentOriginal21120ef38d90a9d572330a5268a23b04 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal21120ef38d90a9d572330a5268a23b04 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.site.footer','data' => ['variant' => $footerVariant ?? 'full']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('site.footer'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($footerVariant ?? 'full')]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal21120ef38d90a9d572330a5268a23b04)): ?>
<?php $attributes = $__attributesOriginal21120ef38d90a9d572330a5268a23b04; ?>
<?php unset($__attributesOriginal21120ef38d90a9d572330a5268a23b04); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal21120ef38d90a9d572330a5268a23b04)): ?>
<?php $component = $__componentOriginal21120ef38d90a9d572330a5268a23b04; ?>
<?php unset($__componentOriginal21120ef38d90a9d572330a5268a23b04); ?>
<?php endif; ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
    <script src="<?php echo e(asset('js/site/chrome.js')); ?>"></script>
    <script src="<?php echo e(asset('js/site/sidebar.js')); ?>"></script>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH /home/ali/Downloads/alarab/alarab-laravel-project/alarab-laravel/resources/views/layouts/site.blade.php ENDPATH**/ ?>