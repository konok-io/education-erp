<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seat_plans', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('plan_code', 50)->unique();
            $table->foreignId('exam_id')->nullable()->constrained('exams')->cascadeOnDelete();
            $table->foreignId('center_id')->nullable()->constrained('exam_centers')->nullOnDelete();
            $table->string('room', 100)->nullable();
            $table->string('floor', 50)->nullable();
            $table->date('exam_date')->nullable();
            $table->time('start_time')->nullable();
            $table->integer('total_seats')->default(0);
            $table->json('seats')->nullable();
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['exam_id', 'center_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seat_plans');
    }
};
