<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('observability_audit_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('event_type'); // alert_triggered, alert_resolved, incident_created, incident_resolved, health_check_failed, health_check_restored, slo_breached, chaos_test_executed
            $table->string('severity'); // critical, high, medium, low, info
            $table->uuid('service_id')->nullable();
            $table->uuid('user_id')->nullable();
            $table->json('event_data')->nullable();
            $table->json('metadata')->nullable();
            $table->string('source'); // system, user, automated
            $table->string('environment')->default('production');
            $table->timestamp('occurred_at');
            $table->timestamps();

            $table->index(['event_type', 'occurred_at']);
            $table->index(['service_id', 'occurred_at']);
            $table->index(['user_id', 'occurred_at']);
            $table->index('occurred_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('observability_audit_logs');
    }
};
