
<!-- PAGE HERO -->
<section class="page-hero">
  <div class="page-hero-eyebrow">Blogs · المدونات</div>
  <h1 class="page-hero-title">أقلام <em>تحكي</em></h1>
  <p class="page-hero-sub">آراء، تجارب، وقصص شخصية من كتّاب ومفكّرين ومبدعين عرب — حيث تلتقي الكلمة بالفكر وتتحوّل القصة إلى مرآة لزماننا.</p>
  <div class="crumb">
    <a href="/">الرئيسية</a>
    <span>›</span>
    <span>المدونات</span>
  </div>
</section>

<!-- FEATURED BLOG -->
<section id="featured" class="feat-blog" style="display:none">
  <div class="feat-blog-grid" id="featuredGrid"></div>
</section>

<!-- TAG FILTERS -->
<div class="sh"><div class="sh-title">استكشف المدونات حسب الموضوع</div><div class="sh-rule"></div><span class="sh-more" id="resultsCount">—</span></div>
<div class="filters" id="filters">
  <button class="chip active" data-tag="all">جميع المواضيع</button>
</div>

<!-- LIST -->
<section class="list-section">
  <div class="list-grid" id="blogsGrid">
    @if (($initialBlogs ?? collect())->isNotEmpty())
      <x-site.blog-list-cards :blogs="$initialBlogs" />
    @else
      <div class="loading">جاري تحميل المدونات…</div>
    @endif
  </div>
  <div class="load-more-wrap" id="loadMoreWrap" style="display:none">
    <button class="load-more" id="loadMoreBtn">عرض المزيد ↓</button>
  </div>
</section>

