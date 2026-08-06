<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dr_sites', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug');
            $table->string('type'); // primary, secondary, dr, hot, warm, cold
            $table->string('status'); // active, standby, failed, maintenance
            $table->string('region');
            $table->string('zone')->nullable();
            $table->json('infrastructure_config')->nullable();
            $table->string('endpoint')->nullable();
            $table->json('connection_config')->nullable();
            $table->string('role'); // active, passive, arbitrator
            $table->boolean('is_primary')->default(false);
            $table->boolean('auto_failover_enabled')->default(false);
            $table->string('health_check_endpoint')->nullable();
            $table->unsignedInteger('health_check_interval_seconds')->default(30);
            $table->string('health_status')->default('unknown');
            $table->timestampTz('last_health_check')->nullable();
            $table->unsignedInteger('recovery_time_target_seconds')->default(3600);
            $table->unsignedInteger('recovery_point_target_seconds')->default(300);
            $table->json('metadata')->nullable();
            $table->uuid('tenant_id')->nullable();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'status']);
            $table->index(['type', 'status']);
            $table->index(['region']);
            $table->unique(['tenant_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dr_sites');
    }
};
