<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('replication_jobs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('type'); // sync, async, continuous
            $table->string('status'); // active, paused, stopped, failed
            $table->string('source_type'); // database, storage, region
            $table->json('source_config')->nullable();
            $table->string('destination_type'); // database, storage, region
            $table->json('destination_config')->nullable();
            $table->string('source_region')->nullable();
            $table->string('destination_region')->nullable();
            $table->string('replication_mode'); // master_slave, master_master, cluster, read_replica
            $table->unsignedInteger('lag_seconds')->default(0);
            $table->unsignedBigInteger('data_transferred_bytes')->default(0);
            $table->unsignedInteger('last_sync_at')->nullable();
            $table->timestampTz('started_at')->nullable();
            $table->timestampTz('stopped_at')->nullable();
            $table->text('error_message')->nullable();
            $table->json('metadata')->nullable();
            $table->string('environment')->default('production');
            $table->uuid('tenant_id')->nullable();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'status']);
            $table->index(['source_region', 'destination_region']);
            $table->index(['type', 'status']);
            $table->index(['created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('replication_jobs');
    }
};
