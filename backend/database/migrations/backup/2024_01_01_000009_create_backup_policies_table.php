<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('backup_policies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->string('type'); // backup, archive, retention
            $table->string('status'); // active, inactive, suspended
            $table->json('schedule_config')->nullable();
            $table->string('backup_type'); // full, incremental, differential, snapshot
            $table->string('source_type'); // database, files, all
            $table->json('source_config')->nullable();
            $table->string('destination_type');
            $table->json('destination_config')->nullable();
            $table->string('encryption')->default('none');
            $table->string('compression')->default('gzip');
            $table->unsignedInteger('retention_days')->default(30);
            $table->unsignedInteger('retention_copies')->default(10);
            $table->boolean('immutable')->default(false);
            $table->integer('immutable_days')->nullable();
            $table->boolean('verify_on_backup')->default(true);
            $table->boolean('auto_prune')->default(true);
            $table->unsignedInteger('max_backup_size_bytes')->nullable();
            $table->json('notifications')->nullable();
            $table->json('metadata')->nullable();
            $table->string('environment')->default('production');
            $table->uuid('tenant_id')->nullable();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'status']);
            $table->index(['type', 'status']);
            $table->index(['environment']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('backup_policies');
    }
};
