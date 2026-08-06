<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('vendor_code', 50)->unique();
            $table->string('name');
            $table->string('name_bn')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('mobile', 20)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('district', 100)->nullable();
            $table->string('country', 100)->nullable();
            $table->string('tax_id')->nullable();
            $table->string('trade_license')->nullable();
            $table->enum('vendor_type', ['supplier', 'contractor', 'service_provider', 'manufacturer'])->default('supplier');
            $table->enum('status', ['active', 'inactive', 'blacklisted'])->default('active');
            $table->integer('rating')->default(0); // 0-5
            $table->text('payment_terms')->nullable();
            $table->integer('credit_limit')->default(0);
            $table->integer('credit_days')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'vendor_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};
