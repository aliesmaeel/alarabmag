@if (config('ads.enabled') && filled(config('ads.client')) && filled(config('ads.slot')))
    <div class="site-ad site-ad--leaderboard">
        <ins class="adsbygoogle"
             style="display:block"
             data-ad-client="{{ config('ads.client') }}"
             data-ad-slot="{{ config('ads.slot') }}"
             data-ad-format="auto"
             data-full-width-responsive="true"></ins>
        <script>(adsbygoogle = window.adsbygoogle || []).push({});</script>
    </div>
@endif
