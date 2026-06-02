const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
const fallbackImg = 'https://images.unsplash.com/photo-1518834107812-67b0b7c58434?auto=format&fit=crop&w=900&q=80';

const params = new URLSearchParams(location.search);
let state = { role: params.get('role') || 'all', offset:0, pageSize:9, all:[], filtered:[] };

const grid = document.getElementById('artGrid');
const featuredSection = document.getElementById('featured');
const featuredGrid = document.getElementById('featuredGrid');
const featuredBg = document.getElementById('featuredBg');
const filtersEl = document.getElementById('filters');
const resultsCountEl = document.getElementById('resultsCount');
const loadMoreWrap = document.getElementById('loadMoreWrap');
const loadMoreBtn = document.getElementById('loadMoreBtn');

const pad = n => String(n).padStart(2,'0');

function cardHTML(a, idx){
  return `
    <a href="/artists/${encodeURIComponent(a.id)}" class="art-list-card">
      <div class="art-list-img">
        <img src="${esc(a.image_url || fallbackImg)}" alt="${esc(a.name)}" onerror="this.src='${fallbackImg}'">
        <div class="art-list-img-ov"></div>
        <span class="art-list-num">${pad(idx + 1)}</span>
      </div>
      <div class="art-list-body">
        <div class="art-list-role">${esc(a.role || 'فنان')}</div>
        <h3 class="art-list-name">${esc(a.name)}</h3>
        <div class="art-list-country">${a.flag ? esc(a.flag) + ' ' : ''}${esc(a.country || '')}</div>
        ${a.excerpt ? `<p class="art-list-excerpt">${esc(a.excerpt)}</p>` : ''}
      </div>
    </a>`;
}

function featuredHTML(a){
  return `
    <div class="feat-art-img"><img src="${esc(a.image_url || fallbackImg)}" alt="${esc(a.name)}" onerror="this.src='${fallbackImg}'"></div>
    <div class="feat-art-body">
      <div class="feat-art-num">01</div>
      <div class="feat-art-eyebrow">✦ فنان الشهر</div>
      <div class="feat-art-role">${esc(a.role || 'فنان')}</div>
      <h2 class="feat-art-name">${esc(a.name)}</h2>
      <div class="feat-art-country">${a.flag ? esc(a.flag) + ' ' : ''}${esc(a.country || '')}</div>
      ${a.excerpt ? `<p class="feat-art-excerpt">"${esc(a.excerpt)}"</p>` : ''}
      <a href="/artists/${encodeURIComponent(a.id)}" class="feat-art-cta">الملف الفني الكامل →</a>
    </div>`;
}

function renderGrid(){
  const data = state.filtered;
  if (!data.length){
    grid.innerHTML = '<div class="empty" style="grid-column:1/-1;background:transparent;color:rgba(248,244,238,.5);"><div class="empty-icon">🎭</div>لا يوجد فنانون في هذا التخصص حالياً.</div>';
    loadMoreWrap.style.display = 'none';
    resultsCountEl.textContent = '0 فنان';
    return;
  }
  const slice = data.slice(0, state.offset + state.pageSize);
  state.offset = slice.length;
  grid.innerHTML = slice.map((a, i) => cardHTML(a, i)).join('');
  resultsCountEl.textContent = `${data.length} فنان`;
  loadMoreWrap.style.display = slice.length < data.length ? 'flex' : 'none';
}

function applyFilter(){
  if (state.role === 'all') state.filtered = state.all;
  else state.filtered = state.all.filter(a => (a.role || '').includes(state.role));
  state.offset = 0;
  renderGrid();
}

function buildRoleChips(){
  const roles = Array.from(new Set(state.all.map(a => a.role).filter(Boolean))).sort();
  roles.forEach(r => {
    const btn = document.createElement('button');
    btn.className = 'chip' + (state.role === r ? ' active' : '');
    btn.dataset.role = r;
    btn.textContent = r;
    filtersEl.appendChild(btn);
  });
  if (state.role !== 'all') filtersEl.querySelector('[data-role="all"]').classList.remove('active');
  filtersEl.addEventListener('click', e => {
    const b = e.target.closest('.chip'); if (!b) return;
    filtersEl.querySelectorAll('.chip').forEach(x => x.classList.remove('active'));
    b.classList.add('active');
    state.role = b.dataset.role;
    applyFilter();
  });
}

async function init(){
  try{
    const res = await fetch('/api/people?category=artist&limit=200');
    const j = await res.json();
    state.all = j.data || [];

    if (!state.all.length){
      grid.innerHTML = '<div class="empty" style="grid-column:1/-1;background:transparent;color:rgba(248,244,238,.5);"><div class="empty-icon">🎭</div>لا يوجد فنانون حالياً.</div>';
      resultsCountEl.textContent = '0 فنان';
      return;
    }

    const featured = state.all.find(a => a.featured) || state.all[0];
    if (featured){
      featuredGrid.innerHTML = featuredHTML(featured);
      featuredBg.style.backgroundImage = `url('${featured.image_url || fallbackImg}')`;
      featuredSection.style.display = 'block';
    }

    buildRoleChips();
    applyFilter();
  }catch(e){
    console.error(e);
    grid.innerHTML = '<div class="empty" style="grid-column:1/-1;background:transparent;color:rgba(248,244,238,.5);">تعذّر تحميل القائمة.</div>';
  }
}

loadMoreBtn.addEventListener('click', () => {
  state.offset += state.pageSize;
  const data = state.filtered;
  const slice = data.slice(0, state.offset);
  grid.innerHTML = slice.map((a, i) => cardHTML(a, i)).join('');
  loadMoreWrap.style.display = slice.length < data.length ? 'flex' : 'none';
});

gsap.registerPlugin(ScrollTrigger);
init();

const cur=document.getElementById('cur'),ring=document.getElementById('curRing');
let mx=0,my=0,rx=0,ry=0;
document.addEventListener('mousemove',e=>{mx=e.clientX;my=e.clientY;cur.style.left=mx+'px';cur.style.top=my+'px';});
(function a(){rx+=(mx-rx)*.1;ry+=(my-ry)*.1;ring.style.left=rx+'px';ring.style.top=ry+'px';requestAnimationFrame(a);})();
document.addEventListener('mouseover',e=>{
  if(e.target.closest('a,button,.art-list-card')){
    gsap.to(cur,{width:16,height:16,duration:.2});gsap.to(ring,{width:52,height:52,opacity:.3,duration:.3});
  }else{
    gsap.to(cur,{width:8,height:8,duration:.2});gsap.to(ring,{width:32,height:32,opacity:.5,duration:.3});
  }
});
