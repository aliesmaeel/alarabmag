<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('song_poll_votes', function (Blueprint $table) {
            $table->string('email')->nullable()->after('song_poll_entry_id');
            $table->unique(['song_poll_id', 'email']);
        });
    }

    public function down(): void
    {
        Schema::table('song_poll_votes', function (Blueprint $table) {
            $table->dropUnique(['song_poll_id', 'email']);
            $table->dropColumn('email');
        });
    }
};
