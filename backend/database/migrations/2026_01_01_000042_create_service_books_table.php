<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_books', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('entry_no', 50);
            $table->date('entry_date');
            $table->enum('event_type', [
                'joining',
                'promotion',
                'transfer',
                'salary_revision',
                'leave',
                'award',
                'punishment',
                'training',
                'performance_review',
                'confirmation',
                'resignation',
                'retirement',
                'termination',
                'other'
            ]);
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            // For promotion: old_designation, new_designation, old_salary, new_salary
            // For transfer: from_department, to_department, from_campus, to_campus
            // For training: training_name, organizer, duration, result
            // For award: award_name, award_type, date
            // For punishment: punishment_type, details, duration
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->date('approved_date')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['employee_id', 'event_type']);
            $table->index('entry_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_books');
    }
};
