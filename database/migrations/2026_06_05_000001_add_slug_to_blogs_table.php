<?php

use App\Models\Blog;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            $table->string('slug', 255)->nullable()->unique()->after('title');
        });

        Blog::query()->orderBy('id')->each(function (Blog $blog) {
            if (blank($blog->slug)) {
                $blog->slug = Blog::uniqueSlug($blog->title, $blog->id);
                $blog->saveQuietly();
            }
        });

        Schema::table('blogs', function (Blueprint $table) {
            $table->string('slug', 255)->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
