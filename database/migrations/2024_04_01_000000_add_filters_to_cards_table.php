<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ai_analytics_cards', function (Blueprint $table) {
            $table->json('filters')->nullable()->after('aggregation_type');
        });
    }

    public function down(): void
    {
        Schema::table('ai_analytics_cards', function (Blueprint $table) {
            $table->dropColumn('filters');
        });
    }
};
