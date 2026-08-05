<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('exam_name');
            $table->string('exam_code', 50)->unique();
            $table->string('exam_type', 50);
            $table->foreignId('exam_session_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('class_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('section_id')->nullable()->constrained()->nullOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->date('result_publish_date')->nullable();
            $table->text('description')->nullable();
            $table->text('instructions')->nullable();
            $table->string('status', 50)->default('scheduled');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_published')->default(false);
            $table->timestamps();
            
            $table->index('exam_code');
            $table->index('exam_type');
            $table->index('status');
            $table->index(['exam_session_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
