<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('observability_synthetic_tests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('service_id')->nullable();
            $table->string('name');
            $table->string('type'); // login, admission, fee_payment, attendance, api, search
            $table->string('endpoint')->nullable();
            $table->string('method')->default('GET');
            $table->string('schedule'); // every_minute, every_5_minutes, hourly, daily
            $table->string('status')->default('active'); // active, paused, disabled
            $table->json('request_config')->nullable();
            $table->json('assertions')->nullable();
            $table->string('environment')->default('production');
            $table->json('headers')->nullable();
            $table->json('cookies')->nullable();
            $table->boolean('follow_redirects')->default(true);
            $table->integer('timeout_seconds')->default(30);
            $table->timestamp('last_run_at')->nullable();
            $table->string('last_run_status')->nullable(); // success, failed, timeout
            $table->decimal('last_run_duration_ms', 20, 6)->nullable();
            $table->json('last_run_result')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('service_id')->references('id')->on('observability_services')->onDelete('cascade');
            $table->index(['type', 'status']);
            $table->index(['service_id', 'is_active']);
        });

        Schema::create('observability_synthetic_test_results', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('test_id');
            $table->string('status'); // success, failed, timeout, error
            $table->decimal('duration_ms', 20, 6)->nullable();
            $table->integer('http_status_code')->nullable();
            $table->text('error_message')->nullable();
            $table->json('response_body')->nullable();
            $table->json('assertion_results')->nullable();
            $table->string('environment')->default('production');
            $table->timestamp('executed_at');
            $table->timestamps();

            $table->foreign('test_id')->references('id')->on('observability_synthetic_tests')->onDelete('cascade');
            $table->index(['test_id', 'executed_at']);
            $table->index('executed_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('observability_synthetic_test_results');
        Schema::dropIfExists('observability_synthetic_tests');
    }
};
