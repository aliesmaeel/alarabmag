/**
 * Shared site chrome: custom cursor (always), newsletter signup, ticker (when GSAP is available).
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

    const nlForm = document.getElementById('nlForm');
    const nlBtn = document.getElementById('nlBtn');
    const nlEmail = document.getElementById('nlEmail');
    const nlStatus = document.getElementById('nlStatus');

    function setNewsletterStatus(message, isError) {
        if (!nlStatus) return;
        nlStatus.textContent = message || '';
        nlStatus.classList.toggle('is-error', !!isError);
        if (message) {
            nlStatus.hidden = false;
            nlStatus.classList.remove('is-hidden');
        } else {
            nlStatus.hidden = true;
            nlStatus.classList.add('is-hidden');
        }
    }

    if (nlForm && nlBtn && nlEmail) {
        nlForm.addEventListener('submit', async (event) => {
            event.preventDefault();

            const email = (nlEmail.value || '').trim();
            if (!email || !email.includes('@')) {
                setNewsletterStatus('أدخل بريداً إلكترونياً صالحاً.', true);
                return;
            }

            nlBtn.disabled = true;
            setNewsletterStatus('', false);

            try {
                const res = await fetch(nlForm.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': nlForm.dataset.csrf || '',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({ email }),
                });

                const data = await res.json().catch(() => ({}));

                if (res.ok && data.ok) {
                    setNewsletterStatus(data.message || 'أهلاً بك في مجلة العرب — تم تسجيل اشتراكك.', false);
                    return;
                }

                const fieldError = data.errors?.email?.[0] || data.message || 'أدخل بريداً إلكترونياً صالحاً.';
                setNewsletterStatus(fieldError, true);
            } catch {
                setNewsletterStatus('تعذّر الاتصال. حاول مرة أخرى.', true);
            } finally {
                nlBtn.disabled = false;
            }
        });
    }

    if (typeof gsap === 'undefined') return;

    gsap.registerPlugin(ScrollTrigger);

    const tr = document.getElementById('tickerTrack');
    if (tr) {
        const speed = Math.min(10, Math.max(1, Number(tr.dataset.speed) || 5));
        const duration = 80 - ((speed - 1) / 9) * 68;
        gsap.fromTo(tr, { x: -tr.scrollWidth / 2 }, { x: 0, duration, repeat: -1, ease: 'none' });
    }
})();
