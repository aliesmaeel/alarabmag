const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
const fallbackImg = 'https://images.unsplash.com/photo-1518834107812-67b0b7c58434?auto=format&fit=crop&w=1400&q=85';
const pad = n => String(n).padStart(2,'0');

function getId(){
  if (window.SITE_PERSON_ID != null) return String(window.SITE_PERSON_ID);
  const m = location.pathname.match(/\/artists\/(\d+)/);
  if (m) return m[1];
  return new URLSearchParams(location.search).get('id');
}

function bioHTML(raw){
  if (!raw) return '<p>لم تتم إضافة سيرة بعد.</p>';
  const looksLikeHTML = /<\/?(p|h\d|ul|ol|li|blockquote|img|a|strong|em|br)\b/i.test(raw);
  if (looksLikeHTML) return raw;
  return raw.split(/\n\s*\n/).map(p => `<p>${esc(p.trim()).replace(/\n/g,'<br>')}</p>`).join('');
}

function relCardHTML(a, idx){
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

async function loadRelated(currentId){
  try{
    const res = await fetch('/api/people?category=artist&limit=12');
    const j = await res.json();
    const items = (j.data || []).filter(a => a.id !== currentId).slice(0,3);
    if (!items.length) return '';
    return `
      <section class="related-section">
        <h2 class="related-title">فنانون <em>آخرون</em></h2>
        <div class="related-grid">${items.map((a, i) => relCardHTML(a, i)).join('')}</div>
      </section>
      <style>
        .art-list-card{background:var(--ink);cursor:pointer;overflow:hidden;transition:background .35s;text-decoration:none;color:inherit;display:flex;flex-direction:column;position:relative;}
        .art-list-card:hover{background:rgba(184,146,42,.07);}
        .art-list-img{height:340px;overflow:hidden;position:relative;}
        .art-list-img img{width:100%;height:100%;object-fit:cover;object-position:top;filter:grayscale(35%);transition:transform .9s,filter .6s;}
        .art-list-card:hover .art-list-img img{transform:scale(1.08);filter:grayscale(0%);}
        .art-list-img-ov{position:absolute;inset:0;background:linear-gradient(to top,rgba(13,12,10,.92) 0%,transparent 50%);}
        .art-list-num{position:absolute;top:1rem;left:1.5rem;font-family:'Playfair Display',serif;font-size:3rem;font-weight:900;color:rgba(184,146,42,.18);line-height:1;}
        .art-list-card:hover .art-list-num{color:rgba(184,146,42,.55);}
        .art-list-body{padding:1.6rem;text-align:right;}
        .art-list-role{font-size:.7rem;font-weight:700;letter-spacing:.06em;color:var(--gold);text-transform:uppercase;margin-bottom:.5rem;font-family:'Cairo',sans-serif;}
        .art-list-name{font-family:'Amiri',serif;font-size:1.4rem;font-weight:700;color:var(--cream);margin-bottom:.4rem;}
        .art-list-country{font-size:.78rem;color:rgba(248,244,238,.4);margin-bottom:.8rem;font-family:'Cairo',sans-serif;}
        .art-list-excerpt{font-size:.85rem;line-height:1.8;color:rgba(248,244,238,.5);font-family:'Cairo',sans-serif;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;}
      </style>`;
  }catch{return '';}
}

async function init(){
  const id = getId();
  const main = document.getElementById('main');
  if (!id){
    main.innerHTML = `<div class="notfound"><h1>الملف غير موجود</h1><p>لم نتمكن من العثور على الملف المطلوب.</p><a href="/artists">عرض جميع الفنانين</a></div>`;
    return;
  }
  try{
    const res = await fetch(`/api/people/${encodeURIComponent(id)}`);
    if (!res.ok) throw new Error('not found');
    const j = await res.json();
    const a = j.data;
    if (!a || a.category !== 'artist') throw new Error('wrong category');

    document.title = `${a.name} — مجلة العرب`;
    const related = await loadRelated(a.id);

    main.innerHTML = `
      <section class="art-hero">
        <div class="art-hero-img"><img src="${esc(a.image_url || fallbackImg)}" alt="${esc(a.name)}" onerror="this.src='${fallbackImg}'"></div>
        <div class="art-hero-overlay"></div>
        <div class="art-hero-inner">
          <div class="art-hero-num">01</div>
          <div class="art-hero-eyebrow">Artist Profile · ملف فنان</div>
          <div class="art-hero-role">${esc(a.role || 'فنان')}</div>
          <h1 class="art-hero-name">${esc(a.name)}</h1>
          ${a.name_en ? `<div class="art-hero-name-en">${esc(a.name_en)}</div>` : ''}
          ${a.excerpt ? `<p class="art-hero-tagline">"${esc(a.excerpt)}"</p>` : ''}
          <div class="art-hero-meta">
            ${a.country ? `<span>📍 ${a.flag ? esc(a.flag) + ' ' : ''}<b>${esc(a.country)}</b></span>` : ''}
            ${a.role ? `<span>🎨 <b>${esc(a.role)}</b></span>` : ''}
            ${a.stat ? `<span><b>${esc(a.stat)}</b> ${esc(a.stat_label || '')}</span>` : ''}
          </div>
        </div>
      </section>

      <section class="art-body-section">
        <article class="art-body-wrap">
          ${a.excerpt ? `<div class="art-body-pullquote">"<em>${esc(a.excerpt)}</em>"</div>` : ''}
          <div class="art-body">${bioHTML(a.bio)}</div>
        </article>
      </section>

      ${(a.stat || a.role || a.country) ? `
        <section class="art-strip">
          <div class="art-strip-inner">
            ${a.stat ? `<div><div class="astat-num">${esc(a.stat)}</div><div class="astat-lbl">${esc(a.stat_label || 'إنجاز')}</div></div>` : ''}
            ${a.role ? `<div><div class="astat-num small">${esc(a.role)}</div><div class="astat-lbl">التخصص الفني</div></div>` : ''}
            ${a.country ? `<div><div class="astat-num small">${a.flag ? esc(a.flag) + ' ' : ''}${esc(a.country)}</div><div class="astat-lbl">البلد</div></div>` : ''}
          </div>
        </section>` : ''}

      ${related}
    `;

    gsap.from('.art-hero-eyebrow,.art-hero-role,.art-hero-name,.art-hero-name-en,.art-hero-tagline,.art-hero-meta', {y:30,opacity:0,stagger:.12,duration:.85,ease:'power3.out'});
    gsap.from('.art-hero-num', {scale:.8,opacity:0,duration:1.1,ease:'power3.out'});
    gsap.from('.art-body-pullquote', {y:30,opacity:0,duration:.9,ease:'power3.out',scrollTrigger:{trigger:'.art-body-section',start:'top 80%'}});
  }catch(e){
    console.error(e);
    main.innerHTML = `<div class="notfound"><h1>الملف غير موجود</h1><p>لم نتمكن من العثور على الفنان.</p><a href="/artists">عرض جميع الفنانين</a></div>`;
  }
}

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
