const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
const fallbackImg = 'https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?auto=format&fit=crop&w=900&q=80';

const params = new URLSearchParams(location.search);
let state = { spec: params.get('specialty') || 'all', offset:0, pageSize:8, all:[], filtered:[] };

const grid = document.getElementById('docsGrid');
const featuredSection = document.getElementById('featured');
const featuredGrid = document.getElementById('featuredGrid');
const filtersEl = document.getElementById('filters');
const resultsCountEl = document.getElementById('resultsCount');
const loadMoreWrap = document.getElementById('loadMoreWrap');
const loadMoreBtn = document.getElementById('loadMoreBtn');

function cardHTML(d){
  return `
    <a href="/doctors/${encodeURIComponent(d.id)}" class="doc-list-card">
      <div class="doc-list-img">
        <img src="${esc(d.image_url || fallbackImg)}" alt="${esc(d.name)}" onerror="this.src='${fallbackImg}'">
        ${d.flag ? `<span class="doc-list-img-flag">${esc(d.flag)}</span>` : ''}
        <div class="doc-list-img-ov"></div>
      </div>
      <div class="doc-list-body">
        <div class="doc-list-spec">${esc(d.specialty || d.role || 'طب عام')}</div>
        <h3 class="doc-list-name">${esc(d.name)}</h3>
        <div class="doc-list-hospital">${esc(d.hospital || d.country || '')}</div>
        ${d.badge ? `<span class="doc-list-badge">${esc(d.badge)}</span>` : ''}
      </div>
    </a>`;
}

function featuredHTML(d){
  return `
    <div class="feat-doc-img"><img src="${esc(d.image_url || fallbackImg)}" alt="${esc(d.name)}" onerror="this.src='${fallbackImg}'"></div>
    <div class="feat-doc-body">
      <div class="feat-doc-eyebrow">✦ طبيب الشهر</div>
      <div class="feat-doc-spec">${esc(d.specialty || d.role || '')}</div>
      <h2 class="feat-doc-name">${esc(d.name)}</h2>
      <div class="feat-doc-country">${d.flag ? esc(d.flag) + ' ' : ''}${esc(d.hospital || d.country || '')}</div>
      ${d.excerpt ? `<p class="feat-doc-excerpt">${esc(d.excerpt)}</p>` : ''}
      ${(d.stat || d.badge) ? `
        <div class="feat-doc-stat">
          ${d.stat ? `<div class="stat-box"><div class="stat-num">${esc(d.stat)}</div><div class="stat-lbl">${esc(d.stat_label || 'إنجاز')}</div></div>` : ''}
          ${d.badge ? `<div class="stat-box"><div class="stat-num" style="font-size:1rem;line-height:1.4;color:var(--gold);">${esc(d.badge)}</div><div class="stat-lbl">تكريم</div></div>` : ''}
        </div>` : ''}
      <a href="/doctors/${encodeURIComponent(d.id)}" class="feat-doc-cta">الملف الكامل →</a>
    </div>`;
}

function renderGrid(){
  const data = state.filtered;
  if (!data.length){
    grid.innerHTML = '<div class="empty" style="grid-column:1/-1"><div class="empty-icon">🩺</div>لا يوجد أطباء في هذا التخصص حالياً.</div>';
    loadMoreWrap.style.display = 'none';
    resultsCountEl.textContent = '0 طبيب';
    return;
  }
  const slice = data.slice(0, state.offset + state.pageSize);
  state.offset = slice.length;
  grid.innerHTML = slice.map(cardHTML).join('');
  resultsCountEl.textContent = `${data.length} طبيب`;
  loadMoreWrap.style.display = slice.length < data.length ? 'flex' : 'none';
}

function applyFilter(){
  if (state.spec === 'all') state.filtered = state.all;
  else state.filtered = state.all.filter(d => (d.specialty || d.role || '').includes(state.spec));
  state.offset = 0;
  renderGrid();
}

function buildSpecChips(){
  const specs = Array.from(new Set(state.all.map(d => d.specialty || d.role).filter(Boolean))).sort();
  specs.forEach(s => {
    const btn = document.createElement('button');
    btn.className = 'chip' + (state.spec === s ? ' active' : '');
    btn.dataset.spec = s;
    btn.textContent = s;
    filtersEl.appendChild(btn);
  });
  if (state.spec !== 'all') filtersEl.querySelector('[data-spec="all"]').classList.remove('active');
  filtersEl.addEventListener('click', e => {
    const b = e.target.closest('.chip'); if (!b) return;
    filtersEl.querySelectorAll('.chip').forEach(x => x.classList.remove('active'));
    b.classList.add('active');
    state.spec = b.dataset.spec;
    applyFilter();
  });
}

async function init(){
  try{
    const res = await fetch('/api/people?category=doctor&limit=200');
    const j = await res.json();
    state.all = j.data || [];

    if (!state.all.length){
      grid.innerHTML = '<div class="empty" style="grid-column:1/-1"><div class="empty-icon">🩺</div>لا يوجد أطباء حالياً.</div>';
      resultsCountEl.textContent = '0 طبيب';
      return;
    }

    const featured = state.all.find(d => d.featured) || state.all[0];
    if (featured){
      featuredGrid.innerHTML = featuredHTML(featured);
      featuredSection.style.display = 'block';
    }

    buildSpecChips();
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
