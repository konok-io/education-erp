<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('observability_health_checks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('service_id')->nullable();
            $table->string('name');
            $table->string('type'); // api, database, queue, cache, storage, smtp, sms, payment
            $table->string('endpoint')->nullable();
            $table->string('method')->default('GET');
            $table->string('status')->default('healthy'); // healthy, degraded, unhealthy, unknown
            $table->integer('check_interval_seconds')->default(60);
            $table->integer('timeout_seconds')->default(30);
            $table->integer('retry_count')->default(3);
            $table->json('headers')->nullable();
            $table->json('expected_response')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('environment')->default('production');
            $table->json('metadata')->nullable();
            $table->timestamp('last_check_at')->nullable();
            $table->string('last_status')->nullable();
            $table->text('last_error')->nullable();
            $table->decimal('last_response_time_ms', 20, 6)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('service_id')->references('id')->on('observability_services')->onDelete('cascade');
            $table->index(['service_id', 'is_active']);
            $table->index(['type', 'environment']);
            $table->index('status');
        });

        Schema::create('observability_health_check_results', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('health_check_id');
            $table->string('status'); // healthy, degraded, unhealthy
            $table->decimal('response_time_ms', 20, 6)->nullable();
            $table->integer('http_status_code')->nullable();
            $table->text('error_message')->nullable();
            $table->json('response_body')->nullable();
            $table->timestamp('checked_at');
            $table->timestamps();

            $table->foreign('health_check_id')->references('id')->on('observability_health_checks')->onDelete('cascade');
            $table->index(['health_check_id', 'checked_at']);
            $table->index('checked_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('observability_health_check_results');
        Schema::dropIfExists('observability_health_checks');
    }
};
