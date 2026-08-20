<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('song_polls', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('title_en')->nullable();
            $table->string('eyebrow')->nullable();
            $table->text('subtitle')->nullable();
            $table->unsignedSmallInteger('year')->nullable();
            $table->enum('status', ['draft', 'published'])->default('published');
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();

            $table->index('status');
        });

        Schema::create('song_poll_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('song_poll_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('artist');
            $table->string('country')->nullable();
            $table->string('flag', 16)->nullable();
            $table->string('image_url', 1000)->nullable();
            $table->string('listen_url', 1000)->nullable();
            $table->string('excerpt', 500)->nullable();
            $table->unsignedInteger('votes_count')->default(0);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['song_poll_id', 'votes_count']);
        });

        Schema::create('song_poll_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('song_poll_id')->constrained()->cascadeOnDelete();
            $table->foreignId('song_poll_entry_id')->constrained()->cascadeOnDelete();
            $table->string('voter_hash', 64);
            $table->string('ip_hash', 64)->nullable();
            $table->timestamps();

            $table->unique(['song_poll_id', 'voter_hash']);
            $table->index('song_poll_entry_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('song_poll_votes');
        Schema::dropIfExists('song_poll_entries');
        Schema::dropIfExists('song_polls');
    }
};
