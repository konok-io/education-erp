<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounts_payable', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('bill_number')->unique();
            $table->string('ap_type', 50);
            $table->string('supplier_name');
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->string('reference_number')->nullable();
            $table->date('bill_date');
            $table->date('due_date');
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->decimal('outstanding_amount', 15, 2)->default(0);
            $table->string('currency', 10)->default('BDT');
            $table->string('status', 50)->default('pending');
            $table->string('payment_method', 50)->nullable();
            $table->text('description')->nullable();
            $table->foreignId('fiscal_year_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('journal_entry_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('paid_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            $table->index('bill_number');
            $table->index('supplier_name');
            $table->index('status');
            $table->index('due_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounts_payable');
    }
};
