/**
 * Shared site chrome: cursor, ticker (when present), newsletter form.
 */
(function () {
    if (typeof gsap === 'undefined') return;

    gsap.registerPlugin(ScrollTrigger);

    const tr = document.getElementById('tickerTrack');
    if (tr) {
        gsap.to(tr, { x: -tr.scrollWidth / 2, duration: 38, repeat: -1, ease: 'none' });
    }

    const cur = document.getElementById('cur');
    const ring = document.getElementById('curRing');
    if (cur && ring) {
        let mx = 0, my = 0, rx = 0, ry = 0;
        document.addEventListener('mousemove', (e) => {
            mx = e.clientX;
            my = e.clientY;
            cur.style.left = mx + 'px';
            cur.style.top = my + 'px';
        });
        (function animateRing() {
            rx += (mx - rx) * 0.1;
            ry += (my - ry) * 0.1;
            ring.style.left = rx + 'px';
            ring.style.top = ry + 'px';
            requestAnimationFrame(animateRing);
        })();
        document.addEventListener('mouseover', (e) => {
            const interactive = e.target.closest(
                'a,button,.cat-card,.feat-card,.inf-card,.ap,.doc-card,.fash,.news-card,.list-card,.inf-list-card,.doc-list-card'
            );
            if (interactive) {
                gsap.to(cur, { width: 16, height: 16, duration: 0.2 });
                gsap.to(ring, { width: 52, height: 52, opacity: 0.3, duration: 0.3 });
            } else {
                gsap.to(cur, { width: 8, height: 8, duration: 0.2 });
                gsap.to(ring, { width: 32, height: 32, opacity: 0.5, duration: 0.3 });
            }
        });
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
