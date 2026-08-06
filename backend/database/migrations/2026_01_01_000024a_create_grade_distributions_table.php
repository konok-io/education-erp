<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grade_distributions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('campus_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('academic_level_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('type', 50); // exam, quiz, assignment, project, practical, attendance
            $table->decimal('weight', 5, 2)->default(0); // percentage
            $table->decimal('full_marks', 10, 2)->default(100);
            $table->decimal('passing_marks', 10, 2)->default(0);
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grade_distributions');
    }
};
