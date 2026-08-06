<?php

declare(strict_types=1);

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
            $table->foreignId('exam_id')->nullable()->constrained('exams')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name')->nullable();
            $table->foreignId('center_id')->nullable()->constrained('exam_centers')->nullOnDelete();
            $table->string('room', 100)->nullable();
            $table->enum('shift', ['morning', 'evening'])->default('morning');
            $table->time('reporting_time')->nullable();
            $table->enum('status', ['assigned', 'confirmed', 'absent'])->default('assigned');
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->index(['exam_id', 'center_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_invigilators');
    }
};
