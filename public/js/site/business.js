const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
const fallbackImg = 'https://images.unsplash.com/photo-1560250097-0b93528c311a?auto=format&fit=crop&w=900&q=80';
const storyFallback = 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=900&q=80';

const leadersGrid = document.getElementById('leadersGrid');
const storiesGrid = document.getElementById('storiesGrid');
const featuredSection = document.getElementById('featured');
const featuredGrid = document.getElementById('featuredGrid');
const leadersCountEl = document.getElementById('leadersCount');
const storiesCountEl = document.getElementById('storiesCount');
const loadMoreWrap = document.getElementById('loadMoreWrap');
const loadMoreBtn = document.getElementById('loadMoreBtn');

let stories = [];
let storiesOffset = 0;
const pageSize = 9;

function leaderCardHTML(d){
  return `
    <a href="/business/${encodeURIComponent(d.id)}" class="doc-list-card">
      <div class="doc-list-img">
        <img src="${esc(d.image_url || fallbackImg)}" alt="${esc(d.name)}" onerror="this.src='${fallbackImg}'">
        ${d.flag ? `<span class="doc-list-img-flag">${esc(d.flag)}</span>` : ''}
        <div class="doc-list-img-ov"></div>
      </div>
      <div class="doc-list-body">
        <div class="doc-list-spec">${esc(d.company || d.role || 'ريادة أعمال')}</div>
        <h3 class="doc-list-name">${esc(d.name)}</h3>
        <div class="doc-list-hospital">${esc(d.country || '')}</div>
        ${d.net_worth ? `<span class="doc-list-badge">${esc(d.net_worth)}</span>` : ''}
      </div>
    </a>`;
}

function featuredLeaderHTML(d){
  return `
    <div class="feat-doc-img"><img src="${esc(d.image_url || fallbackImg)}" alt="${esc(d.name)}" onerror="this.src='${fallbackImg}'"></div>
    <div class="feat-doc-body">
      <div class="feat-doc-eyebrow">✦ رجل الأعمال · الشهر</div>
      <div class="feat-doc-spec">${esc(d.company || d.role || '')}</div>
      <h2 class="feat-doc-name">${esc(d.name)}</h2>
      <div class="feat-doc-country">${d.flag ? esc(d.flag) + ' ' : ''}${esc(d.country || '')}</div>
      ${d.excerpt ? `<p class="feat-doc-excerpt">${esc(d.excerpt)}</p>` : ''}
      ${(d.stat || d.net_worth) ? `
        <div class="feat-doc-stat">
          ${d.stat ? `<div class="stat-box"><div class="stat-num">${esc(d.stat)}</div><div class="stat-lbl">${esc(d.stat_label || 'إنجاز')}</div></div>` : ''}
          ${d.net_worth ? `<div class="stat-box"><div class="stat-num" style="font-size:1rem;line-height:1.4;color:var(--gold);">${esc(d.net_worth)}</div><div class="stat-lbl">التقييم</div></div>` : ''}
        </div>` : ''}
      <a href="/business/${encodeURIComponent(d.id)}" class="feat-doc-cta">الملف الكامل →</a>
    </div>`;
}

function storyCardHTML(it){
  return `
    <a href="/news/${encodeURIComponent(it.id)}" class="list-card">
      <div class="list-img">
        <img src="${esc(it.image_url || storyFallback)}" alt="${esc(it.title)}" onerror="this.src='${storyFallback}'">
        ${it.featured ? '<span class="list-img-badge">مميّز</span>' : ''}
      </div>
      <div class="list-body">
        <div class="list-kicker">${esc(it.subtitle || it.category || 'أعمال')}${it.region ? ' · ' + esc(it.region) : ''}</div>
        <h3 class="list-headline">${esc(it.title)}</h3>
        <p class="list-excerpt">${esc(it.excerpt || '')}</p>
        <div class="list-meta">
          <span>${esc(it.read_time || '5 دقائق')}</span>
          <span><b>${esc(it.author || 'فريق التحرير')}</b></span>
        </div>
      </div>
    </a>`;
}

function renderStories(){
  const slice = stories.slice(0, storiesOffset);
  storiesGrid.innerHTML = slice.length
    ? slice.map(storyCardHTML).join('')
    : '<div class="empty" style="grid-column:1/-1"><div class="empty-icon">💼</div>لا توجد قصص أعمال حالياً.</div>';
  storiesCountEl.textContent = `${stories.length} قصة`;
  loadMoreWrap.style.display = slice.length < stories.length ? 'flex' : 'none';
}

async function init(){
  try{
    const [peopleRes, articlesRes] = await Promise.all([
      fetch('/api/people?category=business&limit=200'),
      fetch('/api/articles?category=' + encodeURIComponent('أعمال') + '&status=published&limit=200'),
    ]);
    const people = (await peopleRes.json()).data || [];
    stories = (await articlesRes.json()).data || [];

    if (people.length){
      const featured = people.find(d => d.featured) || people[0];
      featuredGrid.innerHTML = featuredLeaderHTML(featured);
      featuredSection.style.display = 'block';
      leadersGrid.innerHTML = people.map(leaderCardHTML).join('');
      leadersCountEl.textContent = `${people.length} ملف`;
    } else {
      leadersGrid.innerHTML = '<div class="empty" style="grid-column:1/-1">لا يوجد ملفات أعمال حالياً.</div>';
      leadersCountEl.textContent = '0 ملف';
    }

    storiesOffset = Math.min(pageSize, stories.length || pageSize);
    renderStories();
  }catch(e){
    console.error(e);
    leadersGrid.innerHTML = '<div class="empty" style="grid-column:1/-1">تعذّر تحميل المحتوى.</div>';
    storiesGrid.innerHTML = '<div class="empty" style="grid-column:1/-1">تعذّر تحميل القصص.</div>';
  }
}

loadMoreBtn?.addEventListener('click', () => {
  storiesOffset = Math.min(storiesOffset + pageSize, stories.length);
  renderStories();
});

gsap.registerPlugin(ScrollTrigger);
init();
