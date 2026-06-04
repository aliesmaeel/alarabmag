/**
 * Shared site chrome: custom cursor (always), ticker + newsletter (when GSAP is available).
 */
(function () {
    const INTERACTIVE_SELECTOR = [
        'a',
        'button',
        'select',
        'input[type="range"]',
        '.cat-card',
        '.feat-card',
        '.inf-card',
        '.ap',
        '.doc-card',
        '.fash',
        '.news-card',
        '.list-card',
        '.inf-list-card',
        '.doc-list-card',
        '.news-side-item',
        '.news-side__more',
        '.video-player',
        '.vp-btn',
        '.video-player__big-play',
        '.vp-share-item',
        '.interview-about',
    ].join(',');

    const cur = document.getElementById('cur');
    const ring = document.getElementById('curRing');

    function setCursorHover(hover) {
        if (!cur || !ring) return;
        const dot = hover ? 16 : 8;
        const ringSize = hover ? 52 : 32;
        const ringOpacity = hover ? 0.3 : 0.5;

        if (typeof gsap !== 'undefined') {
            gsap.to(cur, { width: dot, height: dot, duration: 0.2 });
            gsap.to(ring, { width: ringSize, height: ringSize, opacity: ringOpacity, duration: 0.3 });
        } else {
            cur.style.width = `${dot}px`;
            cur.style.height = `${dot}px`;
            ring.style.width = `${ringSize}px`;
            ring.style.height = `${ringSize}px`;
            ring.style.opacity = String(ringOpacity);
        }
    }

    if (cur && ring) {
        let mx = 0;
        let my = 0;
        let rx = 0;
        let ry = 0;

        document.addEventListener('mousemove', (e) => {
            mx = e.clientX;
            my = e.clientY;
            cur.style.left = `${mx}px`;
            cur.style.top = `${my}px`;
        });

        (function animateRing() {
            rx += (mx - rx) * 0.1;
            ry += (my - ry) * 0.1;
            ring.style.left = `${rx}px`;
            ring.style.top = `${ry}px`;
            requestAnimationFrame(animateRing);
        })();

        document.addEventListener('mouseover', (e) => {
            setCursorHover(!!e.target.closest(INTERACTIVE_SELECTOR));
        });
    }

    if (typeof gsap === 'undefined') return;

    gsap.registerPlugin(ScrollTrigger);

    const tr = document.getElementById('tickerTrack');
    if (tr) {
        gsap.to(tr, { x: -tr.scrollWidth / 2, duration: 38, repeat: -1, ease: 'none' });
    }

    const nlBtn = document.getElementById('nlBtn');
    const nlEmail = document.getElementById('nlEmail');
    if (nlBtn && nlEmail) {
        nlBtn.addEventListener('click', () => {
            if (nlEmail.value && nlEmail.value.includes('@')) {
                gsap.to(nlEmail, {
                    opacity: 0,
                    duration: 0.2,
                    onComplete() {
                        nlEmail.value = '✓ أهلاً بك في مجلة العرب — تفقّد بريدك الإلكتروني';
                        nlEmail.style.color = '#d4ae5a';
                        gsap.to(nlEmail, { opacity: 1, duration: 0.4 });
                    },
                });
            }
        });
    }
})();
