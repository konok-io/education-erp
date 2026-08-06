<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alumni_profiles', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('membership_number')->unique();
            $table->unsignedBigInteger('student_id')->nullable();
            $table->string('registration_number')->nullable();
            $table->string('full_name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('photo')->nullable();
            $table->year('passing_year');
            $table->string('department')->nullable();
            $table->string('program')->nullable();
            $table->string('current_occupation')->nullable();
            $table->string('current_organization')->nullable();
            $table->string('designation')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->text('address')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('twitter')->nullable();
            $table->string('facebook')->nullable();
            $table->string('website')->nullable();
            $table->text('bio')->nullable();
            $table->json('skills')->nullable();
            $table->json('education')->nullable();
            $table->json('experience')->nullable();
            $table->json('achievements')->nullable();
            $table->string('employment_status', 50)->default('employed');
            $table->decimal('current_salary', 12, 2)->nullable();
            $table->string('salary_currency', 10)->default('USD');
            $table->string('membership_type', 50)->default('lifetime');
            $table->date('membership_start_date')->nullable();
            $table->date('membership_end_date')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->string('verification_token')->nullable()->unique();
            $table->boolean('is_active')->default(true);
            $table->string('status', 50)->default('active');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
            
            $table->index('membership_number');
            $table->index('email');
            $table->index('passing_year');
            $table->index('department');
            $table->index('membership_type');
            $table->index('is_verified');
            $table->index('employment_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alumni_profiles');
    }
};
