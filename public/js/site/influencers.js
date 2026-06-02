const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
const fallbackImg = 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=900&q=80';

const params = new URLSearchParams(location.search);
let state = { plat: params.get('platform') || 'all', offset:0, pageSize:8, all:[], filtered:[] };

const grid = document.getElementById('infGrid');
const featuredSection = document.getElementById('featured');
const featuredGrid = document.getElementById('featuredGrid');
const featuredBg = document.getElementById('featuredBg');
const filtersEl = document.getElementById('filters');
const resultsCountEl = document.getElementById('resultsCount');
const loadMoreWrap = document.getElementById('loadMoreWrap');
const loadMoreBtn = document.getElementById('loadMoreBtn');

function cardHTML(p){
  return `
    <a href="/influencers/${encodeURIComponent(p.id)}" class="inf-list-card">
      <div class="inf-list-img">
        <img src="${esc(p.image_url || fallbackImg)}" alt="${esc(p.name)}" onerror="this.src='${fallbackImg}'">
        ${p.flag ? `<span class="inf-list-img-flag">${esc(p.flag)}</span>` : ''}
        <div class="inf-list-img-ov"></div>
        ${p.platform ? `<span class="inf-list-img-platform">${esc(p.platform)}</span>` : ''}
      </div>
      <div class="inf-list-body">
        <div class="inf-list-cat">${esc(p.role || 'مؤثر')}${p.country ? ' · ' + esc(p.country) : ''}</div>
        <h3 class="inf-list-name">${esc(p.name)}</h3>
        <div class="inf-list-handle">${esc(p.handle || '')}</div>
        ${p.followers ? `<div class="inf-list-followers"><div class="inf-list-followers-num">${esc(p.followers)}</div><div class="inf-list-followers-lbl">متابع</div></div>` : ''}
      </div>
    </a>`;
}

function featuredHTML(p){
  return `
    <div class="feat-inf-img"><img src="${esc(p.image_url || fallbackImg)}" alt="${esc(p.name)}" onerror="this.src='${fallbackImg}'"></div>
    <div class="feat-inf-body">
      <div class="feat-inf-eyebrow">✦ المؤثر المميز</div>
      <div class="feat-inf-cat">${esc(p.role || 'مؤثر')}${p.country ? ' · ' + esc(p.flag || '') + ' ' + esc(p.country) : ''}</div>
      <h2 class="feat-inf-name">${esc(p.name)}</h2>
      <div class="feat-inf-handle">${esc(p.handle || '')}</div>
      ${p.excerpt ? `<p class="feat-inf-excerpt">${esc(p.excerpt)}</p>` : ''}
      <div class="feat-inf-followers">
        ${p.followers ? `<div class="fi-stat"><div class="fi-num">${esc(p.followers)}</div><div class="fi-lbl">المتابعون</div></div>` : ''}
        ${p.platform ? `<div class="fi-stat"><div class="fi-num" style="font-size:1.3rem;line-height:1.3;color:var(--gold);">${esc(p.platform)}</div><div class="fi-lbl">المنصة الرئيسية</div></div>` : ''}
        ${p.stat ? `<div class="fi-stat"><div class="fi-num">${esc(p.stat)}</div><div class="fi-lbl">${esc(p.stat_label || 'إحصاء')}</div></div>` : ''}
      </div>
      <a href="/influencers/${encodeURIComponent(p.id)}" class="feat-inf-cta">الملف الكامل →</a>
    </div>`;
}

function renderGrid(){
  const data = state.filtered;
  if (!data.length){
    grid.innerHTML = '<div class="empty" style="grid-column:1/-1"><div class="empty-icon">📱</div>لا يوجد مؤثرون في هذه المنصة حالياً.</div>';
    loadMoreWrap.style.display = 'none';
    resultsCountEl.textContent = '0 مؤثر';
    return;
  }
  const slice = data.slice(0, state.offset + state.pageSize);
  state.offset = slice.length;
  grid.innerHTML = slice.map(cardHTML).join('');
  resultsCountEl.textContent = `${data.length} مؤثر`;
  loadMoreWrap.style.display = slice.length < data.length ? 'flex' : 'none';
}

function applyFilter(){
  if (state.plat === 'all') state.filtered = state.all;
  else state.filtered = state.all.filter(p => (p.platform || '').toLowerCase().includes(state.plat.toLowerCase()));
  state.offset = 0;
  renderGrid();
}

function buildPlatChips(){
  const plats = Array.from(new Set(state.all.map(p => p.platform).filter(Boolean))).sort();
  plats.forEach(s => {
    const btn = document.createElement('button');
    btn.className = 'chip' + (state.plat === s ? ' active' : '');
    btn.dataset.plat = s;
    btn.textContent = s;
    filtersEl.appendChild(btn);
  });
  if (state.plat !== 'all') filtersEl.querySelector('[data-plat="all"]').classList.remove('active');
  filtersEl.addEventListener('click', e => {
    const b = e.target.closest('.chip'); if (!b) return;
    filtersEl.querySelectorAll('.chip').forEach(x => x.classList.remove('active'));
    b.classList.add('active');
    state.plat = b.dataset.plat;
    applyFilter();
  });
}

async function init(){
  try{
    const res = await fetch('/api/people?category=influencer&limit=200');
    const j = await res.json();
    state.all = j.data || [];

    if (!state.all.length){
      grid.innerHTML = '<div class="empty" style="grid-column:1/-1"><div class="empty-icon">📱</div>لا يوجد مؤثرون حالياً.</div>';
      resultsCountEl.textContent = '0 مؤثر';
      return;
    }

    const featured = state.all.find(p => p.featured) || state.all[0];
    if (featured){
      featuredGrid.innerHTML = featuredHTML(featured);
      featuredBg.style.backgroundImage = `url('${featured.image_url || fallbackImg}')`;
      featuredSection.style.display = 'block';
    }

    buildPlatChips();
    applyFilter();
  }catch(e){
    console.error(e);
    grid.innerHTML = '<div class="empty" style="grid-column:1/-1">تعذّر تحميل القائمة.</div>';
  }
}

loadMoreBtn.addEventListener('click', () => {
  state.offset += state.pageSize;
  const data = state.filtered;
  const slice = data.slice(0, state.offset);
  grid.innerHTML = slice.map(cardHTML).join('');
  loadMoreWrap.style.display = slice.length < data.length ? 'flex' : 'none';
});

gsap.registerPlugin(ScrollTrigger);
init();
