<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devsecops_artifacts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('pipeline_run_id')->nullable();
            $table->string('name');
            $table->string('version')->nullable();
            $table->string('type'); // docker, npm, composer, android_apk, android_aab, electron, archive
            $table->string('path')->nullable();
            $table->string('registry'); // dockerhub, ghcr, nexus, artifactory, s3
            $table->string('repository')->nullable();
            $table->string('digest')->nullable();
            $table->string('size')->nullable();
            $table->json('metadata')->nullable();
            $table->json('labels')->nullable();
            $table->json('sbom')->nullable();
            $table->json('provenance')->nullable();
            $table->boolean('signed')->default(false);
            $table->string('signature')->nullable();
            $table->string('scan_status')->default('pending');
            $table->json('scan_results')->nullable();
            $table->integer('vulnerability_count')->default(0);
            $table->integer('critical_vulnerabilities')->default(0);
            $table->string('license')->nullable();
            $table->json('dependencies')->nullable();
            $table->uuid('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('pipeline_run_id')->references('id')->on('devsecops_pipeline_runs')->onDelete('set null');
            $table->index('type');
            $table->index('registry');
            $table->index('scan_status');
            $table->unique(['name', 'version', 'registry']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devsecops_artifacts');
    }
};
