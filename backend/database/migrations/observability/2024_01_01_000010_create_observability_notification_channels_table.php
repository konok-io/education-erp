<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('observability_notification_channels', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('type'); // email, sms, push, slack, teams, discord, webhook, pagerduty
            $table->string('status')->default('active'); // active, inactive, error
            $table->json('config'); // channel-specific configuration
            $table->json('settings')->nullable();
            $table->json('allowed_severities')->nullable(); // which severity levels can use this channel
            $table->json('allowed_services')->nullable(); // which services can use this channel
            $table->boolean('is_default')->default(false);
            $table->string('environment')->default('production');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['type', 'is_active']);
            $table->index('status');
        });

        Schema::create('observability_notification_templates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('channel_id');
            $table->string('name');
            $table->string('event_type'); // alert_triggered, incident_created, incident_resolved, health_check_failed
            $table->text('subject_template')->nullable();
            $table->text('body_template');
            $table->json('variables')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('channel_id')->references('id')->on('observability_notification_channels')->onDelete('cascade');
            $table->index(['channel_id', 'event_type']);
        });

        Schema::create('observability_notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('channel_id');
            $table->uuid('alert_id')->nullable();
            $table->uuid('incident_id')->nullable();
            $table->string('recipient');
            $table->string('status'); // pending, sent, delivered, failed
            $table->text('subject')->nullable();
            $table->text('body');
            $table->json('metadata')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->foreign('channel_id')->references('id')->on('observability_notification_channels')->onDelete('cascade');
            $table->foreign('alert_id')->references('id')->on('observability_alerts')->onDelete('set null');
            $table->foreign('incident_id')->references('id')->on('observability_incidents')->onDelete('set null');
            $table->index(['channel_id', 'status']);
            $table->index(['alert_id', 'status']);
            $table->index(['incident_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('observability_notifications');
        Schema::dropIfExists('observability_notification_templates');
        Schema::dropIfExists('observability_notification_channels');
    }
};
