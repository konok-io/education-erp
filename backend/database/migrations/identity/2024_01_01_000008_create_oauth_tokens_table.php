<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('oauth_tokens', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('client_id');
            $table->uuid('user_id')->nullable();
            $table->string('name')->nullable();
            $table->string('type'); // access_token, refresh_token, id_token, bearer
            $table->string('status')->default('active'); // active, revoked, expired
            $table->text('token');
            $table->string('token_identifier')->nullable();
            $table->text('hashed_token')->nullable();
            $table->string('scope')->nullable();
            $table->json('scopes')->nullable();
            $table->text('code')->nullable();
            $table->text('code_challenge')->nullable();
            $table->string('code_challenge_method')->nullable();
            $table->string('state')->nullable();
            $table->string('nonce')->nullable();
            $table->text('redirect_uri')->nullable();
            $table->timestampTz('token_expires_at')->nullable();
            $table->timestampTz('refresh_expires_at')->nullable();
            $table->timestampTz('issued_at')->nullable();
            $table->timestampTz('revoked_at')->nullable();
            $table->text('revoke_reason')->nullable();
            $table->uuid('revoked_by')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->foreign('client_id')
                ->references('id')
                ->on('oauth_clients')
                ->onDelete('cascade');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->unique(['token_identifier']);
            $table->index(['user_id', 'status']);
            $table->index(['client_id', 'status']);
            $table->index(['token_expires_at']);
            $table->index(['hashed_token']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('oauth_tokens');
    }
};
