<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trial_balances', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('report_code')->unique();
            $table->date('report_date');
            $table->foreignId('fiscal_year_id')->nullable()->constrained()->nullOnDelete();
            $table->string('period_name')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('total_opening_debit', 15, 2)->default(0);
            $table->decimal('total_opening_credit', 15, 2)->default(0);
            $table->decimal('total_debit', 15, 2)->default(0);
            $table->decimal('total_credit', 15, 2)->default(0);
            $table->decimal('total_closing_debit', 15, 2)->default(0);
            $table->decimal('total_closing_credit', 15, 2)->default(0);
            $table->string('status', 50)->default('draft');
            $table->json('entries')->nullable();
            $table->foreignId('generated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('generated_at')->nullable();
            $table->timestamps();
            
            $table->index('report_code');
            $table->index('fiscal_year_id');
            $table->index('report_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trial_balances');
    }
};
