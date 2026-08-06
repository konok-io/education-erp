<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alumni_donations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('receipt_no', 50)->unique();
            $table->foreignId('alumni_id')->nullable()->constrained('alumni_members')->nullOnDelete();
            $table->string('donor_name')->nullable();
            $table->string('donor_email')->nullable();
            $table->string('donor_phone', 20)->nullable();
            $table->decimal('amount', 12, 2)->default(0);
            $table->string('currency', 10)->default('BDT');
            $table->enum('payment_method', ['cash', 'bank', 'card', 'mobile_banking', 'online', 'check'])->default('bank');
            $table->string('transaction_id')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('branch_name')->nullable();
            $table->string('check_no')->nullable();
            $table->date('payment_date')->nullable();
            $table->enum('donation_type', ['one_time', 'monthly', 'quarterly', 'yearly'])->default('one_time');
            $table->foreignId('fund_id')->nullable()->constrained('alumni_funds')->nullOnDelete();
            $table->string('fund_name')->nullable();
            $table->text('purpose')->nullable();
            $table->text('remarks')->nullable();
            $table->enum('status', ['pending', 'received', 'confirmed', 'refunded'])->default('pending');
            $table->string('receipt_path')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['alumni_id', 'status']);
            $table->index(['payment_date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alumni_donations');
    }
};
