<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_analytics_entity_mappings', function (Blueprint $table) {
            $table->id();
            $table->string('entity_name')->unique();
            $table->string('source_type')->default('table'); // table or model
            $table->string('model_class')->nullable();
            $table->string('table_name')->nullable();
            $table->json('mapping')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_analytics_entity_mappings');
    }
};
