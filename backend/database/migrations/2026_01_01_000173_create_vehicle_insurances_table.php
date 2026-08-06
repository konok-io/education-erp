<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_insurances', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('vehicle_id')->nullable()->constrained()->nullOnDelete();
            $table->string('policy_number')->unique();
            $table->string('insurance_type', 50)->default('comprehensive');
            $table->string('company_name')->nullable();
            $table->date('start_date');
            $table->date('expiry_date');
            $table->decimal('premium_amount', 12, 2)->nullable();
            $table->decimal('coverage_amount', 14, 2)->nullable();
            $table->string('agent_name')->nullable();
            $table->string('agent_phone')->nullable();
            $table->string('document')->nullable();
            $table->string('status', 50)->default('active');
            $table->text('remarks')->nullable();
            $table->timestamps();
            
            $table->index('policy_number');
            $table->index('vehicle_id');
            $table->index('expiry_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_insurances');
    }
};
