@props([
    'eyebrow' => 'اشترك في النشرة',
    'headline' => null,
    'sub' => null,
])

<section id="newsletter" class="nl-section">
    <div>
        <div class="nl-eyebrow">{{ $eyebrow }}</div>
        <h2 class="nl-headline">{!! $headline ?? 'احصل على مجلة<br><em>العرب</em><br>قبل الجميع.' !!}</h2>
        <p class="nl-sub">{{ $sub ?? 'قوائم الأقوى، قصص الغلاف، تحليلات السوق، والملفات التي تُحرّك الحديث العربي — كل أربعاء في بريدك.' }}</p>
    </div>
    <div>
        <form
            class="nl-form"
            id="nlForm"
            method="POST"
            action="{{ route('newsletter.store') }}"
            data-csrf="{{ csrf_token() }}"
        >
            @csrf
            <button type="submit" class="nl-btn" id="nlBtn">اشترك مجاناً</button>
            <input
                type="email"
                name="email"
                class="nl-input"
                placeholder="أدخل بريدك الإلكتروني"
                id="nlEmail"
                required
                autocomplete="email"
                value="{{ old('email') }}"
            >
        </form>
        <p
            class="nl-status{{ session('newsletter_success') ? '' : ' is-hidden' }}{{ $errors->has('email') ? ' is-error' : '' }}"
            id="nlStatus"
            @if(! session('newsletter_success') && ! $errors->has('email')) hidden @endif
        >{{ session('newsletter_success') ?: $errors->first('email') }}</p>
        <p style="margin-top:.8rem;font-size:.7rem;color:rgba(248,244,238,.2);text-align:right;font-family:'Cairo',sans-serif;">
            لا رسائل مزعجة. يمكنك إلغاء الاشتراك في أي وقت.
        </p>
    </div>
</section>
