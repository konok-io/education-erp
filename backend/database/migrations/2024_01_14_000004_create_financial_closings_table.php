<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_closings', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('closing_code')->unique();
            $table->string('closing_type', 50);
            $table->string('period_type', 50);
            $table->string('period_label');
            $table->date('start_date');
            $table->date('end_date');
            $table->date('closing_date');
            $table->string('status', 50)->default('draft');
            $table->boolean('is_locked')->default(false);
            $table->decimal('total_debit', 15, 2)->default(0);
            $table->decimal('total_credit', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->json('checklist')->nullable();
            $table->foreignId('fiscal_year_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('prepared_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            
            $table->index('closing_code');
            $table->index('closing_type');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_closings');
    }
};
