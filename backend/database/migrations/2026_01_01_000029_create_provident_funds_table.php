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
            $table->string('pf_no')->unique();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->decimal('employee_contribution', 12, 2)->default(0);
            $table->decimal('employer_contribution', 12, 2)->default(0);
            $table->decimal('total_contribution', 12, 2)->default(0);
            $table->decimal('interest_earned', 12, 2)->default(0);
            $table->decimal('total_balance', 12, 2)->default(0);
            $table->decimal('withdrawn_amount', 12, 2)->default(0);
            $table->enum('status', ['active', 'closed', 'frozen'])->default('active');
            $table->date('activation_date');
            $table->date('closing_date')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('pf_no');
            $table->index('status');
            $table->index(['employee_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('provident_funds');
    }
};
