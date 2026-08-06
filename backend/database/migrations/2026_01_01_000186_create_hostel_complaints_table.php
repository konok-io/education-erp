<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hostel_complaints', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('complaint_no')->unique();
            $table->foreignId('hostel_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('room_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('student_id')->nullable()->constrained()->nullOnDelete();
            $table->string('complaint_type', 50);
            $table->string('priority', 50)->default('normal');
            $table->text('description');
            $table->text('response')->nullable();
            $table->string('assigned_to')->nullable();
            $table->date('response_date')->nullable();
            $table->string('status', 50)->default('pending');
            $table->foreignId('reported_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('resolution')->nullable();
            $table->date('resolved_date')->nullable();
            $table->text('feedback')->nullable();
            $table->timestamps();
            
            $table->index('complaint_no');
            $table->index('complaint_type');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hostel_complaints');
    }
};
