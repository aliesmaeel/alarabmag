<?php

namespace App\Support;

class PublishValidation
{
    public const MIN_BODY_CHARS = 1500;

    public const MIN_BIO_CHARS = 1200;

    public static function bodyLength(?string $body): int
    {
        return mb_strlen(strip_tags($body ?? ''));
    }

    public static function validateBodyForPublish(?string $body): ?string
    {
        if (blank($body)) {
            return __('لا يمكن نشر محتوى بدون نص كامل.');
        }

        if (static::bodyLength($body) < static::MIN_BODY_CHARS) {
            return __('النص الكامل قصير جداً للنشر. يُرجى كتابة مقال بحد أدنى :count حرف (حوالي 300–400 كلمة).', [
                'count' => number_format(static::MIN_BODY_CHARS),
            ]);
        }

        return null;
    }

    public static function validateBioForPublish(?string $bio): ?string
    {
        if (blank($bio)) {
            return __('لا يمكن نشر ملف شخصي بدون سيرة ذاتية.');
        }

        if (static::bodyLength($bio) < static::MIN_BIO_CHARS) {
            return __('السيرة الذاتية قصيرة جداً للنشر. يُرجى كتابة سيرة بحد أدنى :count حرف.', [
                'count' => number_format(static::MIN_BIO_CHARS),
            ]);
        }

        return null;
    }
}
