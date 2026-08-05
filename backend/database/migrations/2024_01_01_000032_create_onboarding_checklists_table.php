<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('onboarding_checklists', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('checklist_name');
            $table->string('category'); // account, documents, equipment, training, payroll
            $table->integer('order')->default(0);
            $table->text('description')->nullable();
            $table->boolean('is_required')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['category', 'is_active']);
        });

        Schema::create('employee_onboardings', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('onboarding_no', 50)->unique();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('offer_letter_id')->nullable()->constrained('offer_letters')->nullOnDelete();
            $table->date('start_date');
            $table->date('completion_date')->nullable();
            $table->enum('status', [
                'pending',
                'in_progress',
                'completed',
                'cancelled'
            ])->default('pending');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['employee_id', 'status']);
        });

        Schema::create('onboarding_completions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('employee_onboarding_id')->constrained('employee_onboardings')->cascadeOnDelete();
            $table->foreignId('checklist_id')->constrained('onboarding_checklists')->cascadeOnDelete();
            $table->boolean('is_completed')->default(false);
            $table->date('completed_date')->nullable();
            $table->foreignId('completed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->unique(['employee_onboarding_id', 'checklist_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('onboarding_completions');
        Schema::dropIfExists('employee_onboardings');
        Schema::dropIfExists('onboarding_checklists');
    }
};
