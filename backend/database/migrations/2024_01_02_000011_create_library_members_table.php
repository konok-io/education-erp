<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('library_members', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('member_no')->unique();
            $table->string('member_type', 50);
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('photo')->nullable();
            $table->string('department')->nullable();
            $table->string('student_id')->nullable();
            $table->string('employee_id')->nullable();
            $table->date('joining_date');
            $table->date('expiry_date')->nullable();
            $table->string('status', 50)->default('active');
            $table->unsignedSmallInteger('max_books')->default(5);
            $table->unsignedSmallInteger('max_days')->default(14);
            $table->decimal('fine_rate', 10, 2)->default(5.00);
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('member_no');
            $table->index('member_type');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('library_members');
    }
};
