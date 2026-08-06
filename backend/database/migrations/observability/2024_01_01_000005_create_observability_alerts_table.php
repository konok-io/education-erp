<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('observability_alerts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('service_id')->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('severity'); // critical, high, medium, low, info
            $table->string('status')->default('active'); // active, acknowledged, resolved, silenced
            $table->string('metric_name')->nullable();
            $table->string('condition'); // gt, lt, eq, gte, lte
            $table->decimal('threshold', 20, 6)->nullable();
            $table->decimal('current_value', 20, 6)->nullable();
            $table->string('environment')->default('production');
            $table->json('metadata')->nullable();
            $table->json('labels')->nullable();
            $table->timestamp('triggered_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->uuid('triggered_by_user_id')->nullable();
            $table->uuid('acknowledged_by_user_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('service_id')->references('id')->on('observability_services')->onDelete('set null');
            $table->index(['status', 'severity']);
            $table->index(['service_id', 'status']);
            $table->index(['environment', 'status']);
            $table->index('triggered_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('observability_alerts');
    }
};
