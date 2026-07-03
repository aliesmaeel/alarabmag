<?php

namespace App\Http\Controllers;

use App\Services\SeoService;
use App\Support\EditorialPage;
use Illuminate\Http\Response;
use Illuminate\View\View;

class StaticPageController extends Controller
{
    public function about(): View
    {
        return $this->render('about', 'مجلة العرب — عن المجلة', 'تعرّف على مجلة العرب (Al Arab Magazine) — المجلة العربية الأولى التي تحتفي بالإنسان العربي المتميّز، صادرة من دبي للعالم العربي.', view('site.pages.content.about')->render());
    }

    public function editorial(): View
    {
        return $this->render(
            'editorial',
            EditorialPage::get('editorial_title'),
            EditorialPage::get('editorial_lead'),
            view('site.pages.content.editorial')->render(),
        );
    }

    public function privacy(): View
    {
        return $this->render('privacy', 'سياسة الخصوصية', 'كيف نجمع ونستخدم ونحمي بياناتك عند زيارة مجلة العرب.', view('site.pages.content.privacy')->render(), '13 يونيو 2026');
    }

    public function terms(): View
    {
        return $this->render('terms', 'شروط الاستخدام', 'الشروط والأحكام التي تحكم استخدامك لموقع مجلة العرب.', view('site.pages.content.terms')->render(), '13 يونيو 2026');
    }

    public function contact(): View
    {
        return $this->render('contact', 'اتصل بنا', 'تواصل مع فريق مجلة العرب — نحن هنا للاستفسارات والاقتراحات.', view('site.pages.content.contact')->render());
    }

    public function advertise(): View
    {
        return $this->render('advertise', 'الإعلان معنا', 'فرص الإعلان والشراكات مع مجلة العرب — الوصول إلى جمهور عربي متميّز.', view('site.pages.content.advertise')->render());
    }

    public function adsTxt(): Response
    {
        return response(
            "google.com, pub-6158011037590169, DIRECT, f08c47fec0942fa0\n",
            200,
            ['Content-Type' => 'text/plain; charset=UTF-8'],
        );
    }

    protected function render(string $key, string $title, string $lead, string $content, ?string $updated = null): View
    {
        $seo = app(SeoService::class)->staticPage($key, $title, $lead);

        return view('site.pages.static', [
            'seo' => $seo,
            'activeNav' => null,
            'footerVariant' => 'compact',
            'pageTitle' => $title,
            'pageLead' => $lead,
            'pageContent' => $content,
            'pageUpdated' => $updated,
        ]);
    }
}
