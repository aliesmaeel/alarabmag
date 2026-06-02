const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
const fallbackImg = 'https://images.unsplash.com/photo-1455390582262-044cdead277a?auto=format&fit=crop&w=1400&q=85';
const fallbackAvatar = 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=200&q=80';
const fmtDate = iso => {
  if (!iso) return '';
  const d = new Date(iso.replace(' ','T'));
  return d.toLocaleDateString('ar-EG', { year:'numeric', month:'long', day:'numeric' });
};

function getId(){
  if (window.SITE_ARTICLE_ID != null) return String(window.SITE_ARTICLE_ID);
  const m = location.pathname.match(/\/blogs\/(\d+)/);
  if (m) return m[1];
  return new URLSearchParams(location.search).get('id');
}

function bodyHTML(raw){
  if (!raw) return '<p>لا يوجد محتوى لعرضه.</p>';
  const looksLikeHTML = /<\/?(p|h\d|ul|ol|li|blockquote|img|a|strong|em|br)\b/i.test(raw);
  if (looksLikeHTML) return raw;
  return raw.split(/\n\s*\n/).map(p => `<p>${esc(p.trim()).replace(/\n/g,'<br>')}</p>`).join('');
}

function relatedCardHTML(b){
  return `
    <a href="/blogs/${encodeURIComponent(b.id)}" class="list-card">
      <div class="list-img">
        <img src="${esc(b.image_url || fallbackImg)}" alt="${esc(b.title)}" onerror="this.src='${fallbackImg}'">
      </div>
      <div class="list-body">
        <div class="list-kicker">مدونة · ${esc(b.author || 'فريق التحرير')}</div>
        <h3 class="list-headline">${esc(b.title)}</h3>
        <p class="list-excerpt">${esc(b.excerpt || '')}</p>
        <div class="list-meta">
          <span>${(b.views || 0).toLocaleString('ar-EG')} مشاهدة</span>
          <span><b>${esc(b.author || 'فريق التحرير')}</b></span>
        </div>
      </div>
    </a>`;
}

async function loadRelated(currentId){
  try{
    const res = await fetch('/api/blogs?status=published&limit=10');
    const j = await res.json();
    const items = (j.data || []).filter(b => b.id !== currentId).slice(0,3);
    if (!items.length) return '';
    return `
      <section class="related-section">
        <h2 class="related-title">مقالات <em>قد تعجبك</em></h2>
        <div class="related-grid">${items.map(relatedCardHTML).join('')}</div>
      </section>`;
  }catch{return '';}
}

async function init(){
  const id = getId();
  const main = document.getElementById('main');
  if (!id){
    main.innerHTML = `<div class="notfound"><h1>المقال غير موجود</h1><p>لم نتمكن من العثور على المقال المطلوب.</p><a href="/blogs">العودة إلى المدونات</a></div>`;
    return;
  }
  try{
    const res = await fetch(`/api/blogs/${encodeURIComponent(id)}`);
    if (!res.ok) throw new Error('not found');
    const j = await res.json();
    const b = j.data;
    if (!b) throw new Error('empty');

    document.title = `${b.title} — مجلة العرب`;
    const tags = b.tags ? String(b.tags).split(',').map(s=>s.trim()).filter(Boolean) : [];
    const related = await loadRelated(b.id);

    main.innerHTML = `
      <section class="blog-hero">
        <div class="blog-hero-inner">
          <div class="blog-hero-eyebrow">مدونة العرب</div>
          <h1 class="blog-hero-title">${esc(b.title)}</h1>
          ${b.excerpt ? `<p class="blog-hero-excerpt">${esc(b.excerpt)}</p>` : ''}
          <div class="blog-hero-meta">
            <div class="blog-hero-avatar"><img src="${esc(b.author_img || fallbackAvatar)}" alt="${esc(b.author)}" onerror="this.src='${fallbackAvatar}'"></div>
            <div class="blog-hero-meta-text">
              بقلم <b>${esc(b.author || 'فريق التحرير')}</b><br>
              ${fmtDate(b.created_at)} · ${(b.views || 0).toLocaleString('ar-EG')} مشاهدة
            </div>
          </div>
        </div>
      </section>

      ${b.image_url ? `<div class="blog-cover"><img src="${esc(b.image_url)}" alt="${esc(b.title)}" onerror="this.style.display='none'"></div>` : ''}

      <section class="blog-section">
        <article class="blog-wrap">
          <div class="blog-body">${bodyHTML(b.body)}</div>

          ${tags.length ? `
            <div class="blog-tags">
              ${tags.map(t => `<a class="blog-tag" href="/blogs?tag=${encodeURIComponent(t)}">#${esc(t)}</a>`).join('')}
            </div>` : ''}

          <div class="blog-share">
            <div class="share-label">شارك المقال</div>
            <div class="share-list">
              <a class="share-btn" target="_blank" rel="noopener" href="https://twitter.com/intent/tweet?url=${encodeURIComponent(location.href)}&text=${encodeURIComponent(b.title)}">𝕏</a>
              <a class="share-btn" target="_blank" rel="noopener" href="https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(location.href)}">f</a>
              <a class="share-btn" target="_blank" rel="noopener" href="https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(location.href)}">in</a>
              <a class="share-btn" target="_blank" rel="noopener" href="https://wa.me/?text=${encodeURIComponent(b.title + ' — ' + location.href)}">w</a>
              <button class="share-btn" id="copyBtn" title="نسخ الرابط">⎘</button>
            </div>
          </div>

          ${(b.author_bio || b.author_img) ? `
            <div class="author-card">
              <div class="author-card-img"><img src="${esc(b.author_img || fallbackAvatar)}" alt="${esc(b.author)}" onerror="this.src='${fallbackAvatar}'"></div>
              <div class="author-card-info">
                <div class="author-card-eyebrow">عن الكاتب</div>
                <div class="author-card-name">${esc(b.author || 'فريق التحرير')}</div>
                <p class="author-card-bio">${esc(b.author_bio || 'كاتب في مجلة العرب.')}</p>
              </div>
            </div>` : ''}
        </article>
      </section>

      ${related}
    `;

    const copyBtn = document.getElementById('copyBtn');
    if (copyBtn){
      copyBtn.addEventListener('click', async () => {
        try{ await navigator.clipboard.writeText(location.href); copyBtn.textContent='✓'; setTimeout(()=>copyBtn.textContent='⎘', 1600); }catch{}
      });
    }

    gsap.from('.blog-hero-eyebrow,.blog-hero-title,.blog-hero-excerpt,.blog-hero-meta', {y:30,opacity:0,stagger:.12,duration:.85,ease:'power3.out'});
    gsap.from('.blog-cover img', {opacity:0,scale:1.04,duration:1,ease:'power3.out',delay:.3});
    gsap.from('.blog-body p:first-child', {y:20,opacity:0,duration:.8,ease:'power3.out',delay:.4,scrollTrigger:{trigger:'.blog-section',start:'top 80%'}});
  }catch(e){
    console.error(e);
    main.innerHTML = `<div class="notfound"><h1>المقال غير موجود</h1><p>لم نتمكن من العثور على المقال المطلوب أو ربما تم حذفه.</p><a href="/blogs">العودة إلى المدونات</a></div>`;
  }
}

gsap.registerPlugin(ScrollTrigger);
init();
