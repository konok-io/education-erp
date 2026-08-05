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
            $table->string('session_name');
            $table->string('academic_year', 9);
            $table->string('semester', 20)->nullable();
            $table->string('term', 50)->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->text('description')->nullable();
            $table->string('status', 50)->default('upcoming');
            $table->boolean('is_current')->default(false);
            $table->timestamps();
            
            $table->index('academic_year');
            $table->index('status');
            $table->index('is_current');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_sessions');
    }
};
