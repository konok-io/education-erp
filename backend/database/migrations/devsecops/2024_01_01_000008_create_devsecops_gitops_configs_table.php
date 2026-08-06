<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devsecops_gitops_configs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('provider'); // argocd, fluxcd
            $table->string('repository');
            $table->string('path');
            $table->string('target_branch')->default('main');
            $table->uuid('environment_id');
            $table->string('sync_policy'); // automated, manual
            $table->boolean('auto_sync')->default(true);
            $table->boolean('self_heal')->default(true);
            $table->boolean('prune')->default(true);
            $table->integer('sync_interval')->default(300);
            $table->json('kustomize')->nullable();
            $table->json('helm')->nullable();
            $table->json('values')->nullable();
            $table->string('health_check_path')->nullable();
            $table->string('last_sync_status')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->json('drift_detection')->nullable();
            $table->json('notifications')->nullable();
            $table->boolean('is_active')->default(true);
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('environment_id')->references('id')->on('devsecops_environments')->onDelete('cascade');
            $table->index('provider');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devsecops_gitops_configs');
    }
};
