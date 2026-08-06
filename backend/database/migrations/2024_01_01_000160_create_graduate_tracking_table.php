<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('graduate_tracking', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('alumni_id')->nullable()->constrained('alumni_members')->nullOnDelete();
            $table->string('name')->nullable();
            $table->year('graduation_year')->nullable();
            $table->string('department', 100)->nullable();
            $table->string('degree', 100)->nullable();
            $table->enum('employment_status', ['employed', 'unemployed', 'self_employed', 'higher_study', 'entrepreneur', 'government_service', 'abroad'])->default('unemployed');
            $table->string('current_organization', 200)->nullable();
            $table->string('designation', 150)->nullable();
            $table->text('work_description')->nullable();
            $table->decimal('current_salary', 12, 2)->nullable();
            $table->string('currency', 10)->default('BDT');
            $table->string('employment_type', 50)->nullable();
            $table->string('industry', 100)->nullable();
            $table->text('skills')->nullable();
            $table->enum('location_type', ['local', 'abroad'])->default('local');
            $table->string('country', 100)->nullable();
            $table->string('city', 100)->nullable();
            $table->year('higher_study_year')->nullable();
            $table->string('university', 200)->nullable();
            $table->string('study_country', 100)->nullable();
            $table->string('degree_pursuing', 100)->nullable();
            $table->text('achievements')->nullable();
            $table->text('publications')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['graduation_year', 'employment_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('graduate_tracking');
    }
};
