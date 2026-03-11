<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_analytics_data_sources', function (Blueprint $table) {
            $table->id();
            $table->string('connection_name')->nullable();
            $table->string('table_name')->unique();
            $table->boolean('is_enabled')->default(false);
            $table->timestamps();
        });

        Schema::create('ai_analytics_cards', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('table_name');
            $table->string('column_name')->nullable();
            $table->string('aggregation_type');
            $table->integer('order_column')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_analytics_cards');
        Schema::dropIfExists('ai_analytics_data_sources');
    }
};
