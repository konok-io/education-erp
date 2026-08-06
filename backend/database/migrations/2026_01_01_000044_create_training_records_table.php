<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('training_types', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('name_bn')->nullable();
            $table->string('code', 20)->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('training_records', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('training_no', 50)->unique();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('training_type_id')->constrained('training_types')->cascadeOnDelete();
            $table->string('training_name');
            $table->string('organizer')->nullable();
            $table->string('venue')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->integer('duration_days')->default(1);
            $table->integer('duration_hours')->default(0);
            $table->string('certificate_number')->nullable();
            $table->date('certificate_date')->nullable();
            $table->enum('result', [
                'pending',
                'passed',
                'failed',
                'incomplete',
                'excellent',
                'very_good',
                'good'
            ])->default('pending');
            $table->text('feedback')->nullable();
            $table->integer('score')->nullable();
            $table->decimal('cost', 12, 2)->nullable();
            $table->string('certificate_file')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['employee_id', 'start_date']);
            $table->index('training_type_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_records');
        Schema::dropIfExists('training_types');
    }
};
