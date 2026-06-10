/* ── DYNAMIC NEWS & BLOGS FROM API ── */
(async function loadHomeGrids(){
  const newsGrid = document.getElementById('newsGrid');
  const blogsGrid = document.getElementById('blogsGridHome');
  if (!newsGrid && !blogsGrid) return;

  const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
  const fmtAgo = iso => {
    if (!iso) return '';
    const d = new Date(iso.replace(' ','T'));
    const diff = (Date.now() - d.getTime())/1000;
    if (diff < 3600) return `منذ ${Math.max(1,Math.floor(diff/60))} دقيقة`;
    if (diff < 86400) return `منذ ${Math.floor(diff/3600)} ساعة`;
    if (diff < 2592000) return `منذ ${Math.floor(diff/86400)} يوم`;
    return d.toISOString().slice(0,10);
  };
  const fallback = 'https://images.unsplash.com/photo-1495020689067-958852a7765e?auto=format&fit=crop&w=800&q=80';

  const articleCard = a => `
    <a href="/news/${encodeURIComponent(a.slug || a.id)}" class="news-card" style="text-decoration:none;color:inherit;display:block;">
      <div class="news-img"><img src="${esc(a.image_url || fallback)}" alt="${esc(a.title)}" onerror="this.src='${fallback}'"></div>
      <div class="news-body">
        <div class="news-kicker">${esc(a.category || 'عام')}${a.region ? ' · ' + esc(a.region) : ''}</div>
        <h3 class="news-headline">${esc(a.title)}</h3>
        <p class="news-excerpt">${esc(a.excerpt || '')}</p>
        <div class="news-meta">قسم ${esc(a.category || 'عام')} · <b>${fmtAgo(a.created_at)}</b></div>
      </div>
    </a>`;

  const blogCard = b => `
    <a href="/blogs/${encodeURIComponent(b.slug || b.id)}" class="news-card" style="text-decoration:none;color:inherit;display:block;">
      <div class="news-img"><img src="${esc(b.image_url || fallback)}" alt="${esc(b.title)}" onerror="this.src='${fallback}'"></div>
      <div class="news-body">
        <div class="news-kicker">مدونة · ${esc(b.author || 'فريق التحرير')}</div>
        <h3 class="news-headline">${esc(b.title)}</h3>
        <p class="news-excerpt">${esc(b.excerpt || '')}</p>
        <div class="news-meta">بقلم <b>${esc(b.author || 'فريق التحرير')}</b> · ${fmtAgo(b.created_at)}</div>
      </div>
    </a>`;

  try {
    const [aRes, bRes] = await Promise.all([
      fetch('/api/articles?status=published&limit=6'),
      fetch('/api/blogs?status=published&limit=3'),
    ]);
    const aJson = await aRes.json();
    const bJson = await bRes.json();
    const articles = aJson.data || [];
    const blogs    = bJson.data || [];

    if (newsGrid) {
      newsGrid.innerHTML = articles.length
        ? articles.slice(0,6).map(articleCard).join('')
        : '<p style="opacity:.6;text-align:center;padding:40px;">لا توجد أخبار حالياً</p>';
    }
    if (blogsGrid) {
      blogsGrid.innerHTML = blogs.length
        ? blogs.slice(0,3).map(blogCard).join('')
        : '<p style="opacity:.6;text-align:center;padding:40px;">لا توجد مدونات حالياً</p>';
    }
  } catch (e) {
    console.error('Failed to load news/blogs:', e);
    if (newsGrid) newsGrid.innerHTML = '<p style="opacity:.6;text-align:center;padding:40px;">تعذّر تحميل الأخبار</p>';
    if (blogsGrid) blogsGrid.innerHTML = '<p style="opacity:.6;text-align:center;padding:40px;">تعذّر تحميل المدونات</p>';
  }
})();

function runHomeAnimations() {
  if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') {
    const pre = document.getElementById('preloader');
    if (pre) pre.style.display = 'none';
    return;
  }

  gsap.registerPlugin(ScrollTrigger);

/* ── PRELOADER ── */
gsap.timeline({onComplete(){
  gsap.to('#preloader',{yPercent:-100,duration:1.1,ease:'power4.inOut',
    onComplete(){document.getElementById('preloader').style.display='none';}
  });
  gsap.from('.hero-kicker,.hero-headline,.hero-deck,.hero-byline',
    {y:30,opacity:0,stagger:.18,duration:.9,ease:'power3.out',delay:.1});
  gsap.from('.sidebar-story',{y:24,opacity:0,stagger:.15,duration:.8,ease:'power3.out',delay:.3});
}})
.to('.pre-logo-img',{opacity:1,scale:1,duration:.75,ease:'power3.out'})
.to('.pre-line',{width:'140px',duration:.6,ease:'power2.inOut'},'-=.2')
.to({},{duration:.55});

/* ── HERO PARALLAX ── */
gsap.to('#heroImg',{
  yPercent:18,ease:'none',
  scrollTrigger:{trigger:'.hero-section',start:'top top',end:'bottom top',scrub:1.2}
});

/* ── SECTION HEADERS ── */
gsap.utils.toArray('.sh').forEach(el=>{
  gsap.from(el,{opacity:0,x:30,duration:.7,ease:'power3.out',
    scrollTrigger:{trigger:el,start:'top 90%'}});
});

/* ── CATEGORIES ── */
gsap.from('.cat-card',{
  y:40,opacity:0,stagger:.06,duration:.7,ease:'power3.out',
  scrollTrigger:{trigger:'.cats-grid',start:'top 82%'}
});
gsap.from('.cats-title,.cats-sub',{
  y:30,opacity:0,stagger:.12,duration:.8,ease:'power3.out',
  scrollTrigger:{trigger:'.cats-section',start:'top 80%'}
});

/* ── IMAGE CLIP REVEALS ── */
document.querySelectorAll('.feat-img img,.inf-img img,.ap-img img,.doc-img img,.fash img,.news-img img,.sidebar-img img').forEach(img=>{
  gsap.fromTo(img,
    {clipPath:'inset(0 0 100% 0)',scale:1.1},
    {clipPath:'inset(0 0 0% 0)',scale:1,duration:1.1,ease:'power4.out',
     scrollTrigger:{trigger:img.parentElement,start:'top 87%'}}
  );
});

/* ── STAGGER GRIDS ── */
['.influencers-grid .inf-card','.doctors-grid .doc-card',
 '.artists-profiles .ap','.news-grid .news-card','.featured-grid .feat-card'].forEach(sel=>{
  const els=document.querySelectorAll(sel);
  if(!els.length) return;
  gsap.from(els,{y:40,opacity:0,stagger:.1,duration:.8,ease:'power3.out',
    scrollTrigger:{trigger:els[0].closest('div'),start:'top 85%'}});
});

/* ── FASHION PARALLAX ── */
document.querySelectorAll('.fash img').forEach(img=>{
  gsap.to(img,{yPercent:14,ease:'none',
    scrollTrigger:{trigger:img.parentElement,start:'top bottom',end:'bottom top',scrub:1.4}});
});

/* ── ARTISTS BAND ── */
gsap.from('.artists-text > *',{y:30,opacity:0,stagger:.12,duration:.85,ease:'power3.out',
  scrollTrigger:{trigger:'.artists-band',start:'top 78%'}});

/* ── EDITORIAL ── */
gsap.utils.toArray('.ed-story').forEach((el,i)=>{
  gsap.from(el,{y:20,opacity:0,duration:.65,ease:'power3.out',
    scrollTrigger:{trigger:el,start:'top 90%'},delay:(i%4)*.07});
});

/* ── NEWSLETTER ── */
gsap.from('.nl-headline,.nl-eyebrow,.nl-sub',{y:35,opacity:0,stagger:.15,duration:.9,ease:'power3.out',
  scrollTrigger:{trigger:'.nl-section',start:'top 80%'}});

/* ── MAGNETIC BUTTONS ── */
document.querySelectorAll('.btn-gold,.btn-subscribe').forEach(btn=>{
  btn.addEventListener('mousemove',e=>{
    const r=btn.getBoundingClientRect();
    gsap.to(btn,{x:(e.clientX-r.left-r.width/2)*.25,y:(e.clientY-r.top-r.height/2)*.25,duration:.3,ease:'power2.out'});
  });
  btn.addEventListener('mouseleave',()=>gsap.to(btn,{x:0,y:0,duration:.5,ease:'elastic.out(1,.4)'}));
});
}

if (typeof gsap !== 'undefined') {
  runHomeAnimations();
} else {
  document.addEventListener('DOMContentLoaded', runHomeAnimations);
}
