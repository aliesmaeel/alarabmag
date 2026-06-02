<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->string('meta_title', 500)->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords', 500)->nullable();
            $table->string('og_title', 500)->nullable();
            $table->text('og_description')->nullable();
            $table->string('og_image', 1000)->nullable();
        });

        Schema::table('blogs', function (Blueprint $table) {
            $table->string('meta_title', 500)->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords', 500)->nullable();
            $table->string('og_title', 500)->nullable();
            $table->text('og_description')->nullable();
            $table->string('og_image', 1000)->nullable();
        });

        Schema::table('people', function (Blueprint $table) {
            $table->string('meta_title', 500)->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords', 500)->nullable();
            $table->string('og_title', 500)->nullable();
            $table->text('og_description')->nullable();
            $table->string('og_image', 1000)->nullable();
        });
    }

    public function down(): void
    {
        foreach (['articles', 'blogs', 'people'] as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropColumn([
                    'meta_title',
                    'meta_description',
                    'meta_keywords',
                    'og_title',
                    'og_description',
                    'og_image',
                ]);
            });
        }
    }
};
