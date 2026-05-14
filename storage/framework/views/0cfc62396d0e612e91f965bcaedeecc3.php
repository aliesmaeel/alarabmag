<div
    <?php echo e($attributes
            ->merge([
                'id' => $getId(),
            ], escape: false)
            ->merge($getExtraAttributes(), escape: false)); ?>

>
    <?php echo e($getChildComponentContainer()); ?>

</div>
<?php /**PATH /home/ali/Downloads/alarab/alarab-laravel-project/alarab-laravel/vendor/filament/forms/resources/views/components/group.blade.php ENDPATH**/ ?>