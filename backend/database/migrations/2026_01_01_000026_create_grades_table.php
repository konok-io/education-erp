<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('gradebook_id')->constrained('gradebooks')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->decimal('obtained_marks', 10, 2)->default(0);
            $table->decimal('full_marks', 10, 2)->default(100);
            $table->string('letter_grade', 5)->nullable();
            $table->decimal('grade_point', 5, 2)->nullable();
            $table->string('status', 50)->default('evaluated'); // present, absent, exempted
            $table->text('feedback')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
