<?php

namespace App\Http\Controllers;

use App\Models\MagazineIssue;
use App\Services\FileUploadService;
use App\Services\SeoService;
use Illuminate\View\View;

class MagazineController extends Controller
{
    public function __construct(
        protected SeoService $seo,
        protected FileUploadService $files,
    ) {}

    public function index(): View
    {
        $issues = MagazineIssue::query()
            ->published()
            ->ordered()
            ->get();

        return view('site.magazine', [
            'seo' => $this->seo->page('magazine'),
            'issues' => $issues,
            'activeNav' => 'magazine',
            'newsletterHeadline' => 'اقرأ المجلة<br><em>أينما كنت</em>',
            'newsletterSub' => 'اشترك في النشرة لتصلك أحدث الأعداد والمحتوى الحصري.',
        ]);
    }

    public function show(MagazineIssue $issue): View
    {
        abort_unless($issue->is_published && filled($issue->html_path), 404);

        $htmlUrl = $this->files->resolveUrl($issue->html_path);

        abort_unless(filled($htmlUrl), 404);

        return view('site.magazine-reader', [
            'seo' => $this->seo->fromMagazineIssue($issue),
            'issue' => $issue,
            'htmlUrl' => $htmlUrl,
        ]);
    }
}
