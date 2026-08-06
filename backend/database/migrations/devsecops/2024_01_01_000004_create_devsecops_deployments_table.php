<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devsecops_deployments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('environment_id');
            $table->uuid('pipeline_run_id')->nullable();
            $table->string('release_id')->nullable();
            $table->string('version')->nullable();
            $table->string('strategy'); // rolling, blue_green, canary, ab, shadow
            $table->string('status'); // pending, deploying, deployed, failed, rolled_back
            $table->string('namespace')->nullable();
            $table->json('config')->nullable();
            $table->json('replicas')->nullable();
            $table->json('resources')->nullable();
            $table->json('health_checks')->nullable();
            $table->json('rollback_config')->nullable();
            $table->string('previous_version')->nullable();
            $table->string('commit_sha')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->integer('duration')->nullable();
            $table->json('metrics')->nullable();
            $table->text('error_message')->nullable();
            $table->boolean('auto_rollback')->default(true);
            $table->uuid('deployed_by')->nullable();
            $table->uuid('rollback_by')->nullable();
            $table->timestamps();

            $table->foreign('environment_id')->references('id')->on('devsecops_environments')->onDelete('cascade');
            $table->foreign('pipeline_run_id')->references('id')->on('devsecops_pipeline_runs')->onDelete('set null');
            $table->index('status');
            $table->index('strategy');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devsecops_deployments');
    }
};
