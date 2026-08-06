<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_keys', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug');
            $table->string('status')->default('active'); // active, inactive, revoked
            $table->string('type'); // user, service, system
            $table->text('api_key_hash');
            $table->string('api_key_prefix')->nullable();
            $table->string('secret_hash')->nullable();
            $table->uuid('user_id')->nullable();
            $table->uuid('oauth_client_id')->nullable();
            $table->json('scopes')->nullable();
            $table->json('permissions')->nullable();
            $table->string('rate_limit')->default('1000');
            $table->unsignedInteger('expires_at')->nullable();
            $table->timestampTz('last_used_at')->nullable();
            $table->string('last_ip_address')->nullable();
            $table->unsignedBigInteger('request_count')->default(0);
            $table->unsignedBigInteger('error_count')->default(0);
            $table->uuid('tenant_id')->nullable();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'status']);
            $table->index(['tenant_id', 'status']);
            $table->index(['status']);
            $table->index(['api_key_prefix']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_keys');
    }
};
