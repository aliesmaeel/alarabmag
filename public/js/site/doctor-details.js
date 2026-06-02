const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
const fallbackImg = 'https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?auto=format&fit=crop&w=900&q=80';

function getId(){
  if (window.SITE_PERSON_ID != null) return String(window.SITE_PERSON_ID);
  const m = location.pathname.match(/\/doctors\/(\d+)/);
  if (m) return m[1];
  return new URLSearchParams(location.search).get('id');
}

function bioHTML(raw){
  if (!raw) return '<p>لم تتم إضافة سيرة بعد.</p>';
  const looksLikeHTML = /<\/?(p|h\d|ul|ol|li|blockquote|img|a|strong|em|br)\b/i.test(raw);
  if (looksLikeHTML) return raw;
  return raw.split(/\n\s*\n/).map(p => `<p>${esc(p.trim()).replace(/\n/g,'<br>')}</p>`).join('');
}

function relCardHTML(d){
  return `
    <a href="/doctors/${encodeURIComponent(d.id)}" class="doc-list-card">
      <div class="doc-list-img">
        <img src="${esc(d.image_url || fallbackImg)}" alt="${esc(d.name)}" onerror="this.src='${fallbackImg}'">
        ${d.flag ? `<span class="doc-list-img-flag">${esc(d.flag)}</span>` : ''}
        <div class="doc-list-img-ov"></div>
      </div>
      <div class="doc-list-body">
        <div class="doc-list-spec">${esc(d.specialty || d.role || '')}</div>
        <h3 class="doc-list-name">${esc(d.name)}</h3>
        <div class="doc-list-hospital">${esc(d.hospital || d.country || '')}</div>
        ${d.badge ? `<span class="doc-list-badge">${esc(d.badge)}</span>` : ''}
      </div>
    </a>`;
}

async function loadRelated(currentId){
  try{
    const res = await fetch('/api/people?category=doctor&limit=12');
    const j = await res.json();
    const items = (j.data || []).filter(d => d.id !== currentId).slice(0,4);
    if (!items.length) return '';
    return `
      <section class="related-section">
        <h2 class="related-title">أطباء <em>آخرون</em></h2>
        <div class="related-grid">${items.map(relCardHTML).join('')}</div>
      </section>`;
  }catch{return '';}
}

async function init(){
  const id = getId();
  const main = document.getElementById('main');
  if (!id){
    main.innerHTML = `<div class="notfound"><h1>الملف غير موجود</h1><p>لم نتمكن من العثور على الملف المطلوب.</p><a href="/doctors">عرض جميع الأطباء</a></div>`;
    return;
  }
  try{
    const res = await fetch(`/api/people/${encodeURIComponent(id)}`);
    if (!res.ok) throw new Error('not found');
    const j = await res.json();
    const d = j.data;
    if (!d || d.category !== 'doctor') throw new Error('wrong category');

    document.title = `${d.name} — مجلة العرب`;
    const related = await loadRelated(d.id);

    main.innerHTML = `
      <section class="profile-hero">
        <div class="profile-hero-bg" style="background-image:url('${esc(d.image_url || fallbackImg)}')"></div>
        <div class="profile-hero-grid">
          <div class="profile-portrait">
            <img src="${esc(d.image_url || fallbackImg)}" alt="${esc(d.name)}" onerror="this.src='${fallbackImg}'">
            ${d.flag ? `<span class="profile-portrait-flag">${esc(d.flag)}</span>` : ''}
          </div>
          <div class="profile-info">
            <div class="profile-eyebrow">Doctor Profile · ملف طبيب</div>
            <div class="profile-spec">${esc(d.specialty || d.role || 'طب عام')}</div>
            <h1 class="profile-name">${esc(d.name)}</h1>
            ${d.name_en ? `<div class="profile-name-en">${esc(d.name_en)}</div>` : ''}
            ${d.excerpt ? `<p class="profile-tagline">${esc(d.excerpt)}</p>` : ''}
            <div class="profile-meta">
              ${d.hospital ? `<span>🏥 <b>${esc(d.hospital)}</b></span>` : ''}
              ${d.country ? `<span>📍 <b>${esc(d.country)}</b></span>` : ''}
              ${d.badge ? `<span>🏅 <b>${esc(d.badge)}</b></span>` : ''}
            </div>
          </div>
        </div>
      </section>

      ${(d.stat || d.specialty || d.hospital || d.badge) ? `
        <section class="profile-stats">
          <div class="profile-stats-inner">
            ${d.stat ? `<div class="pstat"><div class="pstat-num">${esc(d.stat)}</div><div class="pstat-lbl">${esc(d.stat_label || 'إنجاز')}</div></div>` : ''}
            ${d.specialty ? `<div class="pstat"><div class="pstat-num small">${esc(d.specialty)}</div><div class="pstat-lbl">التخصص</div></div>` : ''}
            ${d.hospital ? `<div class="pstat"><div class="pstat-num small">${esc(d.hospital)}</div><div class="pstat-lbl">المستشفى</div></div>` : ''}
            ${d.badge ? `<div class="pstat"><div class="pstat-num small">${esc(d.badge)}</div><div class="pstat-lbl">التكريم</div></div>` : ''}
          </div>
        </section>` : ''}

      <section class="profile-body-section">
        <article class="profile-body-wrap">
          <div class="profile-body">${bioHTML(d.bio)}</div>
        </article>
      </section>

      ${related}
    `;

    gsap.from('.profile-eyebrow,.profile-spec,.profile-name,.profile-name-en,.profile-tagline,.profile-meta', {y:30,opacity:0,stagger:.1,duration:.85,ease:'power3.out'});
    gsap.from('.profile-portrait', {x:-40,opacity:0,duration:1,ease:'power3.out'});
    gsap.from('.pstat', {y:30,opacity:0,stagger:.1,duration:.7,ease:'power3.out',scrollTrigger:{trigger:'.profile-stats',start:'top 85%'}});
  }catch(e){
    console.error(e);
    main.innerHTML = `<div class="notfound"><h1>الملف غير موجود</h1><p>لم نتمكن من العثور على الطبيب المطلوب.</p><a href="/doctors">عرض جميع الأطباء</a></div>`;
  }
}

gsap.registerPlugin(ScrollTrigger);
init();
