<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gradebooks', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('campus_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('academic_level_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('section_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('course_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('teacher_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->string('name');
            $table->string('term', 50)->nullable(); // first, second, final
            $table->decimal('total_marks', 10, 2)->default(100);
            $table->decimal('passing_marks', 10, 2)->default(0);
            $table->date('exam_date')->nullable();
            $table->string('status', 50)->default('draft'); // draft, published
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gradebooks');
    }
};
