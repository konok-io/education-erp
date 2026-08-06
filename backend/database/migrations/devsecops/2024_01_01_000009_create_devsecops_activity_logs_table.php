<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devsecops_activity_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type'); // pipeline_started, pipeline_completed, deployment, rollback, security_scan, release
            $table->string('action');
            $table->string('status'); // success, failed, warning
            $table->uuid('resource_id')->nullable();
            $table->string('resource_type')->nullable();
            $table->string('resource_name')->nullable();
            $table->json('details')->nullable();
            $table->json('metadata')->nullable();
            $table->text('message')->nullable();
            $table->json('changes')->nullable();
            $table->uuid('actor_id')->nullable();
            $table->string('actor_type')->nullable();
            $table->string('actor_name')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            $table->index('type');
            $table->index('action');
            $table->index('status');
            $table->index('resource_type');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devsecops_activity_logs');
    }
};
