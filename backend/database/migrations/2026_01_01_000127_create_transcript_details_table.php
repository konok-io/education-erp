<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transcript_details', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('transcript_id')->nullable()->constrained('transcripts')->cascadeOnDelete();
            $table->string('semester', 50);
            $table->string('course_code', 50)->nullable();
            $table->string('course_name')->nullable();
            $table->decimal('credits', 6, 2)->default(0);
            $table->string('grade', 10)->nullable();
            $table->decimal('grade_point', 5, 2)->nullable();
            $table->decimal('marks', 8, 2)->nullable();
            $table->decimal('semester_gpa', 5, 2)->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->unique(['transcript_id', 'semester', 'course_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transcript_details');
    }
};
