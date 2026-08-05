<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('disciplinary_actions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('case_no', 50)->unique();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->enum('action_type', [
                'verbal_warning',
                'written_warning',
                'show_cause',
                'suspension',
                'fine',
                'demotion',
                'termination_recommendation'
            ]);
            $table->string('title');
            $table->text('description');
            $table->text('evidence')->nullable();
            $table->date('incident_date')->nullable();
            $table->date('issue_date');
            $table->date('response_deadline')->nullable();
            $table->text('employee_response')->nullable();
            $table->date('response_date')->nullable();
            $table->enum('status', [
                'pending',
                'under_investigation',
                'show_cause_issued',
                'response_received',
                'decided',
                'withdrawn',
                'cancelled'
            ])->default('pending');
            $table->enum('final_decision', [
                'no_action',
                'warning',
                'fine',
                'suspension',
                'demotion',
                'termination',
                'other'
            ])->nullable();
            $table->text('decision_details')->nullable();
            $table->date('decision_date')->nullable();
            $table->decimal('fine_amount', 12, 2)->nullable();
            $table->date('suspension_start')->nullable();
            $table->date('suspension_end')->nullable();
            $table->foreignId('investigation_officer')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('decided_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['employee_id', 'status']);
            $table->index('action_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disciplinary_actions');
    }
};
