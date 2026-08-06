<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mfa_factors', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->string('name');
            $table->string('type'); // totp, sms, email, push, security_key, biometric
            $table->string('factor_type'); // authenticator, backup, primary
            $table->string('status')->default('active'); // active, inactive, compromised
            $table->string('phone_number')->nullable();
            $table->string('email')->nullable();
            $table->text('secret')->nullable();
            $table->text('public_key')->nullable();
            $table->text('credential_id')->nullable();
            $table->string('authenticator_type')->nullable(); // google, microsoft, authenticator
            $table->string('device_name')->nullable();
            $table->string('device_type')->nullable();
            $table->string('aaguid')->nullable();
            $table->unsignedInteger('sign_count')->default(0);
            $table->boolean('backup')->default(false);
            $table->boolean('verified')->default(false);
            $table->boolean('default')->default(false);
            $table->timestampTz('verified_at')->nullable();
            $table->timestampTz('last_used_at')->nullable();
            $table->timestampTz('expires_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'type']);
            $table->index(['credential_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mfa_factors');
    }
};
