<div class="ticker-bar">
    <div class="ticker-label"><?php echo e($label); ?></div>
    <div class="ticker-wrap">
        <div class="ticker-track" id="tickerTrack">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = [false, true]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $duplicate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div style="display:flex;gap:4rem;flex-shrink:0" <?php if($duplicate): ?> aria-hidden="true" <?php endif; ?>>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $entries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <a href="<?php echo e($entry['url']); ?>" class="ticker-item">
                            <b><?php echo e($entry['kicker']); ?>:</b> <?php echo e($entry['title']); ?>

                        </a>
                        <span class="ticker-sep">◆</span>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <span class="ticker-item"><b>مجلة العرب:</b> تابع آخر الأخبار من فريق التحرير</span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
</div>
<?php /**PATH /home/ali/Downloads/alarab/alarab-laravel-project/alarab-laravel/resources/views/components/site/ticker-bar.blade.php ENDPATH**/ ?>