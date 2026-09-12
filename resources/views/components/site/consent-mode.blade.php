{{-- Google Consent Mode v2 defaults + Funding Choices (certified CMP) bootstrap.
     MUST be the first script in <head>, before gtag.js and the AdSense tag. --}}
<script>
(function () {
    window.dataLayer = window.dataLayer || [];
    function gtag() { dataLayer.push(arguments); }

    // Deny storage until the CMP records a choice. Google's message updates this.
    gtag('consent', 'default', {
        ad_storage: 'denied',
        ad_user_data: 'denied',
        ad_personalization: 'denied',
        analytics_storage: 'denied',
        functionality_storage: 'granted',
        security_storage: 'granted',
        wait_for_update: 2000
    });

    // Serve non-personalised ads and drop ad identifiers while consent is unknown.
    gtag('set', 'ads_data_redaction', true);
    gtag('set', 'url_passthrough', true);

    // Funding Choices queue — the message itself is delivered by the AdSense tag
    // once a GDPR message is published in AdSense › Privacy & messaging.
    window.googlefc = window.googlefc || {};
    window.googlefc.callbackQueue = window.googlefc.callbackQueue || [];

    // Re-open the consent dialog. Returns false when no CMP message is available
    // (e.g. visitors outside the EEA/UK/CH), so callers can show a fallback.
    window.openCookieSettings = function () {
        if (!window.googlefc) return false;

        if (typeof window.googlefc.showRevocationMessage === 'function') {
            window.googlefc.showRevocationMessage();
            return true;
        }

        window.googlefc.callbackQueue.push({
            CONSENT_DATA_READY: function () {
                if (typeof window.googlefc.showRevocationMessage === 'function') {
                    window.googlefc.showRevocationMessage();
                }
            }
        });

        return typeof window.googlefc.showRevocationMessage === 'function';
    };
})();
</script>
