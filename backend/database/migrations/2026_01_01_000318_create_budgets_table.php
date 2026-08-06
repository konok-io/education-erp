<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('budget_code')->unique();
            $table->string('budget_name');
            $table->string('budget_type', 50)->default('annual');
            $table->string('department')->nullable();
            $table->string('category', 100)->nullable();
            $table->string('fiscal_year', 10);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->decimal('allocated_amount', 15, 2)->default(0);
            $table->decimal('spent_amount', 15, 2)->default(0);
            $table->decimal('remaining_amount', 15, 2)->default(0);
            $table->decimal('utilization_percent', 5, 2)->default(0);
            $table->date('start_date');
            $table->date('end_date');
            $table->string('status', 50)->default('draft');
            $table->text('description')->nullable();
            $table->foreignId('fiscal_year_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            
            $table->index('budget_code');
            $table->index('fiscal_year');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budgets');
    }
};
