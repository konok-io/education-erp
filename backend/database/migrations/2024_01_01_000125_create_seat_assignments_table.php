<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seat_assignments', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('seat_plan_id')->nullable()->constrained('seat_plans')->cascadeOnDelete();
            $table->unsignedBigInteger('student_id')->nullable();
            $table->string('student_name')->nullable();
            $table->string('roll_number', 50)->nullable();
            $table->string('seat_number', 20);
            $table->string('row_number', 10)->nullable();
            $table->string('column_number', 10)->nullable();
            $table->boolean('is_present')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->unique(['seat_plan_id', 'seat_number']);
            $table->unique(['seat_plan_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seat_assignments');
    }
};
