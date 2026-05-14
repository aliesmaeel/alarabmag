<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('people', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200);
            $table->string('name_en', 200)->nullable();
            $table->string('role', 200)->nullable();
            $table->enum('category', ['influencer', 'artist', 'doctor', 'business'])->default('business');
            $table->string('country', 100)->nullable();
            $table->string('flag', 10)->nullable();
            $table->string('image_url', 1000)->nullable();
            $table->text('excerpt')->nullable();
            $table->longText('bio')->nullable();

            // Shared stat fields
            $table->string('stat', 100)->nullable();
            $table->string('stat_label', 200)->nullable();

            // Influencer fields
            $table->string('handle', 100)->nullable();
            $table->string('platform', 100)->nullable();
            $table->string('followers', 50)->nullable();

            // Doctor fields
            $table->string('hospital', 300)->nullable();
            $table->string('specialty', 200)->nullable();
            $table->string('badge', 200)->nullable();

            // Business fields
            $table->string('company', 200)->nullable();
            $table->string('net_worth', 100)->nullable();

            $table->boolean('featured')->default(false);
            $table->timestamps();

            $table->index('category');
            $table->index('featured');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('people');
    }
};
