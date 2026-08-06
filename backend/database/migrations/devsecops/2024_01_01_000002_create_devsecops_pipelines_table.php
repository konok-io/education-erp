<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devsecops_pipelines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('type'); // ci, cd, security, release
            $table->string('provider'); // github, gitlab, jenkins, azure
            $table->string('repository')->nullable();
            $table->string('branch')->default('main');
            $table->string('yaml_path')->default('.github/workflows/pipeline.yml');
            $table->json('stages')->nullable();
            $table->json('config')->nullable();
            $table->string('status')->default('inactive');
            $table->integer('timeout')->default(3600);
            $table->boolean('auto_trigger')->default(true);
            $table->boolean('require_approval')->default(false);
            $table->json('approval_roles')->nullable();
            $table->integer('min_coverage')->default(80);
            $table->boolean('is_active')->default(true);
            $table->uuid('environment_id')->nullable();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('environment_id')->references('id')->on('devsecops_environments')->onDelete('set null');
            $table->index('type');
            $table->index('status');
            $table->index('provider');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devsecops_pipelines');
    }
};
