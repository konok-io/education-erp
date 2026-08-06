<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('failover_events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('type'); // automatic, manual, planned, emergency
            $table->string('status'); // initiated, in_progress, completed, failed, rolled_back
            $table->string('source_site')->nullable();
            $table->string('destination_site')->nullable();
            $table->string('trigger_reason')->nullable();
            $table->text('trigger_details')->nullable();
            $table->json('affected_services')->nullable();
            $table->unsignedInteger('affected_users')->default(0);
            $table->unsignedInteger('downtime_seconds')->default(0);
            $table->timestampTz('initiated_at')->nullable();
            $table->timestampTz('completed_at')->nullable();
            $table->unsignedInteger('recovery_time_seconds')->default(0);
            $table->text('error_message')->nullable();
            $table->json('rollback_config')->nullable();
            $table->boolean('is_rolled_back')->default(false);
            $table->json('metadata')->nullable();
            $table->uuid('tenant_id')->nullable();
            $table->uuid('initiated_by')->nullable();
            $table->uuid('approved_by')->nullable();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'status']);
            $table->index(['type', 'status']);
            $table->index(['initiated_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('failover_events');
    }
};
