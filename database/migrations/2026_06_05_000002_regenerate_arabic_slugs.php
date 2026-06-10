<?php

use App\Models\Blog;
use App\Models\Interview;
use App\Support\Slug;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Blog::query()->orderBy('id')->each(function (Blog $blog) {
            $blog->slug = Slug::unique($blog->title, Blog::class, $blog->id, 'blog');
            $blog->saveQuietly();
        });

        Interview::query()->orderBy('id')->each(function (Interview $interview) {
            $interview->slug = Slug::unique($interview->title, Interview::class, $interview->id, 'interview');
            $interview->saveQuietly();
        });
    }

    public function down(): void
    {
        // Slugs are content-derived; no safe rollback.
    }
};
