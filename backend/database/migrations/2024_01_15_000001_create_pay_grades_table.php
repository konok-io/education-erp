<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pay_grades', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('grade_code')->unique();
            $table->string('grade_name');
            $table->decimal('min_salary', 12, 2)->default(0);
            $table->decimal('max_salary', 12, 2)->default(0);
            $table->decimal('basic_percent', 5, 2)->default(40);
            $table->decimal('house_rent_percent', 5, 2)->default(25);
            $table->decimal('medical_percent', 5, 2)->default(10);
            $table->decimal('transport_percent', 5, 2)->default(10);
            $table->decimal('other_percent', 5, 2)->default(15);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->text('description')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            $table->index('grade_code');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pay_grades');
    }
};
