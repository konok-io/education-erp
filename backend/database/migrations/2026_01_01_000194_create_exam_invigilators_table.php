<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_invigilators', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('exam_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('exam_hall_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('exam_subject_id')->nullable()->constrained()->nullOnDelete();
            $table->date('duty_date');
            $table->time('reporting_time')->nullable();
            $table->string('role', 50)->default('invigilator');
            $table->string('status', 50)->default('assigned');
            $table->text('remarks')->nullable();
            $table->timestamps();
            
            $table->index(['exam_id', 'duty_date']);
            $table->index('user_id');
            $table->index('exam_hall_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_invigilators');
    }
};
