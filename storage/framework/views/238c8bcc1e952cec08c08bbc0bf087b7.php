<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['active' => null]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['active' => null]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<?php
    use App\Support\HomeSections;

    $onHome = request()->routeIs('home');
    $items = HomeSections::items();
?>

<header class="masthead">
    <div class="masthead-top">

        <div class="logo-wrap">
            <a href="<?php echo e($onHome ? '#top' : url('/')); ?>" class="logo-link" aria-label="مجلة العرب — AL ARAB">
                <img src="<?php echo e(asset('logo.png')); ?>" alt="AL ARAB" class="site-logo">
            </a>
        </div>
        <div style="display:flex;align-items:center;gap:1.5rem;">

            <a href="<?php echo e($onHome ? '#newsletter' : url('/#newsletter')); ?>" class="btn-subscribe">اشترك الآن</a>
        </div>
    </div>
    <nav class="nav-bar" aria-label="التنقل الرئيسي">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e(HomeSections::href($item, $onHome)); ?>" class="<?php echo \Illuminate\Support\Arr::toCssClasses(['active' => $active === $item['key']]); ?>"
                data-section="<?php echo e($item['id']); ?>"><?php echo e($item['label']); ?></a>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$loop->last): ?>
                <div class="nav-divider"></div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </nav>
</header>
<?php /**PATH /home/ali/Downloads/alarab/alarab-laravel-project/alarab-laravel/resources/views/components/site/header.blade.php ENDPATH**/ ?>