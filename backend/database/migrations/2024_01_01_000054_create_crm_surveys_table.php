<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_surveys', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('survey_no', 50)->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('survey_type', [
                'course',
                'teacher_evaluation',
                'campus_feedback',
                'service_quality',
                'custom',
            ])->default('custom');
            $table->json('questions'); // [{type, question, options, required}]
            $table->enum('status', ['draft', 'active', 'closed'])->default('draft');
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_anonymous')->default(false);
            $table->boolean('allow_multiple')->default(false);
            $table->boolean('show_results')->default(true);
            $table->json('target_audience')->nullable();
            $table->integer('total_responses')->default(0);
            $table->decimal('average_rating', 3, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'survey_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_surveys');
    }
};
