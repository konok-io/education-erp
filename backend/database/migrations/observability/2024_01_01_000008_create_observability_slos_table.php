<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('observability_slos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('service_id')->nullable();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('type'); // availability, latency, reliability, throughput
            $table->string('indicator_type'); // metric, histogram, success_rate
            $table->string('indicator_name');
            $table->decimal('target_percentage', 10, 4)->default(99.9);
            $table->decimal('current_percentage', 10, 4)->nullable();
            $table->string('time_window'); // 1h, 24h, 7d, 30d, 90d
            $table->string('environment')->default('production');
            $table->json('threshold_config')->nullable();
            $table->json('metadata')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('service_id')->references('id')->on('observability_services')->onDelete('cascade');
            $table->index(['service_id', 'is_active']);
            $table->index(['type', 'environment']);
            $table->index('name');
        });

        Schema::create('observability_error_budgets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('slo_id');
            $table->string('period'); // monthly, quarterly, yearly
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('total_budget', 20, 6);
            $table->decimal('spent_budget', 20, 6)->default(0);
            $table->decimal('remaining_budget', 20, 6)->nullable();
            $table->decimal('burn_rate', 10, 4)->nullable();
            $table->decimal('projected_budget_exhaustion', 10, 4)->nullable();
            $table->boolean('is_breached')->default(false);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->foreign('slo_id')->references('id')->on('observability_slos')->onDelete('cascade');
            $table->index(['slo_id', 'period']);
            $table->index(['start_date', 'end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('observability_error_budgets');
        Schema::dropIfExists('observability_slos');
    }
};
