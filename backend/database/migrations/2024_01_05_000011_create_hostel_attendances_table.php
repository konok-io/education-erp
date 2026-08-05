<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hostel_attendances', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('hostel_id')->nullable()->constrained()->nullOnDelete();
            $table->string('attendance_type', 50)->default('night');
            $table->date('attendance_date');
            $table->string('student_name')->nullable();
            $table->string('student_id_number')->nullable();
            $table->foreignId('bed_id')->nullable()->constrained()->nullOnDelete();
            $table->time('check_in_time')->nullable();
            $table->time('check_out_time')->nullable();
            $table->string('status', 50)->default('present');
            $table->text('remarks')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            $table->index(['attendance_date', 'attendance_type']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hostel_attendances');
    }
};
