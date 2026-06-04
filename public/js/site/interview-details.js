const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
const fallbackImg = 'https://images.unsplash.com/photo-1611162616475-46b635cb6868?auto=format&fit=crop&w=1400&q=85';
const fmtDate = iso => {
  if (!iso) return '';
  const d = new Date(iso.replace(' ','T'));
  return d.toLocaleDateString('ar-EG', { year:'numeric', month:'long', day:'numeric' });
};

function getSlug(){
  if (window.SITE_INTERVIEW_SLUG) return String(window.SITE_INTERVIEW_SLUG);
  const m = location.pathname.match(/\/interviews\/([^/]+)/);
  if (m) return decodeURIComponent(m[1]);
  return null;
}

function videoEmbed(url){
  if (!url) return '';
  const u = String(url).trim();

  let yt = u.match(/(?:youtube\.com\/(?:watch\?v=|embed\/|shorts\/)|youtu\.be\/)([\w-]{11})/i);
  if (yt) {
    return `<div class="video-wrap"><iframe src="https://www.youtube.com/embed/${esc(yt[1])}" title="YouTube video" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen loading="lazy"></iframe></div>`;
  }

  let vimeo = u.match(/vimeo\.com\/(?:video\/)?(\d+)/i);
  if (vimeo) {
    return `<div class="video-wrap"><iframe src="https://player.vimeo.com/video/${esc(vimeo[1])}" title="Vimeo video" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen loading="lazy"></iframe></div>`;
  }

  if (/\.(mp4|webm|ogg)(\?|$)/i.test(u)) {
    return `<div class="video-wrap"><video controls playsinline preload="metadata" poster="${fallbackImg}"><source src="${esc(u)}"></video></div>`;
  }

  return `<div class="video-wrap video-fallback"><a href="${esc(u)}" target="_blank" rel="noopener">شاهد الفيديو على المنصة الأصلية ←</a></div>`;
}

function relatedCardHTML(item){
  return `
    <a href="/interviews/${encodeURIComponent(item.slug)}" class="list-card interview-card">
      <div class="list-img">
        <img src="${esc(item.thumbnail_url || fallbackImg)}" alt="${esc(item.title)}" onerror="this.src='${fallbackImg}'">
        <span class="interview-play">▶</span>
      </div>
      <div class="list-body">
        <div class="list-kicker">مقابلة · ${esc(item.category || 'عام')}</div>
        <h3 class="list-headline">${esc(item.title)}</h3>
        <p class="list-excerpt">${esc(item.description || '')}</p>
      </div>
    </a>`;
}

async function loadRelated(currentSlug){
  try{
    const res = await fetch('/api/interviews?status=published&limit=10');
    const j = await res.json();
    const items = (j.data || []).filter(i => i.slug !== currentSlug).slice(0,3);
    if (!items.length) return '';
    return `
      <section class="related-section">
        <h2 class="related-title">مقابلات <em>أخرى</em></h2>
        <div class="related-grid">${items.map(relatedCardHTML).join('')}</div>
      </section>`;
  }catch{return '';}
}

async function init(){
  const slug = getSlug();
  const main = document.getElementById('main');
  if (!slug){
    main.innerHTML = `<div class="notfound"><h1>المقابلة غير موجودة</h1><p>لم نتمكن من العثور على المقابلة المطلوبة.</p><a href="/interviews">العودة إلى المقابلات</a></div>`;
    return;
  }
  try{
    const res = await fetch(`/api/interviews/${encodeURIComponent(slug)}`);
    if (!res.ok) throw new Error('not found');
    const j = await res.json();
    const item = j.data;
    if (!item) throw new Error('empty');

    document.title = `${item.title} — مجلة العرب`;
    const related = await loadRelated(item.slug);

    main.innerHTML = `
      <section class="interview-hero">
        <div class="interview-hero-inner">
          <div class="interview-hero-eyebrow">مقابلة · ${esc(item.category || 'عام')}</div>
          <h1 class="interview-hero-title">${esc(item.title)}</h1>
          ${item.description ? `<p class="interview-hero-excerpt">${esc(item.description)}</p>` : ''}
          <div class="interview-hero-meta">
            ${fmtDate(item.created_at)} · ${(item.views || 0).toLocaleString('ar-EG')} مشاهدة
          </div>
        </div>
      </section>

      <section class="interview-video-section">
        <div class="interview-wrap">
          ${videoEmbed(item.video_url)}
        </div>
      </section>

      ${item.description ? `
      <section class="interview-desc-section">
        <article class="interview-wrap">
          <h2 class="interview-desc-title">عن المقابلة</h2>
          <div class="interview-desc-body">${esc(item.description).replace(/\n/g,'<br>')}</div>
          <div class="interview-share">
            <div class="share-label">شارك المقابلة</div>
            <div class="share-list">
              <a class="share-btn" target="_blank" rel="noopener" href="https://twitter.com/intent/tweet?url=${encodeURIComponent(location.href)}&text=${encodeURIComponent(item.title)}">𝕏</a>
              <a class="share-btn" target="_blank" rel="noopener" href="https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(location.href)}">f</a>
              <a class="share-btn" target="_blank" rel="noopener" href="https://wa.me/?text=${encodeURIComponent(item.title + ' — ' + location.href)}">w</a>
              <button class="share-btn" id="copyBtn" title="نسخ الرابط">⎘</button>
            </div>
          </div>
        </article>
      </section>` : ''}

      ${related}
    `;

    const copyBtn = document.getElementById('copyBtn');
    if (copyBtn){
      copyBtn.addEventListener('click', async () => {
        try{ await navigator.clipboard.writeText(location.href); copyBtn.textContent='✓'; setTimeout(()=>copyBtn.textContent='⎘', 1600); }catch{}
      });
    }

    if (typeof gsap !== 'undefined'){
      gsap.from('.interview-hero-eyebrow,.interview-hero-title,.interview-hero-excerpt,.interview-hero-meta', {y:30,opacity:0,stagger:.12,duration:.85,ease:'power3.out'});
      gsap.from('.video-wrap', {opacity:0,y:30,duration:1,ease:'power3.out',delay:.25});
    }
  }catch(e){
    console.error(e);
    main.innerHTML = `<div class="notfound"><h1>المقابلة غير موجودة</h1><p>لم نتمكن من العثور على المقابلة المطلوبة أو ربما تم حذفها.</p><a href="/interviews">العودة إلى المقابلات</a></div>`;
  }
}

if (typeof gsap !== 'undefined') {
  if (typeof ScrollTrigger !== 'undefined') gsap.registerPlugin(ScrollTrigger);
  init();
} else {
  document.addEventListener('DOMContentLoaded', init);
}
