<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['activeNav' => null]));

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

foreach (array_filter((['activeNav' => null]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
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
    $items = HomeSections::forSidebar($onHome);
?>

<aside class="page-sidebar" id="pageSidebar" aria-label="تنقل الأقسام">
    <div class="page-sidebar-head">
        <div class="page-sidebar-label">الأقسام</div>
        <button
            type="button"
            class="page-sidebar-collapse"
            id="sidebarCollapse"
            aria-label="طي القائمة"
            aria-expanded="true"
            title="طي القائمة"
        >
            <span aria-hidden="true">‹</span>
        </button>
    </div>
    <nav class="page-sidebar-nav">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a
                href="<?php echo e($item['href']); ?>"
                class="page-sidebar-link <?php if($activeNav === $item['key']): ?> active <?php endif; ?>"
                <?php if($item['is_anchor']): ?> data-section="<?php echo e($item['id']); ?>" <?php endif; ?>
                title="<?php echo e($item['label']); ?>"
            >
                <span class="page-sidebar-dot"></span>
                <span class="page-sidebar-text"><?php echo e($item['label']); ?></span>
            </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </nav>
</aside>

<button
    type="button"
    class="page-sidebar-expand"
    id="sidebarExpand"
    aria-label="إظهار القائمة"
    title="إظهار القائمة"
    hidden
>
    <span aria-hidden="true">›</span>
</button>

<button type="button" class="page-sidebar-toggle" id="sidebarToggle" aria-label="فتح قائمة الأقسام">
    <span></span><span></span><span></span>
</button>
<div class="page-sidebar-backdrop" id="sidebarBackdrop"></div>
<?php /**PATH /home/ali/Downloads/alarab/alarab-laravel-project/alarab-laravel/resources/views/components/site/page-sidebar.blade.php ENDPATH**/ ?>