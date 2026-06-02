<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'eyebrow' => 'انضم لأكثر من 240,000 مشترك',
    'headline' => null,
    'sub' => null,
]));

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

foreach (array_filter(([
    'eyebrow' => 'انضم لأكثر من 240,000 مشترك',
    'headline' => null,
    'sub' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<section id="newsletter" class="nl-section">
    <div>
        <div class="nl-eyebrow"><?php echo e($eyebrow); ?></div>
        <h2 class="nl-headline"><?php echo $headline ?? 'احصل على مجلة<br><em>العرب</em><br>قبل الجميع.'; ?></h2>
        <p class="nl-sub"><?php echo e($sub ?? 'قوائم الأقوى، قصص الغلاف، تحليلات السوق، والملفات التي تُحرّك الحديث العربي — كل أربعاء في بريدك.'); ?></p>
    </div>
    <div>
        <div class="nl-form">
            <button type="button" class="nl-btn" id="nlBtn">اشترك مجاناً</button>
            <input type="email" class="nl-input" placeholder="أدخل بريدك الإلكتروني" id="nlEmail">
        </div>
        <p style="margin-top:.8rem;font-size:.7rem;color:rgba(248,244,238,.2);text-align:right;font-family:'Cairo',sans-serif;">
            لا رسائل مزعجة. يمكنك إلغاء الاشتراك في أي وقت.
        </p>
    </div>
</section>
<?php /**PATH /home/ali/Downloads/alarab/alarab-laravel-project/alarab-laravel/resources/views/components/site/newsletter.blade.php ENDPATH**/ ?>