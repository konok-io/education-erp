<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('backup_snapshots', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('backup_job_id')->nullable();
            $table->string('name');
            $table->string('type'); // full, incremental, differential
            $table->string('status'); // pending, available, archived, deleted
            $table->unsignedBigInteger('size_bytes')->default(0);
            $table->string('checksum')->nullable();
            $table->string('location');
            $table->string('storage_provider');
            $table->string('region')->nullable();
            $table->timestampTz('created_at');
            $table->timestampTz('expires_at')->nullable();
            $table->timestampTz('archived_at')->nullable();
            $table->boolean('is_immutable')->default(false);
            $table->timestampTz('immutable_until')->nullable();
            $table->json('metadata')->nullable();
            $table->string('environment')->default('production');
            $table->uuid('tenant_id')->nullable();
            $table->uuid('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('backup_job_id')
                ->references('id')
                ->on('backup_jobs')
                ->onDelete('set null');

            $table->index(['tenant_id', 'status']);
            $table->index(['environment', 'status']);
            $table->index(['type', 'status']);
            $table->index(['expires_at']);
            $table->index(['created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('backup_snapshots');
    }
};
