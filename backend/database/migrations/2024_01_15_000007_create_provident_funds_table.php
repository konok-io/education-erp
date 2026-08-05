<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('provident_funds', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->string('pf_number')->unique();
            $table->date('joining_date');
            $table->decimal('employee_contribution_rate', 5, 2)->default(5);
            $table->decimal('employer_contribution_rate', 5, 2)->default(10);
            $table->decimal('employee_contribution', 12, 2)->default(0);
            $table->decimal('employer_contribution', 12, 2)->default(0);
            $table->decimal('total_contribution', 12, 2)->default(0);
            $table->decimal('interest_rate', 5, 2)->default(8);
            $table->decimal('interest_earned', 12, 2)->default(0);
            $table->decimal('total_balance', 12, 2)->default(0);
            $table->string('status', 50)->default('active');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            $table->index('pf_number');
            $table->index('employee_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('provident_funds');
    }
};
