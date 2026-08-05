<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('code', 50)->unique();
            $table->string('company_name');
            $table->string('contact_person')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('mobile', 20)->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('country', 100)->default('Bangladesh');
            $table->string('trade_license')->nullable();
            $table->string('tin')->nullable();
            $table->string('bin')->nullable();
            $table->string('vat_number')->nullable();
            $table->string('website')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_account')->nullable();
            $table->decimal('credit_limit', 12, 2)->nullable();
            $table->unsignedSmallInteger('payment_days')->default(30);
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('code');
            $table->index('company_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
