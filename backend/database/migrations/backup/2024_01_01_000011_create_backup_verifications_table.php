<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('backup_verifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('backup_snapshot_id');
            $table->string('type'); // checksum, restore_test, encryption, integrity, full
            $table->string('status'); // pending, running, passed, failed
            $table->string('method'); // automated, manual
            $table->json('verification_config')->nullable();
            $table->text('details')->nullable();
            $table->boolean('checksum_valid')->nullable();
            $table->boolean('encryption_valid')->nullable();
            $table->boolean('integrity_valid')->nullable();
            $table->boolean('restore_successful')->nullable();
            $table->text('error_message')->nullable();
            $table->unsignedInteger('duration_seconds')->default(0);
            $table->timestampTz('started_at')->nullable();
            $table->timestampTz('completed_at')->nullable();
            $table->json('metadata')->nullable();
            $table->uuid('tenant_id')->nullable();
            $table->uuid('verified_by')->nullable();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('backup_snapshot_id')
                ->references('id')
                ->on('backup_snapshots')
                ->onDelete('cascade');

            $table->index(['tenant_id', 'status']);
            $table->index(['backup_snapshot_id', 'status']);
            $table->index(['type', 'status']);
            $table->index(['completed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('backup_verifications');
    }
};
