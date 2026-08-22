<?php

namespace App\View\Components\Site;

use App\Models\Setting;
use App\Support\SiteTicker;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class Ticker extends Component
{
    public string $label;

    /** @var Collection<int, string> */
    public Collection $texts;

    public int $speed;

    public function __construct(?string $label = null)
    {
        $settings = Setting::getAllAsArray();

        $this->label = $label ?? ($settings['ticker_label'] ?? 'عاجل');
        $this->speed = max(1, min(10, (int) ($settings['ticker_speed'] ?? 5)));
        $this->texts = collect(SiteTicker::texts());
    }

    public function render(): View
    {
        return view('components.site.ticker-bar');
    }
}
