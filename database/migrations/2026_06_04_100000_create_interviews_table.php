<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('interviews', function (Blueprint $table) {
            $table->id();
            $table->string('title', 1000);
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('video_url', 1000);
            $table->string('category', 100)->default('عام');
            $table->string('thumbnail_url', 1000)->nullable();
            $table->boolean('featured')->default(false);
            $table->enum('status', ['published', 'draft'])->default('published');
            $table->unsignedInteger('views')->default(0);
            $table->string('meta_title', 500)->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords', 500)->nullable();
            $table->string('og_title', 500)->nullable();
            $table->text('og_description')->nullable();
            $table->string('og_image', 1000)->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('featured');
            $table->index('category');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('interviews');
    }
};
