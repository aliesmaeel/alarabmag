
<section class="page-hero">
  <div class="page-hero-eyebrow">Business · أعمال</div>
  <h1 class="page-hero-title">الأعمال العربية <em>تُعيد رسم الاقتصاد</em></h1>
  <p class="page-hero-sub">من دبي إلى الرياض وعمّان — قصص رواد الأعمال العرب الذين يبنون إمبراطوريات ويُغيّرون قواعد اللعبة الاقتصادية.</p>
  <div class="crumb"><a href="/">الرئيسية</a><span>›</span><span>الأعمال العربية</span></div>
</section>

<section id="featured" class="feat-doc" style="display:none">
  <div class="feat-doc-grid" id="featuredGrid"></div>
</section>

<div class="sh"><div class="sh-title">رجال الأعمال</div><div class="sh-rule"></div><span class="sh-more" id="leadersCount">—</span></div>
<section class="list-section">
  <div class="doc-list-grid" id="leadersGrid">
    @if (($initialPeople ?? collect())->isNotEmpty())
      <x-site.person-list-cards :people="$initialPeople" category="business" />
    @else
      <div class="loading" style="grid-column:1/-1;">جاري تحميل الملفات…</div>
    @endif
  </div>
</section>

<div class="sh"><div class="sh-title">قصص الأعمال</div><div class="sh-rule"></div><span class="sh-more" id="storiesCount">—</span></div>
<section class="list-section">
  <div class="list-grid" id="storiesGrid">
    @if (($initialArticles ?? collect())->isNotEmpty())
      <x-site.article-list-cards :articles="$initialArticles" />
    @else
      <div class="loading" style="grid-column:1/-1;">جاري تحميل القصص…</div>
    @endif
  </div>
  <div class="load-more-wrap" id="loadMoreWrap" style="display:none">
    <button class="load-more" id="loadMoreBtn">عرض المزيد ↓</button>
  </div>
</section>
