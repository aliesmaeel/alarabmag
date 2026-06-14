
<!-- PAGE HERO -->
<section class="page-hero">
  <div class="page-hero-eyebrow">Doctors · أطباء</div>
  <h1 class="page-hero-title">أطباء عرب <em>يُعالجون العالم</em></h1>
  <p class="page-hero-sub">من مايو كلينيك إلى مستشفى رويال مارسدن، أطباء عرب يقودون الأبحاث الطبية ويُحدثون فرقاً حقيقياً في حياة المرضى حول العالم.</p>
  <div class="crumb"><a href="/">الرئيسية</a><span>›</span><span>أطباء عرب</span></div>
</section>

<!-- FEATURED DOCTOR -->
<section id="featured" class="feat-doc" style="display:none">
  <div class="feat-doc-grid" id="featuredGrid"></div>
</section>

<!-- FILTERS -->
<div class="sh"><div class="sh-title">تصفّح حسب التخصص</div><div class="sh-rule"></div><span class="sh-more" id="resultsCount">—</span></div>
<div class="filters" id="filters">
  <button class="chip active" data-spec="all">جميع التخصصات</button>
</div>

<!-- LIST -->
<section class="list-section">
  <div class="doc-list-grid" id="docsGrid">
    @if (($initialPeople ?? collect())->isNotEmpty())
      <x-site.person-list-cards :people="$initialPeople" category="doctor" />
    @else
      <div class="loading" style="grid-column:1/-1;">جاري تحميل الأطباء…</div>
    @endif
  </div>
  <div class="load-more-wrap" id="loadMoreWrap" style="display:none">
    <button class="load-more" id="loadMoreBtn">عرض المزيد ↓</button>
  </div>
</section>

