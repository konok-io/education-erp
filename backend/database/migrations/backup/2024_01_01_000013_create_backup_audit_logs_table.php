<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('backup_audit_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('event_type'); // backup_started, backup_completed, restore_initiated, etc.
            $table->string('severity'); // info, warning, error, critical
            $table->string('category'); // backup, recovery, replication, failover, archive
            $table->uuid('reference_id')->nullable(); // ID of related entity
            $table->string('reference_type')->nullable(); // Type of related entity
            $table->text('message');
            $table->json('event_data')->nullable();
            $table->json('metadata')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('environment')->default('production');
            $table->string('region')->nullable();
            $table->uuid('user_id')->nullable();
            $table->string('user_name')->nullable();
            $table->timestampTz('occurred_at');
            $table->timestamps();

            $table->index(['event_type', 'occurred_at']);
            $table->index(['category', 'severity']);
            $table->index(['reference_type', 'reference_id']);
            $table->index(['environment', 'occurred_at']);
            $table->index(['user_id', 'occurred_at']);
            $table->index(['occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('backup_audit_logs');
    }
};
