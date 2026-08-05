<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salary_grades', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('grade_name');
            $table->decimal('basic_salary', 12, 2);
            $table->decimal('house_rent_percent', 5, 2)->default(30);
            $table->decimal('medical_percent', 5, 2)->default(10);
            $table->decimal('transport_percent', 5, 2)->default(10);
            $table->decimal('mobile_allowance', 12, 2)->default(0);
            $table->decimal('special_allowance', 12, 2)->default(0);
            $table->decimal('other_allowance', 12, 2)->default(0);
            $table->decimal('provident_fund_percent', 5, 2)->default(10);
            $table->decimal('tax_percent', 5, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_grades');
    }
};
