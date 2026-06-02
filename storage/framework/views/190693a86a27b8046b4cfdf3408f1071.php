<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['variant' => 'full']));

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

foreach (array_filter((['variant' => 'full']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<footer>
    <div class="footer-top">
        <div style="text-align:right;">
            <a href="<?php echo e(url('/')); ?>" class="footer-logo-link" aria-label="مجلة العرب — AL ARAB">
                <img src="<?php echo e(asset('logo.png')); ?>" alt="AL ARAB" class="footer-logo-img">
            </a>
            <p class="footer-about">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($variant === 'full'): ?>
                    المجلة العربية الأولى التي تحتفي بالإنسان العربي المتميّز في كل مكان. نحكي قصص المؤثرين والفنانين والأطباء ورواد الأعمال الذين يُلهمون الأمة. صادرة من دبي، للعالم العربي.
                <?php else: ?>
                    المجلة العربية الأولى التي تحتفي بالإنسان العربي المتميّز في كل مكان.
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </p>
            <div class="footer-socials">
                <a href="#" class="fsoc">𝕏</a>
                <a href="#" class="fsoc">in</a>
                <a href="#" class="fsoc">📷</a>
                <a href="#" class="fsoc">▶</a>
                <a href="#" class="fsoc">📘</a>
            </div>
        </div>
        <div class="fcol">
            <div class="fcol-title">الأقسام</div>
            <ul>
                <li><a href="<?php echo e(url('/influencers')); ?>">المؤثرون العرب</a></li>
                <li><a href="<?php echo e(url('/artists')); ?>">الفنانون العرب</a></li>
                <li><a href="<?php echo e(route('business.index')); ?>">الأعمال العربية</a></li>
                <li><a href="<?php echo e(url('/doctors')); ?>">أطباء عرب</a></li>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($variant === 'full'): ?>
                    <li><a href="<?php echo e(route('fashion.index')); ?>">الموضة العربية</a></li>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <li><a href="<?php echo e(url('/news')); ?>">الأخبار</a></li>
                <li><a href="<?php echo e(url('/blogs')); ?>">المدونات</a></li>
            </ul>
        </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($variant === 'full'): ?>
            <div class="fcol">
                <div class="fcol-title">المناطق</div>
                <ul>
                    <li><a href="#">الإمارات</a></li>
                    <li><a href="#">السعودية</a></li>
                    <li><a href="#">قطر</a></li>
                    <li><a href="#">الكويت</a></li>
                    <li><a href="#">لبنان</a></li>
                    <li><a href="#">مصر</a></li>
                    <li><a href="#">الأردن</a></li>
                    <li><a href="#">العراق</a></li>
                </ul>
            </div>
            <div class="fcol">
                <div class="fcol-title">المجلة</div>
                <ul>
                    <li><a href="#">عن العرب</a></li>
                    <li><a href="#">هيئة التحرير</a></li>
                    <li><a href="#">الإعلان معنا</a></li>
                    <li><a href="#">الترشيح للمجلة</a></li>
                    <li><a href="#">الفعاليات</a></li>
                    <li><a href="#">وظائف</a></li>
                </ul>
            </div>
            <div class="fcol">
                <div class="fcol-title">الاشتراك</div>
                <ul>
                    <li><a href="#">النشرة المجانية</a></li>
                    <li><a href="#">الاشتراك الرقمي</a></li>
                    <li><a href="#">الطبعة الورقية</a></li>
                    <li><a href="#">اشتراك المؤسسات</a></li>
                    <li><a href="#">هدية الاشتراك</a></li>
                    <li><a href="#">الأعداد السابقة</a></li>
                </ul>
            </div>
        <?php else: ?>
            <div class="fcol">
                <div class="fcol-title">المجلة</div>
                <ul>
                    <li><a href="#">عن العرب</a></li>
                    <li><a href="#">هيئة التحرير</a></li>
                    <li><a href="#">الإعلان معنا</a></li>
                </ul>
            </div>
            <div class="fcol">
                <div class="fcol-title">الاشتراك</div>
                <ul>
                    <li><a href="#">النشرة المجانية</a></li>
                    <li><a href="#">الطبعة الورقية</a></li>
                </ul>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
    <div class="footer-bottom">
        <div class="flegal">
            <a href="#">سياسة الخصوصية</a>
            <a href="#">شروط الاستخدام</a>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($variant === 'full'): ?>
                <a href="#">إعدادات الكوكيز</a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <div class="fcopy">© 2026 مجلة العرب. جميع الحقوق محفوظة.<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($variant === 'full'): ?> مدينة دبي للإعلام، الإمارات العربية المتحدة.<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
    </div>
</footer>
<?php /**PATH /home/ali/Downloads/alarab/alarab-laravel-project/alarab-laravel/resources/views/components/site/footer.blade.php ENDPATH**/ ?>