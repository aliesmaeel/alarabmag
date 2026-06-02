<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->boolean('in_ticker')->default(false)->after('featured');
            $table->unsignedSmallInteger('ticker_order')->default(0)->after('in_ticker');

            $table->index(['in_ticker', 'ticker_order']);
        });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropIndex(['in_ticker', 'ticker_order']);
            $table->dropColumn(['in_ticker', 'ticker_order']);
        });
    }
};
