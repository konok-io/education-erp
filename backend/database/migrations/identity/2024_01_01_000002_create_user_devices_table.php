<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_devices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->string('device_uuid')->nullable();
            $table->string('name');
            $table->string('type'); // laptop, desktop, mobile, tablet, other
            $table->string('manufacturer')->nullable();
            $table->string('model')->nullable();
            $table->string('os'); // windows, macos, linux, ios, android
            $table->string('os_version')->nullable();
            $table->string('browser')->nullable();
            $table->string('browser_version')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('status')->default('active'); // active, blocked, lost, retired
            $table->string('trust_level')->default('untrusted'); // trusted, untrusted, verified
            $table->text('public_key')->nullable();
            $table->text('certificate')->nullable();
            $table->float('risk_score')->default(0);
            $table->boolean('is_compliant')->default(true);
            $table->boolean('has_mfa')->default(false);
            $table->string('last_location')->nullable();
            $table->timestampTz('first_seen_at')->nullable();
            $table->timestampTz('last_seen_at')->nullable();
            $table->timestampTz('blocked_at')->nullable();
            $table->text('block_reason')->nullable();
            $table->json('security_attributes')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->index(['user_id', 'status']);
            $table->index(['device_uuid']);
            $table->index(['trust_level']);
            $table->index(['risk_score']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_devices');
    }
};
