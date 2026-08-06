<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('course_code', 50)->unique();
            $table->string('name');
            $table->foreignId('program_id')->nullable()->constrained('programs')->nullOnDelete();
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->foreignId('teacher_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->text('description')->nullable();
            $table->integer('credits')->default(3);
            $table->integer('theory_hours')->default(0);
            $table->integer('practical_hours')->default(0);
            $table->enum('course_type', ['theory', 'practical', 'both', 'project'])->default('theory');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('course_code');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
