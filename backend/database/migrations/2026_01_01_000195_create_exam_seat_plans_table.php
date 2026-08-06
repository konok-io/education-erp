<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_seat_plans', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('exam_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('exam_subject_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('exam_hall_id')->nullable()->constrained()->nullOnDelete();
            $table->integer('row_number')->nullable();
            $table->integer('column_number')->nullable();
            $table->string('seat_number', 20);
            $table->string('student_type', 50)->nullable();
            $table->unsignedBigInteger('student_id')->nullable();
            $table->string('student_name')->nullable();
            $table->string('student_roll')->nullable();
            $table->string('registration_no')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            
            $table->index(['exam_id', 'exam_hall_id']);
            $table->index('student_id');
            $table->index('seat_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_seat_plans');
    }
};
