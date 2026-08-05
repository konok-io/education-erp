<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_admit_cards', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('admit_card_no')->unique();
            $table->foreignId('exam_id')->nullable()->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('student_id')->nullable();
            $table->string('student_name')->nullable();
            $table->string('student_roll')->nullable();
            $table->string('registration_no')->nullable();
            $table->string('class_name')->nullable();
            $table->string('section')->nullable();
            $table->text('photo')->nullable();
            $table->text('signature')->nullable();
            $table->string('qr_code')->nullable();
            $table->string('barcode')->nullable();
            $table->string('verification_token')->nullable();
            $table->date('issue_date')->nullable();
            $table->date('valid_until')->nullable();
            $table->string('status', 50)->default('issued');
            $table->text('remarks')->nullable();
            $table->timestamps();
            
            $table->index('admit_card_no');
            $table->index('student_id');
            $table->index('verification_token');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_admit_cards');
    }
};
