<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Setting::setMany([
            'seo_interviews_title' => 'المقابلات — حوارات تُلهم',
            'seo_interviews_description' => 'مقابلات فيديو حصرية مع شخصيات عربية مؤثرة: رواد أعمال، فنانين، مؤثرين، وأطباء.',
            'seo_interviews_keywords' => 'مقابلات, حوارات, فيديو, مجلة العرب, Al Arab Magazine',
        ]);
    }

    public function down(): void
    {
        //
    }
};
