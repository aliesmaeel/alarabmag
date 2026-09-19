@extends('layouts.site')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/site/creators.css') }}">
@endpush

@section('content')
    <main id="main" class="creators-page" data-creators-root data-creators-url="{{ route('creators.store') }}"
        data-csrf="{{ csrf_token() }}"
        data-flash="{{ session('creator_success') ?: session('creator_error') }}"
        data-flash-ok="{{ session('creator_success') ? '1' : '0' }}">

        <section class="page-hero creators-hero">
            <div class="page-hero-eyebrow">Creators · معاً لصوت عربي أوسع</div>
            <h1 class="page-hero-title">ندعم صُنّاع المحتوى <em>في الوطن العربي</em></h1>
            <p class="page-hero-sub">
                في مجلة العرب، نؤمن بقوة القصص العربية ونعمل على دعم صُنّاع المحتوى المبدعين في مختلف المجالات
                للوصول إلى فرص أكبر وانتشار أوسع.
            </p>
            <div class="creators-hero-cta">
                <a href="#register" class="creators-btn">
                    سجّل معلوماتك الآن
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.5" aria-hidden="true">
                        <path d="M15 6l-6 6 6 6" />
                    </svg>
                </a>
                <p class="creators-hero-note">سيقوم فريق مجلة العرب بالتواصل مع المُختارين.</p>
            </div>
            <div class="crumb">
                <a href="{{ url('/') }}">الرئيسية</a>
                <span>›</span>
                <span>صنّاع المحتوى</span>
            </div>
        </section>

        {{-- ===== HOW WE SUPPORT ===== --}}
        <section class="creators-support" id="support" aria-labelledby="creators-support-title">
            <div class="creators-sec-head">
                <h2 id="creators-support-title">كيف ندعمك؟</h2>
                <p>نوفر لصُنّاع المحتوى فرصاً حقيقية للنمو والانتشار من خلال:</p>
            </div>
            <div class="creators-features">
                <div class="creators-feature">
                    <div class="creators-feature-icon" aria-hidden="true">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.6">
                            <path d="M4 20h16" />
                            <rect x="6" y="12" width="3" height="8" />
                            <rect x="11" y="8" width="3" height="12" />
                            <rect x="16" y="4" width="3" height="16" />
                        </svg>
                    </div>
                    <h3>دعم النمو والتطوير</h3>
                    <p>تسليط الضوء على المواهب والمحتوى الهادف.</p>
                </div>
                <div class="creators-feature">
                    <div class="creators-feature-icon" aria-hidden="true">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.6">
                            <circle cx="12" cy="12" r="3" />
                            <path
                                d="M19.4 15a1.7 1.7 0 0 0 .3 1.8l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.8-.3 1.7 1.7 0 0 0-1 1.5V21a2 2 0 1 1-4 0v-.1a1.7 1.7 0 0 0-1.1-1.5 1.7 1.7 0 0 0-1.8.3l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1.7 1.7 0 0 0 .3-1.8 1.7 1.7 0 0 0-1.5-1H3a2 2 0 1 1 0-4h.1a1.7 1.7 0 0 0 1.5-1.1 1.7 1.7 0 0 0-.3-1.8l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1a1.7 1.7 0 0 0 1.8.3H9a1.7 1.7 0 0 0 1-1.5V3a2 2 0 1 1 4 0v.1a1.7 1.7 0 0 0 1 1.5 1.7 1.7 0 0 0 1.8-.3l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1.7 1.7 0 0 0-.3 1.8V9a1.7 1.7 0 0 0 1.5 1H21a2 2 0 1 1 0 4h-.1a1.7 1.7 0 0 0-1.5 1z" />
                        </svg>
                    </div>
                    <h3>الفرص والمبادرات</h3>
                    <p>إتاحة فرص للتعاون والمشاركة في مشاريع خاصة.</p>
                </div>
                <div class="creators-feature">
                    <div class="creators-feature-icon" aria-hidden="true">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.6">
                            <circle cx="12" cy="5" r="2.5" />
                            <circle cx="5" cy="18" r="2.5" />
                            <circle cx="19" cy="18" r="2.5" />
                            <path d="M10.8 7.2 6.3 15.8M13.2 7.2l4.5 8.6M7.5 18h9" />
                        </svg>
                    </div>
                    <h3>بناء العلاقات والشراكات</h3>
                    <p>ربطك بجهات وفرص مناسبة لمجالك.</p>
                </div>
                <div class="creators-feature">
                    <div class="creators-feature-icon" aria-hidden="true">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.6">
                            <path d="M3 10v4h3l7 4V6L6 10H3z" />
                            <path d="M6 14l1.5 5h2.5L9 15" />
                            <path d="M17 9a4 4 0 0 1 0 6M19.5 6.5a7.5 7.5 0 0 1 0 11" />
                        </svg>
                    </div>
                    <h3>الترويج والتغطية الإعلامية</h3>
                    <p>إبراز قصصك عبر منصات مجلة العرب.</p>
                </div>
            </div>
        </section>

        {{-- ===== WHO IS IT FOR ===== --}}
        <section class="creators-who" id="who" aria-labelledby="creators-who-title">
            <div class="creators-who-body">
                <h2 id="creators-who-title">لمن هذه المبادرة؟</h2>
                <p class="creators-who-lead">لجميع صُنّاع المحتوى في الوطن العربي، مهما كان مجالك:</p>
                <div class="creators-chips">
                    <div class="creators-chip">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.6" aria-hidden="true">
                            <path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1-1.1a5.5 5.5 0 0 0-7.8 7.8L12 21.2l8.8-8.8a5.5 5.5 0 0 0 0-7.8z" />
                        </svg>أسلوب حياة
                    </div>
                    <div class="creators-chip">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.6" aria-hidden="true">
                            <path d="M17.8 19.2 16 11l3.5-3.5C21 6 21.5 4 21 3c-1-.5-3 0-4.5 1.5L13 8 4.8 6.2c-.5-.1-.9.1-1.1.5l-.3.5c-.2.5-.1 1 .3 1.3L9 12l-2 3H4l-1 1 3 2 2 3 1-1v-3l3-2 3.5 5.3c.3.4.8.5 1.3.3l.5-.2c.4-.3.6-.7.5-1.2z" />
                        </svg>سفر وسياحة
                    </div>
                    <div class="creators-chip">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.6" aria-hidden="true">
                            <rect x="2" y="6" width="20" height="12" rx="6" />
                            <path d="M7 10v4M5 12h4" />
                            <circle cx="16" cy="11" r=".8" fill="currentColor" />
                            <circle cx="18" cy="13" r=".8" fill="currentColor" />
                        </svg>محتوى ترفيهي
                    </div>
                    <div class="creators-chip">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.6" aria-hidden="true">
                            <path d="M2 4h6a4 4 0 0 1 4 4v13a3 3 0 0 0-3-3H2zM22 4h-6a4 4 0 0 0-4 4v13a3 3 0 0 1 3-3h7z" />
                        </svg>محتوى تعليمي
                    </div>
                    <div class="creators-chip">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.6" aria-hidden="true">
                            <circle cx="9" cy="7" r="4" />
                            <path d="M1 21v-2a4 4 0 0 1 4-4h8a4 4 0 0 1 4 4v2M16 3.1a4 4 0 0 1 0 7.8M23 21v-2a4 4 0 0 0-3-3.9" />
                        </svg>محتوى مجتمعي
                    </div>
                    <div class="creators-chip">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.6" aria-hidden="true">
                            <rect x="2" y="7" width="20" height="14" rx="2" />
                            <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16" />
                        </svg>ريادة أعمال
                    </div>
                    <div class="creators-chip">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.6" aria-hidden="true">
                            <circle cx="12" cy="12" r="9" />
                            <path d="M8 9h8M8 12h8M9 15h6" />
                        </svg>فن وموسيقى
                    </div>
                    <div class="creators-chip">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <circle cx="5" cy="12" r="1.8" />
                            <circle cx="12" cy="12" r="1.8" />
                            <circle cx="19" cy="12" r="1.8" />
                        </svg>وغيرها الكثير
                    </div>
                </div>
            </div>
            <div class="creators-who-aside">
                <div class="creators-who-quote">
                    <span class="creators-who-mark" aria-hidden="true">”</span>
                    <p>كل قصة عربية تستحق أن تُروى بصوتها، وأن تصل إلى أبعد مما تتخيّل.</p>
                    <span class="creators-who-sign">هيئة تحرير مجلة العرب</span>
                </div>
            </div>
        </section>

        {{-- ===== REGISTER ===== --}}
        <section class="creators-register" id="register" aria-labelledby="creators-register-title">
            <div class="creators-sec-head">
                <h2 id="creators-register-title">سجّل معلوماتك</h2>
                <p>املأ النموذج التالي، وسيقوم فريق مجلة العرب بالتواصل معك.</p>
            </div>

            <form id="creatorForm" class="creators-form" method="POST" action="{{ route('creators.store') }}"
                novalidate>
                @csrf

                <div class="creators-form-grid">
                    <div class="creators-field">
                        <label for="cf-name">الاسم الكامل<span class="req">*</span></label>
                        <input id="cf-name" name="name" type="text" required autocomplete="name" maxlength="120"
                            value="{{ old('name') }}" @class(['is-invalid' => $errors->has('name')])>
                        <p class="creators-field-error" data-error-for="name" @unless($errors->has('name')) hidden @endunless>{{ $errors->first('name') }}</p>
                    </div>

                    <div class="creators-field">
                        <label for="cf-email">البريد الإلكتروني<span class="req">*</span></label>
                        <input id="cf-email" name="email" type="email" required autocomplete="email" class="ltr"
                            value="{{ old('email') }}">
                        <p class="creators-field-error" data-error-for="email" @unless($errors->has('email')) hidden @endunless>{{ $errors->first('email') }}</p>
                    </div>

                    <div class="creators-field">
                        <label for="cf-country">الدولة<span class="req">*</span></label>
                        <select id="cf-country" name="country" required>
                            <option value="">اختر الدولة</option>
                            @foreach ($countries as $country)
                                <option value="{{ $country }}" @selected(old('country') === $country)>{{ $country }}</option>
                            @endforeach
                        </select>
                        <p class="creators-field-error" data-error-for="country" @unless($errors->has('country')) hidden @endunless>{{ $errors->first('country') }}</p>
                    </div>

                    <div class="creators-field">
                        <label for="cf-phone">رقم التواصل<span class="req">*</span></label>
                        <div class="creators-phone">
                            <select name="dial_code" aria-label="رمز الدولة">
                                @foreach ($dialCodes as $code)
                                    <option value="{{ $code }}" @selected(old('dial_code') === $code)>{{ $code }}</option>
                                @endforeach
                            </select>
                            <input id="cf-phone" name="phone" type="tel" required placeholder="50 123 4567"
                                autocomplete="tel-national" maxlength="32" value="{{ old('phone') }}">
                        </div>
                        <p class="creators-field-error" data-error-for="phone" @unless($errors->has('phone')) hidden @endunless>{{ $errors->first('phone') }}</p>
                    </div>

                    <div class="creators-field">
                        <label for="cf-niche">التخصص / نوع المحتوى<span class="req">*</span></label>
                        <select id="cf-niche" name="niche" required>
                            <option value="">اختر المجال</option>
                            @foreach ($niches as $niche)
                                <option value="{{ $niche }}" @selected(old('niche') === $niche)>{{ $niche }}</option>
                            @endforeach
                        </select>
                        <p class="creators-field-error" data-error-for="niche" @unless($errors->has('niche')) hidden @endunless>{{ $errors->first('niche') }}</p>
                    </div>

                    <div class="creators-field">
                        <label for="cf-followers">عدد المتابعين (تقريبي)</label>
                        <input id="cf-followers" name="followers" type="text" placeholder="100K" class="ltr"
                            maxlength="64" value="{{ old('followers') }}">
                        <p class="creators-field-error" data-error-for="followers" @unless($errors->has('followers')) hidden @endunless>{{ $errors->first('followers') }}</p>
                    </div>

                    @php
                        $oldLinks = array_values(array_filter((array) old('social_links', [''])));
                        $oldLinks = $oldLinks !== [] ? $oldLinks : [''];
                    @endphp
                    <div class="creators-field creators-field-wide" data-social-group>
                        <label for="cf-social-0">روابط حسابات التواصل الاجتماعي<span class="req">*</span></label>
                        <div data-social-rows>
                            @foreach ($oldLinks as $index => $link)
                                <div class="creators-social-row" data-social-row>
                                    <input id="cf-social-{{ $index }}" name="social_links[]" type="url"
                                        class="ltr" maxlength="500" placeholder="https://instagram.com/yourname"
                                        value="{{ $link }}" @if ($loop->first) required @endif>
                                    <button type="button" class="creators-social-remove" data-social-remove
                                        aria-label="حذف الرابط" @if ($loop->first && count($oldLinks) === 1) hidden @endif>
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2" aria-hidden="true">
                                            <path d="M6 6l12 12M18 6L6 18" />
                                        </svg>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                        <button type="button" class="creators-social-add" data-social-add>
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.4" aria-hidden="true">
                                <path d="M12 5v14M5 12h14" />
                            </svg>
                            أضف رابطاً آخر
                        </button>
                        <small class="creators-field-hint">أضف حساباتك على إنستغرام، تيك توك، يوتيوب، إكس… حتى 8 روابط.</small>
                        @php $socialError = $errors->first('social_links') ?: collect($errors->keys())->filter(fn ($key) => str_starts_with($key, 'social_links.'))->map(fn ($key) => $errors->first($key))->first(); @endphp
                        <p class="creators-field-error" data-error-for="social_links" @unless($socialError) hidden @endunless>{{ $socialError }}</p>
                    </div>

                    <div class="creators-field creators-field-wide">
                        <label for="cf-bio">نبذة عنك ومحتواك<span class="req">*</span></label>
                        <textarea id="cf-bio" name="bio" required maxlength="2000"
                            placeholder="أخبرنا أكثر عنك، وما الذي يميز محتواك؟">{{ old('bio') }}</textarea>
                        <p class="creators-field-error" data-error-for="bio" @unless($errors->has('bio')) hidden @endunless>{{ $errors->first('bio') }}</p>
                    </div>
                </div>

                <button type="submit" class="creators-btn creators-submit" data-submit>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" aria-hidden="true">
                        <path d="M22 2 11 13M22 2l-7 20-4-9-9-4z" />
                    </svg>
                    <span data-submit-label>إرسال الطلب</span>
                </button>
                <p class="creators-privacy">جميع البيانات ستُعامل بسرية تامة.</p>
                @php $flash = session('creator_success') ?: session('creator_error'); @endphp
                <div class="creators-msg @if (session('creator_error') || $errors->any()) is-error @endif"
                    data-form-msg role="status" aria-live="polite"
                    @unless($flash || $errors->any()) hidden @endunless>{{ $flash ?: ($errors->any() ? 'يرجى تعبئة الحقول المطلوبة بشكل صحيح.' : '') }}</div>
            </form>
        </section>
    </main>
@endsection

@push('scripts')
    <script src="{{ asset('js/site/creators.js') }}" defer></script>
@endpush
