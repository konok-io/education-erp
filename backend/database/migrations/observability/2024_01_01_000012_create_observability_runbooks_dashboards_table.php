<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('observability_runbooks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('service_id')->nullable();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->text('content');
            $table->json('steps')->nullable();
            $table->json('playbook_steps')->nullable();
            $table->json('related_alerts')->nullable();
            $table->json('tags')->nullable();
            $table->string('severity')->nullable(); // critical, high, medium, low
            $table->boolean('is_active')->default(true);
            $table->uuid('created_by_user_id')->nullable();
            $table->uuid('updated_by_user_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('service_id')->references('id')->on('observability_services')->onDelete('set null');
            $table->index(['service_id', 'is_active']);
            $table->index('slug');
        });

        Schema::create('observability_dashboards', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('service_id')->nullable();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('type'); // overview, service, custom
            $table->json('layout')->nullable();
            $table->json('widgets')->nullable();
            $table->json('filters')->nullable();
            $table->string('environment')->default('production');
            $table->json('tags')->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_shared')->default(false);
            $table->uuid('created_by_user_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('service_id')->references('id')->on('observability_services')->onDelete('set null');
            $table->index(['service_id', 'type']);
            $table->index('slug');
        });

        Schema::create('observability_chaos_experiments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('service_id')->nullable();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('type'); // server_failure, database_failure, network_failure, cache_failure, queue_failure
            $table->string('status')->default('draft'); // draft, scheduled, running, completed, stopped, failed
            $table->json('target_config')->nullable();
            $table->json('experiment_config')->nullable();
            $table->integer('duration_seconds')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->uuid('experimenter_user_id')->nullable();
            $table->json('result')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('service_id')->references('id')->on('observability_services')->onDelete('set null');
            $table->index(['type', 'status']);
            $table->index(['service_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('observability_chaos_experiments');
        Schema::dropIfExists('observability_dashboards');
        Schema::dropIfExists('observability_runbooks');
    }
};
