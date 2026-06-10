function initNewsDetails() {
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
  gsap.from('.article-kicker,.article-title,.article-subtitle,.article-byline', {
    y: 30, opacity: 0, stagger: 0.12, duration: 0.85, ease: 'power3.out',
  });
  gsap.from('.article-lede,.article-body p:first-child', {
    y: 20, opacity: 0, duration: 0.8, ease: 'power3.out', delay: 0.3,
    scrollTrigger: { trigger: '.article-section', start: 'top 80%' },
  });
}

if (typeof gsap !== 'undefined') {
  initNewsDetails();
} else {
  document.addEventListener('DOMContentLoaded', initNewsDetails);
}
