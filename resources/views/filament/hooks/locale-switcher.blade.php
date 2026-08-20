@php
    $locale = app()->getLocale();
    $next = $locale === 'ar' ? 'en' : 'ar';
    $label = $next === 'en' ? 'English' : 'العربية';
    $onLogin = $onLogin ?? false;
@endphp

<div @class(['mb-4 flex justify-end' => $onLogin])>
    <a
        href="{{ route('dashboard.locale', $next) }}"
        class="fi-icon-btn fi-size-md inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1.5 text-sm font-medium text-gray-500 hover:bg-gray-50 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-200"
        title="{{ $label }}"
    >
        <span class="text-xs font-semibold tracking-wide">{{ $locale === 'ar' ? 'AR' : 'EN' }}</span>
        <span>{{ $label }}</span>
    </a>
</div>
