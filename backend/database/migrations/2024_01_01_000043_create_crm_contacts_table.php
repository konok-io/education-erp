<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_contacts', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('contact_no', 50)->unique();
            $table->string('full_name');
            $table->string('photo')->nullable();
            $table->enum('contact_type', [
                'prospective_student',
                'student',
                'guardian',
                'teacher',
                'staff',
                'vendor',
                'supplier',
                'alumni',
                'visitor',
                'organization',
            ]);
            $table->string('mobile', 20)->nullable();
            $table->string('alternative_mobile', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 20)->nullable();
            $table->text('present_address')->nullable();
            $table->text('permanent_address')->nullable();
            $table->string('district', 100)->nullable();
            $table->string('division', 100)->nullable();
            $table->string('country', 100)->nullable();
            $table->string('organization')->nullable();
            $table->string('designation')->nullable();
            $table->foreignId('student_id')->nullable()->unique()->constrained('students')->nullOnDelete();
            $table->foreignId('guardian_id')->nullable()->unique()->constrained('guardians')->nullOnDelete();
            $table->foreignId('employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->json('social_links')->nullable();
            $table->json('tags')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['active', 'inactive', 'blocked'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['contact_type', 'status']);
            $table->index('mobile');
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_contacts');
    }
};
