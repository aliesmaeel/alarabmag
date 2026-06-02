const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
const fallbackImg = 'https://images.unsplash.com/photo-1455390582262-044cdead277a?auto=format&fit=crop&w=900&q=80';
const fallbackAvatar = 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=200&q=80';
const fmtAgo = iso => {
  if (!iso) return '';
  const d = new Date(iso.replace(' ','T'));
  const diff = (Date.now() - d.getTime())/1000;
  if (diff < 86400) return `منذ ${Math.max(1,Math.floor(diff/3600))} ساعة`;
  if (diff < 2592000) return `منذ ${Math.floor(diff/86400)} يوم`;
  return d.toISOString().slice(0,10);
};
const tagsOf = b => (b.tags ? String(b.tags).split(',').map(s=>s.trim()).filter(Boolean) : []);

const params = new URLSearchParams(location.search);
let state = { tag: params.get('tag') || 'all', offset:0, pageSize:9, total:0, all:[], filtered:[], loading:false };

const grid = document.getElementById('blogsGrid');
const featuredSection = document.getElementById('featured');
const featuredGrid = document.getElementById('featuredGrid');
const filtersEl = document.getElementById('filters');
const resultsCountEl = document.getElementById('resultsCount');
const loadMoreWrap = document.getElementById('loadMoreWrap');
const loadMoreBtn = document.getElementById('loadMoreBtn');

function blogCardHTML(b){
  const tags = tagsOf(b).slice(0,3);
  return `
    <a href="/blogs/${encodeURIComponent(b.id)}" class="list-card">
      <div class="list-img">
        <img src="${esc(b.image_url || fallbackImg)}" alt="${esc(b.title)}" onerror="this.src='${fallbackImg}'">
        ${b.featured ? '<span class="list-img-badge">مختار التحرير</span>' : ''}
      </div>
      <div class="list-body">
        <div class="list-kicker">مدونة${tags.length ? ' · ' + esc(tags[0]) : ''}</div>
        <h3 class="list-headline">${esc(b.title)}</h3>
        <p class="list-excerpt">${esc(b.excerpt || '')}</p>
        ${tags.length ? `<div class="tag-cloud">${tags.map(t => `<span class="tag-pill">${esc(t)}</span>`).join('')}</div>` : ''}
        <div class="list-author">
          <div class="list-author-img"><img src="${esc(b.author_img || fallbackAvatar)}" alt="${esc(b.author)}" onerror="this.src='${fallbackAvatar}'"></div>
          <div class="list-author-info">
            <div class="list-author-name">${esc(b.author || 'فريق التحرير')}</div>
            <div class="list-author-date">${fmtAgo(b.created_at)}</div>
          </div>
        </div>
      </div>
    </a>`;
}

function featuredHTML(b){
  return `
    <div class="feat-blog-img">
      <img src="${esc(b.image_url || fallbackImg)}" alt="${esc(b.title)}" onerror="this.src='${fallbackImg}'">
      <span class="feat-blog-img-tag">مختار التحرير</span>
    </div>
    <div class="feat-blog-body">
      <div class="feat-blog-eyebrow">Featured Blog</div>
      <h2 class="feat-blog-title"><a href="/blogs/${encodeURIComponent(b.id)}">${esc(b.title)}</a></h2>
      <p class="feat-blog-excerpt">${esc(b.excerpt || '')}</p>
      <div class="feat-author">
        <div class="feat-author-img"><img src="${esc(b.author_img || fallbackAvatar)}" alt="${esc(b.author)}" onerror="this.src='${fallbackAvatar}'"></div>
        <div class="feat-author-info">
          <div class="feat-author-name">${esc(b.author || 'فريق التحرير')}</div>
          <div class="feat-author-bio">${esc(b.author_bio || 'كاتب في مجلة العرب')}</div>
        </div>
      </div>
      <a href="/blogs/${encodeURIComponent(b.id)}" class="feat-blog-cta">اقرأ المقال →</a>
    </div>`;
}

function renderGrid(){
  const data = state.filtered;
  if (!data.length){
    grid.innerHTML = '<div class="empty"><div class="empty-icon">✍️</div>لا توجد مدونات في هذا الموضوع.</div>';
    loadMoreWrap.style.display = 'none';
    resultsCountEl.textContent = '0 مقال';
    return;
  }
  const slice = data.slice(0, state.offset + state.pageSize);
  state.offset = slice.length;
  grid.innerHTML = slice.map(blogCardHTML).join('');
  resultsCountEl.textContent = `${data.length} مقال`;
  loadMoreWrap.style.display = slice.length < data.length ? 'flex' : 'none';
}

function applyFilter(){
  if (state.tag === 'all') state.filtered = state.all;
  else state.filtered = state.all.filter(b => tagsOf(b).includes(state.tag));
  state.offset = 0;
  renderGrid();
}

function buildTagChips(){
  const allTags = new Set();
  state.all.forEach(b => tagsOf(b).forEach(t => allTags.add(t)));
  const sorted = Array.from(allTags).sort();
  sorted.forEach(t => {
    const btn = document.createElement('button');
    btn.className = 'chip' + (state.tag === t ? ' active' : '');
    btn.dataset.tag = t;
    btn.textContent = t;
    filtersEl.appendChild(btn);
  });
  if (state.tag !== 'all'){
    filtersEl.querySelector('[data-tag="all"]').classList.remove('active');
  }
  filtersEl.addEventListener('click', e => {
    const b = e.target.closest('.chip'); if (!b) return;
    filtersEl.querySelectorAll('.chip').forEach(x => x.classList.remove('active'));
    b.classList.add('active');
    state.tag = b.dataset.tag;
    applyFilter();
  });
}

async function init(){
  try{
    const res = await fetch('/api/blogs?status=published&limit=200');
    const j = await res.json();
    state.all = j.data || [];

    if (!state.all.length){
      grid.innerHTML = '<div class="empty"><div class="empty-icon">✍️</div>لا توجد مدونات منشورة حالياً.</div>';
      resultsCountEl.textContent = '0 مقال';
      return;
    }

    const featured = state.all.find(b => b.featured) || state.all[0];
    if (featured){
      featuredGrid.innerHTML = featuredHTML(featured);
      featuredSection.style.display = 'block';
    }

    buildTagChips();
    applyFilter();
  }catch(e){
    console.error(e);
    grid.innerHTML = '<div class="empty">تعذّر تحميل المدونات. يُرجى المحاولة لاحقاً.</div>';
  }
}

loadMoreBtn.addEventListener('click', () => {
  state.offset += state.pageSize;
  const data = state.filtered;
  const slice = data.slice(0, state.offset);
  grid.innerHTML = slice.map(blogCardHTML).join('');
  loadMoreWrap.style.display = slice.length < data.length ? 'flex' : 'none';
});

gsap.registerPlugin(ScrollTrigger);
init();
