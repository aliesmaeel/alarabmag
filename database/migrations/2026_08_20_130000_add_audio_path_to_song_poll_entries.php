<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('song_poll_entries', function (Blueprint $table) {
            $table->string('audio_path', 1000)->nullable()->after('listen_url');
        });
    }

    public function down(): void
    {
        Schema::table('song_poll_entries', function (Blueprint $table) {
            $table->dropColumn('audio_path');
        });
    }
};
