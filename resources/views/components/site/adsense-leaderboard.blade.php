@if (config('ads.enabled') && filled(config('ads.client')) && filled(config('ads.slot')))
    <div class="site-ad site-ad--leaderboard">
        <ins class="adsbygoogle"
             style="display:block;width:100%;min-height:90px"
             data-ad-client="{{ config('ads.client') }}"
             data-ad-slot="{{ config('ads.slot') }}"
             data-ad-format="auto"
             data-full-width-responsive="true"></ins>
        <script>
            (function () {
                var el = document.currentScript && document.currentScript.previousElementSibling;
                if (!el || el.tagName !== 'INS' || el.dataset.adInitialized === '1') return;

                var MIN_WIDTH = 250;
                var MAX_ATTEMPTS = 60;

                function ready() {
                    return el.getBoundingClientRect().width >= MIN_WIDTH;
                }

                function pushAd() {
                    if (el.dataset.adInitialized === '1') return;
                    if (!ready()) return false;
                    el.dataset.adInitialized = '1';
                    (window.adsbygoogle = window.adsbygoogle || []).push({});
                    return true;
                }

                function waitAndPush(attempt) {
                    if (pushAd()) return;
                    if ((attempt || 0) >= MAX_ATTEMPTS) return;
                    requestAnimationFrame(function () {
                        waitAndPush((attempt || 0) + 1);
                    });
                }

                function start() {
                    if (pushAd()) return;
                    if (typeof ResizeObserver === 'function') {
                        var ro = new ResizeObserver(function () {
                            if (pushAd()) ro.disconnect();
                        });
                        ro.observe(el);
                    }
                    waitAndPush(0);
                }

                if (document.readyState === 'complete') {
                    start();
                } else {
                    window.addEventListener('load', start, { once: true });
                }
            })();
        </script>
    </div>
@endif
