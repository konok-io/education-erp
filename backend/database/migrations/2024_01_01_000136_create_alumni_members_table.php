<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alumni_members', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('member_id', 50)->unique();
            $table->string('name');
            $table->string('name_bn')->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('gender', 20)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->text('present_address')->nullable();
            $table->text('permanent_address')->nullable();
            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('photo')->nullable();
            $table->string('nid', 50)->nullable();
            $table->unsignedBigInteger('student_id')->nullable();
            $table->string('roll_number', 50)->nullable();
            $table->string('registration_no', 50)->nullable();
            $table->year('admission_year')->nullable();
            $table->year('passing_year')->nullable();
            $table->string('program', 100)->nullable();
            $table->string('department', 100)->nullable();
            $table->string('degree', 100)->nullable();
            $table->string('occupation', 150)->nullable();
            $table->string('designation', 150)->nullable();
            $table->string('organization', 200)->nullable();
            $table->text('work_address')->nullable();
            $table->enum('membership_type', ['free', 'basic', 'premium', 'lifetime'])->default('free');
            $table->date('membership_start')->nullable();
            $table->date('membership_end')->nullable();
            $table->decimal('membership_fee', 10, 2)->default(0);
            $table->enum('status', ['active', 'inactive', 'expired'])->default('active');
            $table->text('skills')->nullable();
            $table->text('achievements')->nullable();
            $table->text('bio')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('facebook')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['email']);
            $table->index(['passing_year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alumni_members');
    }
};
