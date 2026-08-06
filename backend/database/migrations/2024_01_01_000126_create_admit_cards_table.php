<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new new class extends Migration
{
    public function up(): void
    {
        Schema::create('admit_cards', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('admit_card_no', 50)->unique();
            $table->foreignId('student_id')->nullable()->constrained('students')->nullOnDelete();
            $table->string('student_name')->nullable();
            $table->string('roll_number', 50)->nullable();
            $table->string('registration_no', 50)->nullable();
            $table->foreignId('session_id')->nullable()->constrained('exam_sessions')->nullOnDelete();
            $table->foreignId('exam_id')->nullable()->constrained('exams')->nullOnDelete();
            $table->foreignId('center_id')->nullable()->constrained('exam_centers')->nullOnDelete();
            $table->string('photo')->nullable();
            $table->string('qr_code')->nullable();
            $table->date('issue_date')->nullable();
            $table->date('exam_date')->nullable();
            $table->enum('status', ['pending', 'issued', 'downloaded', 'cancelled'])->default('pending');
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['student_id', 'session_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admit_cards');
    }
};
