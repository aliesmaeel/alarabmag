function initBlogDetails() {
  const copyBtn = document.getElementById('copyBtn');
  if (copyBtn) {
    copyBtn.addEventListener('click', async () => {
      try {
        await navigator.clipboard.writeText(location.href);
        copyBtn.textContent = '✓';
        setTimeout(() => { copyBtn.textContent = '⎘'; }, 1600);
      } catch {}
    });
  }

  if (typeof gsap === 'undefined') return;

  gsap.registerPlugin(ScrollTrigger);
  gsap.from('.blog-hero-eyebrow,.blog-hero-title,.blog-hero-excerpt,.blog-hero-meta', {
    y: 30, opacity: 0, stagger: 0.12, duration: 0.85, ease: 'power3.out',
  });
  gsap.from('.blog-cover img', {
    opacity: 0, scale: 1.04, duration: 1, ease: 'power3.out', delay: 0.3,
  });
  gsap.from('.blog-body p:first-child', {
    y: 20, opacity: 0, duration: 0.8, ease: 'power3.out', delay: 0.4,
    scrollTrigger: { trigger: '.blog-section', start: 'top 80%' },
  });
}

if (typeof gsap !== 'undefined') {
  initBlogDetails();
} else {
  document.addEventListener('DOMContentLoaded', initBlogDetails);
}
