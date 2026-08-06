<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_malpractices', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('exam_subject_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('exam_hall_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedBigInteger('student_id')->nullable();
            $table->string('student_name')->nullable();
            $table->string('student_roll')->nullable();
            $table->string('seat_number')->nullable();
            $table->string('incident_type', 100);
            $table->text('description');
            $table->text('evidence')->nullable();
            $table->foreignId('invigilator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action_taken', 100)->nullable();
            $table->text('remarks')->nullable();
            $table->string('status', 50)->default('reported');
            $table->timestamps();
            
            $table->index('student_id');
            $table->index('incident_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_malpractices');
    }
};
