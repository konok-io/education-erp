<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('backup_jobs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('type'); // full, incremental, differential, snapshot
            $table->string('status'); // pending, running, completed, failed, cancelled
            $table->string('source_type'); // database, files, media, all
            $table->json('source_config')->nullable();
            $table->string('destination_type'); // local, nas, s3, azure, gcs, minio, ftp
            $table->json('destination_config')->nullable();
            $table->string('encryption')->default('aes-256'); // none, aes-256, rsa-4096
            $table->string('encryption_key_id')->nullable();
            $table->unsignedBigInteger('size_bytes')->default(0);
            $table->unsignedInteger('file_count')->default(0);
            $table->string('checksum')->nullable();
            $table->text('compression_algorithm')->default('gzip'); // none, gzip, bzip2, xz
            $table->unsignedInteger('compression_level')->default(6);
            $table->string('retention_policy')->nullable();
            $table->timestampTz('scheduled_at')->nullable();
            $table->timestampTz('started_at')->nullable();
            $table->timestampTz('completed_at')->nullable();
            $table->text('error_message')->nullable();
            $table->json('metadata')->nullable();
            $table->string('environment')->default('production');
            $table->string('region')->nullable();
            $table->boolean('is_immutable')->default(false);
            $table->boolean('verified')->default(false);
            $table->timestampTz('verified_at')->nullable();
            $table->uuid('tenant_id')->nullable();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'status']);
            $table->index(['environment', 'status']);
            $table->index(['type', 'status']);
            $table->index(['scheduled_at']);
            $table->index(['created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('backup_jobs');
    }
};
