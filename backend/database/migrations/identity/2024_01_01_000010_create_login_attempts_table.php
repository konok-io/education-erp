<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('login_attempts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type'); // login, mfa_verification, password_reset, registration
            $table->string('status'); // success, failed, blocked, rate_limited
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('username')->nullable();
            $table->uuid('user_id')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('device_type')->nullable();
            $table->string('location')->nullable();
            $table->string('failure_reason')->nullable();
            $table->boolean('is_suspicious')->default(false);
            $table->float('risk_score')->default(0);
            $table->json('metadata')->nullable();
            $table->timestampTz('attempted_at');
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            $table->index(['email', 'attempted_at']);
            $table->index(['ip_address', 'attempted_at']);
            $table->index(['user_id', 'attempted_at']);
            $table->index(['status', 'attempted_at']);
            $table->index(['is_suspicious']);
            $table->index(['attempted_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('login_attempts');
    }
};
