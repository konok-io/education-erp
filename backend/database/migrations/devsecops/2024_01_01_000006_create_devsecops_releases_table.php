<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devsecops_releases', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('version');
            $table->string('type'); // major, minor, patch, rc, lts, hotfix
            $table->text('description')->nullable();
            $table->string('status'); // draft, rc, stable, lts, deprecated, archived
            $table->string('channel'); // stable, beta, alpha, edge
            $table->string('git_tag')->nullable();
            $table->string('git_commit')->nullable();
            $table->json('changelog')->nullable();
            $table->json('breaking_changes')->nullable();
            $table->json('known_issues')->nullable();
            $table->json('upgrade_guide')->nullable();
            $table->json('artifacts')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('released_at')->nullable();
            $table->timestamp('eol_at')->nullable();
            $table->boolean('is_prerelease')->default(false);
            $table->boolean('is_draft')->default(true);
            $table->uuid('created_by')->nullable();
            $table->uuid('released_by')->nullable();
            $table->uuid('environment_id')->nullable();
            $table->timestamps();

            $table->foreign('environment_id')->references('id')->on('devsecops_environments')->onDelete('set null');
            $table->index('version');
            $table->index('type');
            $table->index('status');
            $table->index('channel');
            $table->unique(['version']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devsecops_releases');
    }
};
