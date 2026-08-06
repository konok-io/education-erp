<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devsecops_security_scans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('pipeline_run_id')->nullable();
            $table->uuid('artifact_id')->nullable();
            $table->string('type'); // sast, dast, sca, secret, container, iac, sbom
            $table->string('tool'); // sonarqube, snyk, trivy, gitleaks, owasp
            $table->string('status'); // pending, running, completed, failed
            $table->string('severity'); // none, low, medium, high, critical
            $table->json('results')->nullable();
            $table->json('vulnerabilities')->nullable();
            $table->json('secrets_found')->nullable();
            $table->json('compliance')->nullable();
            $table->integer('vulnerability_count')->default(0);
            $table->integer('critical_count')->default(0);
            $table->integer('high_count')->default(0);
            $table->integer('medium_count')->default(0);
            $table->integer('low_count')->default(0);
            $table->integer('info_count')->default(0);
            $table->text('report_path')->nullable();
            $table->text('summary')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->integer('duration')->nullable();
            $table->json('metadata')->nullable();
            $table->uuid('scan_by')->nullable();
            $table->timestamps();

            $table->foreign('pipeline_run_id')->references('id')->on('devsecops_pipeline_runs')->onDelete('set null');
            $table->foreign('artifact_id')->references('id')->on('devsecops_artifacts')->onDelete('set null');
            $table->index('type');
            $table->index('tool');
            $table->index('status');
            $table->index('severity');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devsecops_security_scans');
    }
};
