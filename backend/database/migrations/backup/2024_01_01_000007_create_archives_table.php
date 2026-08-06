<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('archives', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('type'); // cold, warm, hot
            $table->string('status'); // pending, active, archived, deleted
            $table->string('source_type'); // database, files, logs
            $table->json('source_config')->nullable();
            $table->string('archive_location');
            $table->string('storage_provider_type');
            $table->uuid('storage_provider_id')->nullable();
            $table->unsignedBigInteger('size_bytes')->default(0);
            $table->unsignedInteger('record_count')->default(0);
            $table->string('format'); // zip, tar, tar.gz, custom
            $table->boolean('is_encrypted')->default(false);
            $table->string('encryption_algorithm')->nullable();
            $table->timestampTz('archived_at')->nullable();
            $table->timestampTz('expires_at')->nullable();
            $table->boolean('is_legal_hold')->default(false);
            $table->string('legal_hold_reason')->nullable();
            $table->json('retention_policy')->nullable();
            $table->json('metadata')->nullable();
            $table->string('environment')->default('production');
            $table->uuid('tenant_id')->nullable();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('storage_provider_id')
                ->references('id')
                ->on('storage_providers')
                ->onDelete('set null');

            $table->index(['tenant_id', 'status']);
            $table->index(['type', 'status']);
            $table->index(['archived_at']);
            $table->index(['expires_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('archives');
    }
};
