<?php

declare(strict_types=1);

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
            $table->unsignedBigInteger('student_id')->nullable();
            $table->foreignId('building_id')->nullable()->constrained('hostel_buildings')->nullOnDelete();
            $table->date('date');
            $table->enum('status', ['present', 'absent', 'leave', 'late', 'night_out'])->default('present');
            $table->time('check_in_time')->nullable();
            $table->time('check_out_time')->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['student_id', 'date']);
            $table->index(['date', 'building_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hostel_attendances');
    }
};
