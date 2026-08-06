<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('identity_providers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug');
            $table->string('type'); // saml, oidc, oauth, ldap, active_directory, azure, google, github, apple
            $table->string('protocol'); // saml, oidc, oauth2
            $table->string('status')->default('active'); // active, inactive, error
            $table->text('client_id')->nullable();
            $table->text('client_secret')->nullable();
            $table->text('issuer_url')->nullable();
            $table->text('authorization_url')->nullable();
            $table->text('token_url')->nullable();
            $table->text('userinfo_url')->nullable();
            $table->text('jwks_url')->nullable();
            $table->text('logout_url')->nullable();
            $table->text('sso_url')->nullable();
            $table->text('certificate')->nullable();
            $table->text('private_key')->nullable();
            $table->string('default_role')->nullable();
            $table->json('scopes')->nullable();
            $table->json('claim_mapping')->nullable();
            $table->boolean('auto_provision')->default(false);
            $table->boolean('auto_link')->default(false);
            $table->boolean('force_ssl')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->json('metadata')->nullable();
            $table->uuid('tenant_id')->nullable();
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
        Schema::dropIfExists('identity_providers');
    }
};
