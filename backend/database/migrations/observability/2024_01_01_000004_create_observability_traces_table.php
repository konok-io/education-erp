<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('observability_traces', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('service_id')->nullable();
            $table->string('trace_id')->unique();
            $table->string('name');
            $table->string('operation');
            $table->string('status'); // ok, error
            $table->decimal('duration_ms', 20, 6);
            $table->string('environment')->default('production');
            $table->timestamp('started_at');
            $table->timestamp('ended_at');
            $table->json('tags')->nullable();
            $table->timestamps();

            $table->foreign('service_id')->references('id')->on('observability_services')->onDelete('set null');
            $table->index(['service_id', 'started_at']);
            $table->index(['trace_id', 'started_at']);
            $table->index('trace_id');
            $table->index('started_at');
        });

        Schema::create('observability_spans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('trace_id');
            $table->uuid('parent_id')->nullable();
            $table->string('span_id');
            $table->string('name');
            $table->string('type'); // http, database, cache, queue, custom
            $table->string('status'); // ok, error
            $table->decimal('duration_ms', 20, 6);
            $table->json('attributes')->nullable();
            $table->json('events')->nullable();
            $table->json('links')->nullable();
            $table->timestamp('started_at');
            $table->timestamp('ended_at');
            $table->timestamps();

            $table->foreign('trace_id')->references('trace_id')->on('observability_traces')->onDelete('cascade');
            $table->index(['trace_id', 'span_id']);
            $table->index('span_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('observability_spans');
        Schema::dropIfExists('observability_traces');
    }
};
