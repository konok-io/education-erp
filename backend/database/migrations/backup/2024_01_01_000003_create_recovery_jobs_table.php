<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recovery_jobs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('backup_snapshot_id')->nullable();
            $table->string('name');
            $table->string('type'); // full, partial, file, database, point_in_time
            $table->string('status'); // pending, running, completed, failed, cancelled
            $table->string('destination_type'); // original, new, custom
            $table->json('destination_config')->nullable();
            $table->timestampTz('point_in_time')->nullable();
            $table->json('restore_options')->nullable();
            $table->string('target_database')->nullable();
            $table->string('target_path')->nullable();
            $table->unsignedBigInteger('size_restored')->default(0);
            $table->unsignedInteger('files_restored')->default(0);
            $table->unsignedInteger('records_restored')->default(0);
            $table->timestampTz('started_at')->nullable();
            $table->timestampTz('completed_at')->nullable();
            $table->unsignedInteger('duration_seconds')->default(0);
            $table->text('error_message')->nullable();
            $table->json('logs')->nullable();
            $table->json('metadata')->nullable();
            $table->string('environment')->default('production');
            $table->uuid('tenant_id')->nullable();
            $table->uuid('initiated_by')->nullable();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('backup_snapshot_id')
                ->references('id')
                ->on('backup_snapshots')
                ->onDelete('set null');

            $table->index(['tenant_id', 'status']);
            $table->index(['environment', 'status']);
            $table->index(['type', 'status']);
            $table->index(['created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recovery_jobs');
    }
};
