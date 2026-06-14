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
        <div class="nl-form">
            <button type="button" class="nl-btn" id="nlBtn">اشترك مجاناً</button>
            <input type="email" class="nl-input" placeholder="أدخل بريدك الإلكتروني" id="nlEmail">
        </div>
        <p style="margin-top:.8rem;font-size:.7rem;color:rgba(248,244,238,.2);text-align:right;font-family:'Cairo',sans-serif;">
            لا رسائل مزعجة. يمكنك إلغاء الاشتراك في أي وقت.
        </p>
    </div>
</section>
