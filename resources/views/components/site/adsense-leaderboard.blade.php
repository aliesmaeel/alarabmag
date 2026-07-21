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
                if (!el || el.tagName !== 'INS') return;

                function pushWhenReady(attempt) {
                    if (el.offsetWidth > 0) {
                        (window.adsbygoogle = window.adsbygoogle || []).push({});
                        return;
                    }
                    if ((attempt || 0) >= 40) return;
                    requestAnimationFrame(function () {
                        pushWhenReady((attempt || 0) + 1);
                    });
                }

                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', function () {
                        pushWhenReady(0);
                    });
                } else {
                    pushWhenReady(0);
                }
            })();
        </script>
    </div>
@endif
