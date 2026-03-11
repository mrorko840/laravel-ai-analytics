<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ai_analytics_events', function (Blueprint $table) {
            $table->id();
            $table->string('event_type');
            $table->string('event_key')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('session_id')->nullable();
            $table->string('entity_type')->nullable();
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('occurred_at')->useCurrent();
            $table->timestamps();

            $table->index('event_type');
            $table->index('occurred_at');
            $table->index('user_id');
        });

        Schema::create('ai_analytics_chats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('title')->nullable();
            $table->json('context')->nullable();
            $table->timestamps();
        });

        Schema::create('ai_analytics_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_id')->constrained('ai_analytics_chats')->cascadeOnDelete();
            $table->string('role');
            $table->longText('content');
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        Schema::create('ai_analytics_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('title');
            $table->string('report_type')->nullable();
            $table->json('filters')->nullable();
            $table->longText('payload');
            $table->timestamp('exported_at')->nullable();
            $table->timestamps();
        });

        Schema::create('ai_analytics_metric_cache', function (Blueprint $table) {
            $table->id();
            $table->string('metric_name');
            $table->string('cache_key')->unique();
            $table->string('filters_hash');
            $table->longText('payload');
            $table->timestamp('expires_at');
            $table->timestamps();

            $table->index(['metric_name', 'filters_hash']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_analytics_metric_cache');
        Schema::dropIfExists('ai_analytics_reports');
        Schema::dropIfExists('ai_analytics_messages');
        Schema::dropIfExists('ai_analytics_chats');
        Schema::dropIfExists('ai_analytics_events');
    }
};
