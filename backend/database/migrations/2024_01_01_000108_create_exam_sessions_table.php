<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_sessions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->date('start_date');
            $table->date('end_date');
            $table->year('exam_year')->nullable();
            $table->string('exam_type')->nullable();
            $table->enum('status', ['upcoming', 'running', 'completed'])->default('upcoming');
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('exam_year');
            $table->index('exam_type');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_sessions');
    }
};
