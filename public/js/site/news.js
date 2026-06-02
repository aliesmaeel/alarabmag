const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
const fallbackImg = 'https://images.unsplash.com/photo-1495020689067-958852a7765e?auto=format&fit=crop&w=900&q=80';
const fmtAgo = iso => {
  if (!iso) return '';
  const d = new Date(iso.replace(' ','T'));
  const diff = (Date.now() - d.getTime())/1000;
  if (diff < 3600) return `منذ ${Math.max(1,Math.floor(diff/60))} دقيقة`;
  if (diff < 86400) return `منذ ${Math.floor(diff/3600)} ساعة`;
  if (diff < 2592000) return `منذ ${Math.floor(diff/86400)} يوم`;
  return d.toISOString().slice(0,10);
};
const params = new URLSearchParams(location.search);
const initialCategory = params.get('category') || 'all';
const initialRegion   = params.get('region') || '';

let state = { category: initialCategory, region: initialRegion, offset: 0, pageSize: 9, total: 0, loading: false, exhausted: false };

const grid = document.getElementById('newsGrid');
const featuredSection = document.getElementById('featured');
const featuredGrid = document.getElementById('featuredGrid');
const filtersEl = document.getElementById('filters');
const resultsCountEl = document.getElementById('resultsCount');
const loadMoreWrap = document.getElementById('loadMoreWrap');
const loadMoreBtn = document.getElementById('loadMoreBtn');

function cardHTML(it){
  const region = it.region ? ` · ${esc(it.region)}` : '';
  return `
    <a href="/news/${encodeURIComponent(it.id)}" class="list-card">
      <div class="list-img">
        <img src="${esc(it.image_url || fallbackImg)}" alt="${esc(it.title)}" onerror="this.src='${fallbackImg}'">
        ${it.featured ? '<span class="list-img-badge">مميّز</span>' : ''}
      </div>
      <div class="list-body">
        <div class="list-kicker">${esc(it.category || 'عام')}${region}</div>
        <h3 class="list-headline">${esc(it.title)}</h3>
        <p class="list-excerpt">${esc(it.excerpt || it.subtitle || '')}</p>
        <div class="list-meta">
          <span>${esc(it.read_time || '5 دقائق')}</span>
          <span><b>${esc(it.author || 'فريق التحرير')}</b> · ${fmtAgo(it.created_at)}</span>
        </div>
      </div>
    </a>`;
}

function featuredHTML(it){
  return `
    <div class="feat-hero-img">
      <img src="${esc(it.image_url || fallbackImg)}" alt="${esc(it.title)}" onerror="this.src='${fallbackImg}'">
    </div>
    <div class="feat-hero-body">
      <div class="feat-hero-kicker">✦ قصة الغلاف · ${esc(it.category || 'عام')}</div>
      <h2 class="feat-hero-title"><a href="/news/${encodeURIComponent(it.id)}">${esc(it.title)}</a></h2>
      ${it.subtitle ? `<p class="feat-hero-deck" style="font-style:italic;">${esc(it.subtitle)}</p>` : ''}
      <p class="feat-hero-deck">${esc(it.excerpt || '')}</p>
      <div class="feat-hero-meta">بقلم <b>${esc(it.author || 'فريق التحرير')}</b> · ${esc(it.read_time || '5 دقائق')} للقراءة · ${fmtAgo(it.created_at)}</div>
      <a href="/news/${encodeURIComponent(it.id)}" class="feat-hero-cta">اقرأ القصة كاملة →</a>
    </div>`;
}

async function loadFeatured(){
  try{
    const url = `/api/articles?status=published&featured=1&limit=1${state.region ? '&region=' + encodeURIComponent(state.region) : ''}`;
    const res = await fetch(url);
    const j = await res.json();
    const it = (j.data || [])[0];
    if (it){
      featuredGrid.innerHTML = featuredHTML(it);
      featuredSection.style.display = 'block';
    }
  }catch(e){console.warn('featured load failed', e);}
}

async function loadCategories(){
  try{
    const res = await fetch('/api/articles?status=published&limit=200');
    const j = await res.json();
    const cats = Array.from(new Set((j.data || []).map(a => a.category).filter(Boolean)));
    cats.forEach(c => {
      const btn = document.createElement('button');
      btn.className = 'chip' + (state.category === c ? ' active' : '');
      btn.dataset.category = c;
      btn.textContent = c;
      filtersEl.appendChild(btn);
    });
    if (state.category !== 'all'){
      filtersEl.querySelector('[data-category="all"]').classList.remove('active');
    }
    filtersEl.addEventListener('click', e => {
      const b = e.target.closest('.chip');
      if (!b) return;
      filtersEl.querySelectorAll('.chip').forEach(x => x.classList.remove('active'));
      b.classList.add('active');
      state.category = b.dataset.category;
      state.offset = 0;
      state.exhausted = false;
      grid.innerHTML = '<div class="loading">جاري تحميل الأخبار…</div>';
      loadGrid(true);
    });
  }catch(e){console.warn('categories failed', e);}
}

async function loadGrid(replace = false){
  if (state.loading || state.exhausted) return;
  state.loading = true;
  loadMoreBtn.disabled = true;
  try{
    const q = new URLSearchParams({
      status: 'published',
      limit: state.pageSize,
      offset: state.offset,
    });
    if (state.category && state.category !== 'all') q.set('category', state.category);
    if (state.region) q.set('region', state.region);
    const res = await fetch('/api/articles?' + q.toString());
    const j = await res.json();
    const items = j.data || [];

    if (replace){
      grid.innerHTML = '';
    }
    if (replace && !items.length){
      grid.innerHTML = '<div class="empty"><div class="empty-icon">📰</div>لا توجد أخبار في هذا القسم حالياً.</div>';
      loadMoreWrap.style.display = 'none';
      resultsCountEl.textContent = '0 نتيجة';
      return;
    }

    const html = items.map(cardHTML).join('');
    grid.insertAdjacentHTML('beforeend', html);
    state.offset += items.length;
    state.total += items.length;
    resultsCountEl.textContent = `${state.total} ${state.total === 1 ? 'نتيجة' : 'نتيجة'}`;

    if (items.length < state.pageSize){
      state.exhausted = true;
      loadMoreWrap.style.display = 'none';
    } else {
      loadMoreWrap.style.display = 'flex';
    }
  }catch(e){
    console.error(e);
    if (replace) grid.innerHTML = '<div class="empty">تعذّر تحميل الأخبار. يُرجى المحاولة لاحقاً.</div>';
  }finally{
    state.loading = false;
    loadMoreBtn.disabled = false;
  }
}

loadMoreBtn.addEventListener('click', () => loadGrid(false));

(async function init(){
  await Promise.all([loadFeatured(), loadCategories()]);
  state.total = 0;
  state.offset = 0;
  grid.innerHTML = '';
  loadGrid(true);
})();
