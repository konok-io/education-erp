<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('passkeys', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->string('name');
            $table->string('type'); // platform, cross-platform
            $table->string('status')->default('active'); // active, inactive, revoked
            $table->text('credential_id');
            $table->text('public_key');
            $table->string('aaguid')->nullable();
            $table->string('device_type')->nullable(); // mobile, desktop
            $table->string('device_os')->nullable();
            $table->string('device_name')->nullable();
            $table->string('browser_name')->nullable();
            $table->string('browser_version')->nullable();
            $table->unsignedBigInteger('sign_count')->default(0);
            $table->boolean('backup_eligible')->default(false);
            $table->boolean('backup_state')->default(false);
            $table->boolean('resident_key')->default(false);
            $table->string('transports')->nullable(); // usb, nfc, ble
            $table->string('rp_id')->nullable();
            $table->timestampTz('created_at');
            $table->timestampTz('last_used_at')->nullable();
            $table->timestampTz('revoked_at')->nullable();
            $table->text('revoke_reason')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->unique(['credential_id']);
            $table->index(['user_id', 'status']);
            $table->index(['aaguid']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('passkeys');
    }
};
