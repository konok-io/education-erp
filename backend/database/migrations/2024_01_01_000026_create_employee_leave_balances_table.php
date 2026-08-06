<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_leave_balances', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('leave_type_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('fiscal_year');
            $table->integer('total_days')->default(0);
            $table->integer('used_days')->default(0);
            $table->integer('pending_days')->default(0);
            $table->integer('carried_forward')->default(0);
            $table->integer('balance')->default(0);
            $table->timestamps();
            
            $table->unique(['employee_id', 'leave_type_id', 'fiscal_year'], 'elb_unique');
            $table->index(['employee_id', 'fiscal_year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_leave_balances');
    }
};
