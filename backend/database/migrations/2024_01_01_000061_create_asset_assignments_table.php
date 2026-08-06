<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_assignments', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('assignment_no', 50)->unique();
            $table->foreignId('asset_id')->constrained('assets')->cascadeOnDelete();
            $table->foreignId('assignee_id')->nullable();
            $table->string('assignee_type')->default('employee'); // employee, student, department
            $table->foreignId('assigned_by')->constrained('users')->cascadeOnDelete();
            $table->date('assignment_date');
            $table->date('expected_return_date')->nullable();
            $table->date('actual_return_date')->nullable();
            $table->enum('status', ['active', 'returned', 'overdue'])->default('active');
            $table->text('purpose')->nullable();
            $table->text('return_notes')->nullable();
            $table->enum('return_condition', ['excellent', 'good', 'fair', 'poor', 'damaged'])->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['asset_id', 'status']);
            $table->index(['assignee_id', 'assignee_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_assignments');
    }
};
