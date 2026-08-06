<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('timetable_slots', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('timetable_id')->constrained('timetables')->cascadeOnDelete();
            $table->unsignedBigInteger('period_id')->nullable()->comment('References periods.id when table exists');
            $table->unsignedBigInteger('course_id')->nullable()->comment('References courses.id when table exists');
            $table->foreignId('teacher_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->unsignedBigInteger('room_id')->nullable()->comment('References rooms.id when table exists');
            $table->string('day', 20); // monday, tuesday, etc.
            $table->integer('order')->default(0);
            $table->string('type', 50)->default('lecture'); // lecture, lab, tutorial, break
            $table->json('settings')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('timetable_slots');
    }
};
