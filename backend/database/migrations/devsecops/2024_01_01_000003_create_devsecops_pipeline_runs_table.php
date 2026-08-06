<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devsecops_pipeline_runs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('pipeline_id');
            $table->string('run_number');
            $table->string('status'); // pending, running, success, failed, cancelled, blocked
            $table->string('trigger'); // push, pull_request, manual, scheduled, api
            $table->string('branch')->nullable();
            $table->string('commit_sha')->nullable();
            $table->string('commit_message')->nullable();
            $table->string('author')->nullable();
            $table->json('stages')->nullable();
            $table->json('jobs')->nullable();
            $table->text('logs')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->integer('duration')->nullable();
            $table->json('artifacts')->nullable();
            $table->json('metadata')->nullable();
            $table->uuid('triggered_by')->nullable();
            $table->uuid('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->foreign('pipeline_id')->references('id')->on('devsecops_pipelines')->onDelete('cascade');
            $table->index('status');
            $table->index('trigger');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devsecops_pipeline_runs');
    }
};
