<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('survey_responses', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('survey_id')->constrained()->cascadeOnDelete();
            $table->foreignId('alumni_profile_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedBigInteger('respondent_id')->nullable();
            $table->string('respondent_name')->nullable();
            $table->string('respondent_email')->nullable();
            $table->json('responses')->nullable();
            $table->text('additional_comments')->nullable();
            $table->string('status', 50)->default('submitted');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
            
            $table->index('survey_id');
            $table->index('respondent_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('survey_responses');
    }
};
