<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_attendances', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('exam_id')->nullable()->constrained('exams')->cascadeOnDelete();
            $table->foreignId('student_id')->nullable()->constrained('students')->nullOnDelete();
            $table->string('student_name')->nullable();
            $table->string('roll_number', 50)->nullable();
            $table->enum('status', ['present', 'absent', 'late', 'disqualified'])->default('present');
            $table->time('entry_time')->nullable();
            $table->time('exit_time')->nullable();
            $table->string('seat_number', 20)->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->unique(['exam_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_attendances');
    }
};
