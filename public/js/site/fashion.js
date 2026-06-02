const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
const fallbackImg = 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?auto=format&fit=crop&w=900&q=85';

const grid = document.getElementById('fashionGrid');
const featuredSection = document.getElementById('featured');
const featuredGrid = document.getElementById('featuredGrid');
const resultsCountEl = document.getElementById('resultsCount');

function cardHTML(it){
  const kicker = it.subtitle || it.region || 'موضة';
  return `
    <a href="/fashion/${encodeURIComponent(it.id)}" class="fash">
      <img src="${esc(it.image_url || fallbackImg)}" alt="${esc(it.title)}" onerror="this.src='${fallbackImg}'">
      <div class="fash-overlay"></div>
      <div class="fash-body">
        <div class="fash-kicker">${esc(kicker)}</div>
        <h3 class="fash-headline">${esc(it.title)}</h3>
      </div>
    </a>`;
}

function featuredHTML(it){
  return `
    <div class="feat-hero-img">
      <img src="${esc(it.image_url || fallbackImg)}" alt="${esc(it.title)}" onerror="this.src='${fallbackImg}'">
    </div>
    <div class="feat-hero-body">
      <div class="feat-hero-kicker">✦ ${esc(it.subtitle || 'الموضة العربية')}</div>
      <h2 class="feat-hero-title"><a href="/fashion/${encodeURIComponent(it.id)}">${esc(it.title)}</a></h2>
      <p class="feat-hero-deck">${esc(it.excerpt || '')}</p>
      <div class="feat-hero-meta">بقلم <b>${esc(it.author || 'فريق التحرير')}</b> · ${esc(it.read_time || '6 دقائق')}</div>
      <a href="/fashion/${encodeURIComponent(it.id)}" class="feat-hero-cta">اقرأ التقرير →</a>
    </div>`;
}

async function init(){
  try{
    const res = await fetch('/api/articles?category=' + encodeURIComponent('موضة') + '&status=published&limit=200');
    const items = (await res.json()).data || [];

    if (!items.length){
      grid.innerHTML = '<div class="empty" style="grid-column:1/-1;padding:3rem;text-align:center;">لا توجد تقارير موضة حالياً.</div>';
      resultsCountEl.textContent = '0 تقرير';
      return;
    }

    const featured = items.find(a => a.featured) || items[0];
    featuredGrid.innerHTML = featuredHTML(featured);
    featuredSection.style.display = 'block';

    grid.innerHTML = items.map(cardHTML).join('');
    resultsCountEl.textContent = `${items.length} تقرير`;
  }catch(e){
    console.error(e);
    grid.innerHTML = '<div class="empty" style="grid-column:1/-1;padding:3rem;text-align:center;">تعذّر تحميل التقارير.</div>';
  }
}

gsap.registerPlugin(ScrollTrigger);
init();
