<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('creator_applications', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->index();
            $table->string('country');
            $table->string('dial_code', 8)->nullable();
            $table->string('phone', 32);
            $table->string('niche');
            $table->string('followers', 64)->nullable();
            $table->json('social_links');
            $table->text('bio');
            $table->string('status', 20)->default('new')->index();
            $table->text('admin_notes')->nullable();
            $table->string('ip_hash', 64)->nullable();
            $table->timestamps();

            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('creator_applications');
    }
};
