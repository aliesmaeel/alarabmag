
<!-- PAGE HERO -->
<section class="page-hero">
  <div class="page-hero-eyebrow">News · الأخبار</div>
  <h1 class="page-hero-title">نبض <em>العالم العربي</em></h1>
  <p class="page-hero-sub">أحدث الأخبار والتحليلات والتقارير الميدانية من قلب المنطقة العربية — قصص تُحرّك الحديث وتُعيد رسم المشهد.</p>
  <div class="crumb">
    <a href="/">الرئيسية</a>
    <span>›</span>
    <span>الأخبار</span>
  </div>
</section>

<!-- FEATURED ARTICLE -->
<section id="featured" class="feat-hero" style="display:none">
  <div class="feat-hero-grid" id="featuredGrid"></div>
</section>

<!-- FILTERS -->
<div class="sh"><div class="sh-title">تصفّح الأخبار حسب القسم</div><div class="sh-rule"></div><span class="sh-more" id="resultsCount">—</span></div>
<div class="filters" id="filters">
  <button class="chip active" data-category="all">جميع الأقسام</button>
</div>

<!-- LIST -->
<section class="list-section">
  <div class="list-grid" id="newsGrid">
    @if (($initialArticles ?? collect())->isNotEmpty())
      <x-site.article-list-cards :articles="$initialArticles" />
    @else
      <div class="loading">جاري تحميل الأخبار…</div>
    @endif
  </div>
  <div class="load-more-wrap" id="loadMoreWrap" style="display:none">
    <button class="load-more" id="loadMoreBtn">عرض المزيد ↓</button>
  </div>
</section>

