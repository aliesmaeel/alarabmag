const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
const fallbackImg = 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=900&q=80';

function getId(){
  if (window.SITE_PERSON_ID != null) return String(window.SITE_PERSON_ID);
  const m = location.pathname.match(/\/influencers\/(\d+)/);
  if (m) return m[1];
  return new URLSearchParams(location.search).get('id');
}

function bioHTML(raw){
  if (!raw) return '<p>لم تتم إضافة سيرة بعد.</p>';
  const looksLikeHTML = /<\/?(p|h\d|ul|ol|li|blockquote|img|a|strong|em|br)\b/i.test(raw);
  if (looksLikeHTML) return raw;
  return raw.split(/\n\s*\n/).map(p => `<p>${esc(p.trim()).replace(/\n/g,'<br>')}</p>`).join('');
}

function relCardHTML(p){
  return `
    <a href="/influencers/${encodeURIComponent(p.id)}" class="inf-list-card">
      <div class="inf-list-img">
        <img src="${esc(p.image_url || fallbackImg)}" alt="${esc(p.name)}" onerror="this.src='${fallbackImg}'">
        ${p.flag ? `<span class="inf-list-img-flag">${esc(p.flag)}</span>` : ''}
        <div class="inf-list-img-ov"></div>
        ${p.platform ? `<span class="inf-list-img-platform">${esc(p.platform)}</span>` : ''}
      </div>
      <div class="inf-list-body">
        <div class="inf-list-cat">${esc(p.role || 'مؤثر')}</div>
        <h3 class="inf-list-name">${esc(p.name)}</h3>
        <div class="inf-list-handle">${esc(p.handle || '')}</div>
        ${p.followers ? `<div class="inf-list-followers"><div class="inf-list-followers-num">${esc(p.followers)}</div><div class="inf-list-followers-lbl">متابع</div></div>` : ''}
      </div>
    </a>`;
}

async function loadRelated(currentId){
  try{
    const res = await fetch('/api/people?category=influencer&limit=12');
    const j = await res.json();
    const items = (j.data || []).filter(p => p.id !== currentId).slice(0,4);
    if (!items.length) return '';
    return `
      <section class="related-section">
        <h2 class="related-title">مؤثرون <em>آخرون</em></h2>
        <div class="related-grid">${items.map(relCardHTML).join('')}</div>
      </section>
      <style>
        .related-grid .inf-list-img{height:260px;}
        .related-grid .inf-list-body{padding:1.3rem;}
        .inf-list-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:1px;background:var(--rule);}
        .inf-list-card{background:var(--white);cursor:pointer;overflow:hidden;transition:background .25s;text-decoration:none;color:inherit;display:flex;flex-direction:column;}
        .inf-list-card:hover{background:var(--cream);}
        .inf-list-img{overflow:hidden;position:relative;}
        .inf-list-img img{width:100%;height:100%;object-fit:cover;object-position:top;transition:transform .7s;}
        .inf-list-card:hover .inf-list-img img{transform:scale(1.07);}
        .inf-list-img-ov{position:absolute;inset:0;background:linear-gradient(to top,rgba(13,12,10,.85) 0%,transparent 45%);}
        .inf-list-img-flag{position:absolute;top:1rem;right:1rem;font-size:1.5rem;background:rgba(255,255,255,.95);width:42px;height:42px;border-radius:50%;display:flex;align-items:center;justify-content:center;}
        .inf-list-img-platform{position:absolute;bottom:1rem;left:1rem;background:var(--gold);color:var(--ink);font-size:.65rem;font-weight:700;padding:.3rem .8rem;font-family:'Cairo',sans-serif;letter-spacing:.05em;}
        .inf-list-body{text-align:right;flex:1;display:flex;flex-direction:column;}
        .inf-list-cat{font-size:.68rem;font-weight:700;color:var(--gold);margin-bottom:.4rem;font-family:'Cairo',sans-serif;letter-spacing:.05em;}
        .inf-list-name{font-family:'Amiri',serif;font-size:1.3rem;font-weight:700;color:var(--ink);margin-bottom:.3rem;}
        .inf-list-card:hover .inf-list-name{color:var(--gold);}
        .inf-list-handle{font-size:.78rem;color:var(--ink-soft);margin-bottom:1rem;font-family:'Cairo',sans-serif;}
        .inf-list-followers{display:flex;align-items:baseline;gap:.5rem;border-top:1px solid var(--rule);padding-top:.9rem;margin-top:auto;flex-direction:row-reverse;}
        .inf-list-followers-num{font-family:'Playfair Display',serif;font-size:1.5rem;font-weight:900;color:var(--ink);}
        .inf-list-followers-lbl{font-size:.6rem;letter-spacing:.08em;color:var(--gold);font-family:'Cairo',sans-serif;font-weight:600;}
      </style>`;
  }catch{return '';}
}

async function init(){
  const id = getId();
  const main = document.getElementById('main');
  if (!id){
    main.innerHTML = `<div class="notfound"><h1>الملف غير موجود</h1><p>لم نتمكن من العثور على الملف.</p><a href="/influencers">عرض جميع المؤثرين</a></div>`;
    return;
  }
  try{
    const res = await fetch(`/api/people/${encodeURIComponent(id)}`);
    if (!res.ok) throw new Error('not found');
    const j = await res.json();
    const p = j.data;
    if (!p || p.category !== 'influencer') throw new Error('wrong category');

    document.title = `${p.name} — مجلة العرب`;
    const related = await loadRelated(p.id);

    main.innerHTML = `
      <section class="profile-hero">
        <div class="profile-hero-bg" style="background-image:url('${esc(p.image_url || fallbackImg)}')"></div>
        <div class="profile-hero-grid">
          <div class="profile-portrait">
            <img src="${esc(p.image_url || fallbackImg)}" alt="${esc(p.name)}" onerror="this.src='${fallbackImg}'">
            ${p.flag ? `<span class="profile-portrait-flag">${esc(p.flag)}</span>` : ''}
            ${p.platform ? `<span class="profile-portrait-platform">${esc(p.platform)}</span>` : ''}
          </div>
          <div class="profile-info">
            <div class="profile-eyebrow">Influencer Profile · ملف مؤثر</div>
            <div class="profile-role">${esc(p.role || 'مؤثر')}</div>
            <h1 class="profile-name">${esc(p.name)}</h1>
            ${p.handle ? `<div class="profile-handle">${esc(p.handle)}</div>` : ''}
            ${p.excerpt ? `<p class="profile-tagline">${esc(p.excerpt)}</p>` : ''}
            <div class="profile-meta">
              ${p.country ? `<span>📍 <b>${esc(p.country)}</b></span>` : ''}
              ${p.platform ? `<span>📱 <b>${esc(p.platform)}</b></span>` : ''}
              ${p.followers ? `<span>👥 <b>${esc(p.followers)}</b> متابع</span>` : ''}
            </div>
          </div>
        </div>
      </section>

      <section class="profile-stats">
        <div class="profile-stats-inner">
          ${p.followers ? `<div class="pstat"><div class="pstat-num">${esc(p.followers)}</div><div class="pstat-lbl">المتابعون</div></div>` : ''}
          ${p.platform ? `<div class="pstat"><div class="pstat-num small">${esc(p.platform)}</div><div class="pstat-lbl">المنصة الرئيسية</div></div>` : ''}
          ${p.stat ? `<div class="pstat"><div class="pstat-num">${esc(p.stat)}</div><div class="pstat-lbl">${esc(p.stat_label || 'إحصاء')}</div></div>` : ''}
          ${(!p.followers && !p.platform && !p.stat) ? '<div class="pstat" style="grid-column:1/-1;color:rgba(248,244,238,.4);">لم تتم إضافة إحصائيات بعد.</div>' : ''}
        </div>
      </section>

      <section class="profile-body-section">
        <article class="profile-body-wrap">
          <div class="profile-body">${bioHTML(p.bio)}</div>
        </article>
      </section>

      ${related}
    `;

    gsap.from('.profile-eyebrow,.profile-role,.profile-name,.profile-handle,.profile-tagline,.profile-meta', {y:30,opacity:0,stagger:.1,duration:.85,ease:'power3.out'});
    gsap.from('.profile-portrait', {x:-40,opacity:0,duration:1,ease:'power3.out'});
    gsap.from('.pstat', {y:30,opacity:0,stagger:.12,duration:.7,ease:'power3.out',scrollTrigger:{trigger:'.profile-stats',start:'top 85%'}});
  }catch(e){
    console.error(e);
    main.innerHTML = `<div class="notfound"><h1>الملف غير موجود</h1><p>لم نتمكن من العثور على المؤثر.</p><a href="/influencers">عرض جميع المؤثرين</a></div>`;
  }
}

gsap.registerPlugin(ScrollTrigger);
init();

const cur=document.getElementById('cur'),ring=document.getElementById('curRing');
let mx=0,my=0,rx=0,ry=0;
document.addEventListener('mousemove',e=>{mx=e.clientX;my=e.clientY;cur.style.left=mx+'px';cur.style.top=my+'px';});
(function a(){rx+=(mx-rx)*.1;ry+=(my-ry)*.1;ring.style.left=rx+'px';ring.style.top=ry+'px';requestAnimationFrame(a);})();
document.addEventListener('mouseover',e=>{
  if(e.target.closest('a,button,.inf-list-card')){
    gsap.to(cur,{width:16,height:16,duration:.2});gsap.to(ring,{width:52,height:52,opacity:.3,duration:.3});
  }else{
    gsap.to(cur,{width:8,height:8,duration:.2});gsap.to(ring,{width:32,height:32,opacity:.5,duration:.3});
  }
});
