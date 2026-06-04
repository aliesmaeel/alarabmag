const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
const fallbackImg = 'https://images.unsplash.com/photo-1611162616475-46b635cb6868?auto=format&fit=crop&w=900&q=80';
const fmtAgo = iso => {
  if (!iso) return '';
  const d = new Date(iso.replace(' ','T'));
  const diff = (Date.now() - d.getTime())/1000;
  if (diff < 86400) return `منذ ${Math.max(1,Math.floor(diff/3600))} ساعة`;
  if (diff < 2592000) return `منذ ${Math.floor(diff/86400)} يوم`;
  return d.toISOString().slice(0,10);
};

const params = new URLSearchParams(location.search);
let state = { category: params.get('category') || 'all', offset:0, pageSize:9, all:[], filtered:[], loading:false };

const grid = document.getElementById('interviewsGrid');
const featuredSection = document.getElementById('featured');
const featuredGrid = document.getElementById('featuredGrid');
const filtersEl = document.getElementById('filters');
const resultsCountEl = document.getElementById('resultsCount');
const loadMoreWrap = document.getElementById('loadMoreWrap');
const loadMoreBtn = document.getElementById('loadMoreBtn');

function interviewCardHTML(item){
  return `
    <a href="/interviews/${encodeURIComponent(item.slug)}" class="list-card interview-card">
      <div class="list-img">
        <img src="${esc(item.thumbnail_url || fallbackImg)}" alt="${esc(item.title)}" onerror="this.src='${fallbackImg}'">
        <span class="interview-play">▶</span>
        ${item.featured ? '<span class="list-img-badge">مختار التحرير</span>' : ''}
      </div>
      <div class="list-body">
        <div class="list-kicker">مقابلة · ${esc(item.category || 'عام')}</div>
        <h3 class="list-headline">${esc(item.title)}</h3>
        <p class="list-excerpt">${esc(item.description || '')}</p>
        <div class="list-meta">
          <span>${fmtAgo(item.created_at)}</span>
          <span>${(item.views || 0).toLocaleString('ar-EG')} مشاهدة</span>
        </div>
      </div>
    </a>`;
}

function featuredHTML(item){
  return `
    <div class="feat-interview-media">
      <img src="${esc(item.thumbnail_url || fallbackImg)}" alt="${esc(item.title)}" onerror="this.src='${fallbackImg}'">
      <span class="interview-play feat-play">▶</span>
      <span class="feat-interview-tag">مقابلة مميزة</span>
    </div>
    <div class="feat-interview-body">
      <div class="feat-interview-eyebrow">Featured Interview</div>
      <h2 class="feat-interview-title"><a href="/interviews/${encodeURIComponent(item.slug)}">${esc(item.title)}</a></h2>
      <p class="feat-interview-excerpt">${esc(item.description || '')}</p>
      <div class="feat-interview-meta">${esc(item.category || 'عام')} · ${fmtAgo(item.created_at)}</div>
      <a href="/interviews/${encodeURIComponent(item.slug)}" class="feat-interview-cta">شاهد المقابلة →</a>
    </div>`;
}

function renderGrid(){
  const data = state.filtered;
  if (!data.length){
    grid.innerHTML = '<div class="empty"><div class="empty-icon">🎬</div>لا توجد مقابلات في هذا التصنيف.</div>';
    loadMoreWrap.style.display = 'none';
    resultsCountEl.textContent = '0 مقابلة';
    return;
  }
  const slice = data.slice(0, state.offset + state.pageSize);
  state.offset = slice.length;
  grid.innerHTML = slice.map(interviewCardHTML).join('');
  resultsCountEl.textContent = `${data.length} مقابلة`;
  loadMoreWrap.style.display = slice.length < data.length ? 'flex' : 'none';
}

function applyFilter(){
  if (state.category === 'all') state.filtered = state.all;
  else state.filtered = state.all.filter(i => i.category === state.category);
  state.offset = 0;
  renderGrid();
}

function buildCategoryChips(){
  const categories = new Set();
  state.all.forEach(i => { if (i.category) categories.add(i.category); });
  Array.from(categories).sort().forEach(cat => {
    const btn = document.createElement('button');
    btn.className = 'chip' + (state.category === cat ? ' active' : '');
    btn.dataset.category = cat;
    btn.textContent = cat;
    filtersEl.appendChild(btn);
  });
  if (state.category !== 'all'){
    const allBtn = filtersEl.querySelector('[data-category="all"]');
    if (allBtn) allBtn.classList.remove('active');
  }
  filtersEl.addEventListener('click', e => {
    const b = e.target.closest('.chip'); if (!b) return;
    filtersEl.querySelectorAll('.chip').forEach(x => x.classList.remove('active'));
    b.classList.add('active');
    state.category = b.dataset.category;
    applyFilter();
  });
}

async function init(){
  try{
    const res = await fetch('/api/interviews?status=published&limit=200');
    const j = await res.json();
    state.all = j.data || [];

    const featured = state.all.find(i => i.featured) || state.all[0];
    if (featured){
      featuredSection.style.display = 'block';
      featuredGrid.innerHTML = featuredHTML(featured);
    }

    buildCategoryChips();
    applyFilter();

    loadMoreBtn?.addEventListener('click', renderGrid);

    gsap.from('.page-hero-eyebrow,.page-hero-title,.page-hero-sub', {y:30,opacity:0,stagger:.12,duration:.85,ease:'power3.out'});
    gsap.from('.feat-interview-media,.feat-interview-body', {y:40,opacity:0,stagger:.15,duration:.9,ease:'power3.out',delay:.2});
  }catch(e){
    console.error(e);
    grid.innerHTML = '<div class="empty"><div class="empty-icon">⚠️</div>تعذّر تحميل المقابلات.</div>';
  }
}

if (typeof gsap !== 'undefined') init();
else document.addEventListener('DOMContentLoaded', init);
