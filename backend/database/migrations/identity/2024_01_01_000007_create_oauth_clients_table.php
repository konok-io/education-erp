<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('oauth_clients', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug');
            $table->string('type'); // confidential, public
            $table->string('status')->default('active'); // active, inactive, revoked
            $table->text('client_id')->nullable();
            $table->text('client_secret')->nullable();
            $table->string('grant_types')->nullable(); // authorization_code, client_credentials, password, refresh_token
            $table->text('redirect_uris')->nullable();
            $table->string('logo_url')->nullable();
            $table->text('description')->nullable();
            $table->string('website_url')->nullable();
            $table->string('privacy_policy_url')->nullable();
            $table->string('terms_of_service_url')->nullable();
            $table->json('scopes')->nullable();
            $table->string('default_scopes')->nullable();
            $table->boolean('pkce_required')->default(false);
            $table->boolean('refresh_token_rotation')->default(true);
            $table->unsignedInteger('access_token_ttl')->default(3600);
            $table->unsignedInteger('refresh_token_ttl')->default(1209600);
            $table->unsignedInteger('id_token_ttl')->default(3600);
            $table->uuid('user_id')->nullable();
            $table->uuid('tenant_id')->nullable();
            $table->string('environment')->default('production');
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'slug']);
            $table->index(['status']);
            $table->index(['type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('oauth_clients');
    }
};
