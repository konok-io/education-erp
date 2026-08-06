<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('confirmation_records', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('confirmation_no', 50)->unique();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->date('probation_start_date');
            $table->date('probation_end_date');
            $table->text('performance_summary')->nullable();
            $table->enum('recommendation', [
                'confirm',
                'extend_probation',
                'terminate'
            ])->default('confirm');
            $table->text('recommendation_remarks')->nullable();
            $table->enum('status', [
                'pending',
                'under_review',
                'recommended',
                'approved',
                'rejected',
                'cancelled'
            ])->default('pending');
            $table->foreignId('recommended_by')->nullable()->constrained('users')->nullOnDelete();
            $table->date('recommended_date')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->date('reviewed_date')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->date('approved_date')->nullable();
            $table->date('confirmation_date')->nullable();
            $table->string('confirmation_letter')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['employee_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('confirmation_records');
    }
};
