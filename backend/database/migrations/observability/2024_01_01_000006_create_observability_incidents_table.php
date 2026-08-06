<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('observability_incidents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('incident_number')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('severity'); // critical, high, medium, low
            $table->string('status')->default('triggered'); // triggered, acknowledged, investigating, resolved, closed
            $table->uuid('service_id')->nullable();
            $table->uuid('alert_id')->nullable();
            $table->string('environment')->default('production');
            $table->json('affected_components')->nullable();
            $table->json('impact')->nullable();
            $table->timestamp('started_at');
            $table->timestamp('acknowledged_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->uuid('assigned_to_user_id')->nullable();
            $table->uuid('created_by_user_id')->nullable();
            $table->text('postmortem')->nullable();
            $table->json('timeline')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('service_id')->references('id')->on('observability_services')->onDelete('set null');
            $table->foreign('alert_id')->references('id')->on('observability_alerts')->onDelete('set null');
            $table->index(['status', 'severity']);
            $table->index(['service_id', 'status']);
            $table->index(['environment', 'status']);
            $table->index('incident_number');
            $table->index('started_at');
        });

        Schema::create('observability_incident_timeline_events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('incident_id');
            $table->string('event_type'); // status_change, assignment, note, action, escalation
            $table->string('title');
            $table->text('description')->nullable();
            $table->uuid('user_id')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('occurred_at');
            $table->timestamps();

            $table->foreign('incident_id')->references('id')->on('observability_incidents')->onDelete('cascade');
            $table->index(['incident_id', 'occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('observability_incident_timeline_events');
        Schema::dropIfExists('observability_incidents');
    }
};
