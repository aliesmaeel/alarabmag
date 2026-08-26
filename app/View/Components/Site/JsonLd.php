<?php

namespace App\View\Components\Site;

use App\Services\SeoService;
use App\Support\SeoMeta;
use Illuminate\View\Component;

class JsonLd extends Component
{
    public function __construct(
        public ?SeoMeta $seo = null,
        public mixed $entity = null,
    ) {}

    public function render(): string
    {
        $html = '';

        foreach (app(SeoService::class)->jsonLdScripts($this->seo, $this->entity) as $json) {
            $html .= '<script type="application/ld+json">'.$json.'</script>'."\n";
        }

        return $html;
    }
}
