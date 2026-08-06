<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recovery_drills', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('backup_policy_id')->nullable();
            $table->uuid('dr_site_id')->nullable();
            $table->string('name');
            $table->string('type'); // full, partial, database, files, point_in_time
            $table->string('status'); // scheduled, in_progress, completed, failed, cancelled
            $table->string('scenario'); // disaster, corruption, ransomware, maintenance
            $table->text('description')->nullable();
            $table->json('drill_config')->nullable();
            $table->timestampTz('scheduled_at')->nullable();
            $table->timestampTz('started_at')->nullable();
            $table->timestampTz('completed_at')->nullable();
            $table->unsignedInteger('planned_duration_minutes')->nullable();
            $table->unsignedInteger('actual_duration_minutes')->nullable();
            $table->unsignedInteger('recovery_time_minutes')->nullable();
            $table->unsignedInteger('data_loss_minutes')->nullable();
            $table->boolean('rto_achieved')->nullable();
            $table->boolean('rpo_achieved')->nullable();
            $table->text('findings')->nullable();
            $table->text('recommendations')->nullable();
            $table->text('lessons_learned')->nullable();
            $table->json('attachments')->nullable();
            $table->json('participants')->nullable();
            $table->json('metadata')->nullable();
            $table->uuid('tenant_id')->nullable();
            $table->uuid('conducted_by')->nullable();
            $table->uuid('approved_by')->nullable();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('backup_policy_id')
                ->references('id')
                ->on('backup_policies')
                ->onDelete('set null');

            $table->foreign('dr_site_id')
                ->references('id')
                ->on('dr_sites')
                ->onDelete('set null');

            $table->index(['tenant_id', 'status']);
            $table->index(['type', 'status']);
            $table->index(['scheduled_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recovery_drills');
    }
};
