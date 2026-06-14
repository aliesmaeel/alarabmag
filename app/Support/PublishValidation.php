<?php

namespace App\Support;

class PublishValidation
{
    public const MIN_BODY_CHARS = 3000;

    public const MIN_BIO_CHARS = 1200;

    public static function bodyLength(?string $body): int
    {
        return mb_strlen(strip_tags($body ?? ''));
    }

    public static function validateBodyForPublish(?string $body): ?string
    {
        if (blank($body)) {
            return 'لا يمكن نشر محتوى بدون نص كامل.';
        }

        if (static::bodyLength($body) < static::MIN_BODY_CHARS) {
            return 'النص الكامل قصير جداً للنشر. يُرجى كتابة مقال بحد أدنى ' . number_format(static::MIN_BODY_CHARS) . ' حرف (حوالي 600–800 كلمة).';
        }

        return null;
    }

    public static function validateBioForPublish(?string $bio): ?string
    {
        if (blank($bio)) {
            return 'لا يمكن نشر ملف شخصي بدون سيرة ذاتية.';
        }

        if (static::bodyLength($bio) < static::MIN_BIO_CHARS) {
            return 'السيرة الذاتية قصيرة جداً للنشر. يُرجى كتابة سيرة بحد أدنى ' . number_format(static::MIN_BIO_CHARS) . ' حرف.';
        }

        return null;
    }
}
