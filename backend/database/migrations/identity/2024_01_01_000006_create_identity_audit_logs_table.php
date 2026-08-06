<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('identity_audit_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('event_type'); // login, logout, mfa_enabled, mfa_disabled, password_changed, role_updated, session_revoked, etc.
            $table->string('severity'); // info, warning, error, critical
            $table->string('category'); // authentication, authorization, identity, device, mfa
            $table->uuid('user_id')->nullable();
            $table->string('user_email')->nullable();
            $table->uuid('session_id')->nullable();
            $table->uuid('device_id')->nullable();
            $table->string('identity_provider_id')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('location')->nullable();
            $table->text('description');
            $table->json('event_data')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->boolean('success')->default(true);
            $table->text('failure_reason')->nullable();
            $table->string('environment')->default('production');
            $table->string('region')->nullable();
            $table->timestampTz('occurred_at');
            $table->timestamps();

            $table->index(['event_type', 'occurred_at']);
            $table->index(['category', 'severity']);
            $table->index(['user_id', 'occurred_at']);
            $table->index(['session_id']);
            $table->index(['environment', 'occurred_at']);
            $table->index(['ip_address', 'occurred_at']);
            $table->index(['occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('identity_audit_logs');
    }
};
