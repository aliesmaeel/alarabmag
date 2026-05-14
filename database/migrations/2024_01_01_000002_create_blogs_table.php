<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->string('title', 1000);
            $table->text('excerpt')->nullable();
            $table->longText('body')->nullable();
            $table->string('author', 200)->default('فريق التحرير');
            $table->string('author_bio', 500)->nullable();
            $table->string('author_img', 1000)->nullable();
            $table->string('image_url', 1000)->nullable();
            $table->string('tags', 500)->nullable();
            $table->boolean('featured')->default(false);
            $table->enum('status', ['published', 'draft'])->default('published');
            $table->unsignedInteger('views')->default(0);
            $table->timestamps();

            $table->index('status');
            $table->index('featured');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blogs');
    }
};
