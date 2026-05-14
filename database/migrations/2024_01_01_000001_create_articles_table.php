<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('title', 1000);
            $table->string('subtitle', 500)->nullable();
            $table->text('excerpt')->nullable();
            $table->longText('body')->nullable();
            $table->string('category', 100)->default('عام');
            $table->string('author', 200)->default('فريق التحرير');
            $table->string('image_url', 1000)->nullable();
            $table->string('read_time', 50)->default('5 دقائق');
            $table->boolean('featured')->default(false);
            $table->enum('status', ['published', 'draft'])->default('published');
            $table->string('region', 100)->nullable();
            $table->unsignedInteger('views')->default(0);
            $table->timestamps();

            $table->index('category');
            $table->index('status');
            $table->index('featured');
            $table->index('region');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
