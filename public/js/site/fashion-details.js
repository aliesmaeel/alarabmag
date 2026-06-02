const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
const fallbackImg = 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?auto=format&fit=crop&w=1400&q=85';
const fmtDate = iso => {
  if (!iso) return '';
  const d = new Date(iso.replace(' ','T'));
  return d.toLocaleDateString('ar-EG', { year:'numeric', month:'long', day:'numeric' });
};

function getId(){
  if (window.SITE_ARTICLE_ID != null) return String(window.SITE_ARTICLE_ID);
  const m = location.pathname.match(/\/fashion\/(\d+)/);
  if (m) return m[1];
  return new URLSearchParams(location.search).get('id');
}

function bodyHTML(raw){
  if (!raw) return '<p>لا يوجد محتوى لعرضه.</p>';
  const looksLikeHTML = /<\/?(p|h\d|ul|ol|li|blockquote|img|a|strong|em|br)\b/i.test(raw);
  if (looksLikeHTML) return raw;
  return raw.split(/\n\s*\n/).map(p => `<p>${esc(p.trim()).replace(/\n/g,'<br>')}</p>`).join('');
}

function relatedCardHTML(it){
  const kicker = it.subtitle || it.region || 'موضة';
  return `
    <a href="/fashion/${encodeURIComponent(it.id)}" class="list-card">
      <div class="list-img">
        <img src="${esc(it.image_url || fallbackImg)}" alt="${esc(it.title)}" onerror="this.src='${fallbackImg}'">
      </div>
      <div class="list-body">
        <div class="list-kicker">${esc(kicker)}</div>
        <h3 class="list-headline">${esc(it.title)}</h3>
        <p class="list-excerpt">${esc(it.excerpt || '')}</p>
        <div class="list-meta">
          <span>${esc(it.read_time || '6 دقائق')}</span>
          <span><b>${esc(it.author || 'فريق التحرير')}</b></span>
        </div>
      </div>
    </a>`;
}

async function loadRelated(article){
  try{
    const res = await fetch('/api/articles?category=' + encodeURIComponent('موضة') + '&status=published&limit=10');
    const j = await res.json();
    const items = (j.data || []).filter(a => a.id !== article.id).slice(0,3);
    if (!items.length) return '';
    return `
      <section class="related-section">
        <h2 class="related-title">موضة <em>ذات صلة</em></h2>
        <div class="related-grid">${items.map(relatedCardHTML).join('')}</div>
      </section>`;
  }catch{return '';}
}

async function init(){
  const id = getId();
  const main = document.getElementById('main');
  if (!id){
    main.innerHTML = `<div class="notfound"><h1>التقرير غير موجود</h1><p>لم نتمكن من العثور على التقرير المطلوب.</p><a href="/fashion">العودة إلى الموضة</a></div>`;
    return;
  }

  try{
    const res = await fetch(`/api/articles/${encodeURIComponent(id)}`);
    if (!res.ok) throw new Error('not found');
    const j = await res.json();
    const a = j.data;
    if (!a || a.category !== 'موضة') throw new Error('wrong category');

    document.title = `${a.title} — مجلة العرب`;

    const heroImg = a.image_url || fallbackImg;
    const related = await loadRelated(a);

    main.innerHTML = `
      <section class="article-hero">
        <img src="${esc(heroImg)}" alt="${esc(a.title)}" onerror="this.src='${fallbackImg}'">
        <div class="article-hero-overlay"></div>
        <div class="article-hero-inner">
          <div class="article-kicker">✦ ${esc(a.subtitle || 'الموضة العربية')}${a.region ? ' · ' + esc(a.region) : ''}</div>
          <h1 class="article-title">${esc(a.title)}</h1>
          <div class="article-byline">
            <span>بقلم <b>${esc(a.author || 'فريق التحرير')}</b></span>
            <span class="byline-sep">·</span>
            <span>${fmtDate(a.created_at)}</span>
            <span class="byline-sep">·</span>
            <span>${esc(a.read_time || '6 دقائق')} للقراءة</span>
          </div>
        </div>
      </section>

      <section class="article-section">
        <article class="article-wrap">
          ${a.excerpt ? `<p class="article-lede">${esc(a.excerpt)}</p>` : ''}
          <div class="article-body">${bodyHTML(a.body)}</div>

          <div class="article-share">
            <div class="share-label">شارك التقرير</div>
            <div class="share-list">
              <a class="share-btn" target="_blank" rel="noopener" href="https://twitter.com/intent/tweet?url=${encodeURIComponent(location.href)}&text=${encodeURIComponent(a.title)}">𝕏</a>
              <a class="share-btn" target="_blank" rel="noopener" href="https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(location.href)}">f</a>
              <button class="share-btn" id="copyBtn" title="نسخ الرابط">⎘</button>
            </div>
          </div>
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

    gsap.from('.article-kicker,.article-title,.article-byline', {y:30,opacity:0,stagger:.12,duration:.85,ease:'power3.out'});
  }catch(e){
    console.error(e);
    main.innerHTML = `<div class="notfound"><h1>التقرير غير موجود</h1><p>لم نتمكن من العثور على التقرير المطلوب.</p><a href="/fashion">العودة إلى الموضة</a></div>`;
  }
}

gsap.registerPlugin(ScrollTrigger);
init();
