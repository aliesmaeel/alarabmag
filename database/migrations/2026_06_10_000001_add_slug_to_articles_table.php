<?php

use App\Models\Article;
use App\Support\Slug;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->string('slug', 255)->nullable()->unique()->after('title');
        });

        Article::query()->orderBy('id')->each(function (Article $article) {
            if (blank($article->slug)) {
                $article->slug = Slug::unique($article->title, Article::class, $article->id, 'news');
                $article->saveQuietly();
            }
        });

        Schema::table('articles', function (Blueprint $table) {
            $table->string('slug', 255)->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
