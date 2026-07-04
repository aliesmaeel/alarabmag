<section class="page-hero">
    <div class="page-hero-eyebrow">Magazine · المجلة</div>
    <h1 class="page-hero-title">اقرأ <em>المجلة</em></h1>
    <p class="page-hero-sub">تصفّح أعداد مجلة العرب — اختر العدد الذي تريد قراءته واستمتع بالنسخة الرقمية كاملة.</p>
    <div class="crumb">
        <a href="{{ route('home') }}">الرئيسية</a>
        <span>›</span>
        <span>المجلة</span>
    </div>
</section>

<section class="magazine-section">
    @if ($issues->isEmpty())
        <div class="magazine-empty">
            <p>لا توجد أعداد منشورة حالياً.</p>
        </div>
    @else
        <div class="magazine-grid">
            @foreach ($issues as $issue)
                <a href="{{ route('magazine.show', $issue) }}" class="magazine-card">
                    <div class="magazine-card-icon" aria-hidden="true"></div>
                    <h2 class="magazine-card-title">{{ $issue->name }}</h2>
                    <p class="magazine-card-meta">اقرأ العدد ←</p>
                </a>
            @endforeach
        </div>
    @endif
</section>
