<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alumni_surveys', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('survey_code')->unique();
            $table->string('survey_title');
            $table->text('description')->nullable();
            $table->string('survey_type', 50);
            $table->json('questions')->nullable();
            $table->text('target_audience')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('responses_count')->default(0);
            $table->boolean('is_anonymous')->default(true);
            $table->boolean('is_active')->default(true);
            $table->string('status', 50)->default('draft');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            $table->index('survey_code');
            $table->index('survey_type');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alumni_surveys');
    }
};
