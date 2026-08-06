<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_sessions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->string('name')->nullable();
            $table->string('device_type'); // web, mobile, desktop
            $table->string('device_name')->nullable();
            $table->string('device_os')->nullable();
            $table->string('device_browser')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('location')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->string('status')->default('active'); // active, inactive, revoked
            $table->string('token')->nullable();
            $table->text('refresh_token')->nullable();
            $table->timestampTz('token_expires_at')->nullable();
            $table->timestampTz('refresh_expires_at')->nullable();
            $table->timestampTz('last_activity_at')->nullable();
            $table->timestampTz('login_at')->nullable();
            $table->timestampTz('logout_at')->nullable();
            $table->boolean('is_current')->default(false);
            $table->json('metadata')->nullable();
            $table->string('environment')->default('production');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->index(['user_id', 'status']);
            $table->index(['token']);
            $table->index(['refresh_token']);
            $table->index(['last_activity_at']);
            $table->index(['ip_address']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_sessions');
    }
};
