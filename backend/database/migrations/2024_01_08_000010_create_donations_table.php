<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('donation_number')->unique();
            $table->foreignId('campaign_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('alumni_profile_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedBigInteger('donor_id')->nullable();
            $table->string('donor_name');
            $table->string('donor_email');
            $table->string('donor_phone')->nullable();
            $table->string('donor_type', 50)->default('individual');
            $table->string('company_name')->nullable();
            $table->decimal('amount', 12, 2);
            $table->string('currency', 10)->default('USD');
            $table->string('payment_method', 50)->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('payment_status', 50)->default('pending');
            $table->string('donation_type', 50)->default('one_time');
            $table->string('fund_category', 100)->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_anonymous')->default(false);
            $table->boolean('is_tax_deductible')->default(false);
            $table->text('receipt_path')->nullable();
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('donated_at')->nullable();
            $table->timestamps();
            
            $table->index('donation_number');
            $table->index('campaign_id');
            $table->index('donor_email');
            $table->index('payment_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};
