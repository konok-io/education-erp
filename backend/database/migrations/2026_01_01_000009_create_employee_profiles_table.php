<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_profiles', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('employeeable_id');
            $table->string('employeeable_type');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('full_name');
            $table->string('photo')->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('mobile', 20)->nullable();
            $table->string('nid', 30)->nullable();
            $table->string('passport', 30)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('religion', 50)->nullable();
            $table->string('nationality', 50)->nullable();
            $table->enum('marital_status', ['single', 'married', 'divorced', 'widowed'])->nullable();
            $table->string('blood_group', 5)->nullable();
            $table->text('present_address')->nullable();
            $table->text('permanent_address')->nullable();
            $table->timestamps();
            
            $table->index('employeeable_id');
            $table->index('employeeable_type');
            $table->index('email');
            $table->index('mobile');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_profiles');
    }
};
