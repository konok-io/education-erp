<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('storage_providers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('type'); // local, nas, san, s3, azure, gcs, minio, ftp, sftp
            $table->string('status'); // active, inactive, error, maintenance
            $table->json('config')->nullable();
            $table->string('region')->nullable();
            $table->string('zone')->nullable();
            $table->string('endpoint')->nullable();
            $table->string('bucket')->nullable();
            $table->string('path_prefix')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->boolean('is_replicated')->default(false);
            $table->unsignedBigInteger('total_capacity_bytes')->nullable();
            $table->unsignedBigInteger('used_capacity_bytes')->default(0);
            $table->unsignedBigInteger('available_capacity_bytes')->nullable();
            $table->unsignedInteger('file_count')->default(0);
            $table->unsignedInteger('latency_ms')->default(0);
            $table->unsignedInteger('throughput_mbps')->default(0);
            $table->json('encryption_config')->nullable();
            $table->boolean('is_encrypted')->default(false);
            $table->string('encryption_algorithm')->nullable();
            $table->boolean('is_worm')->default(false); // Write Once Read Many
            $table->integer('retention_days')->nullable();
            $table->json('metadata')->nullable();
            $table->uuid('tenant_id')->nullable();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'status']);
            $table->index(['type', 'status']);
            $table->index(['region']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('storage_providers');
    }
};
