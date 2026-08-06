<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transport_vehicles', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('vehicle_number', 50)->unique();
            $table->string('registration_no', 50)->unique();
            $table->enum('vehicle_type', ['bus', 'mini_bus', 'micro_bus', 'van', 'car', 'ambulance', 'other'])->default('bus');
            $table->string('brand', 100)->nullable();
            $table->string('model', 100)->nullable();
            $table->integer('capacity')->default(40);
            $table->string('color', 50)->nullable();
            $table->string('chassis_no', 100)->nullable();
            $table->string('engine_no', 100)->nullable();
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_price', 12, 2)->nullable();
            $table->date('insurance_expiry')->nullable();
            $table->date('tax_token')->nullable();
            $table->date('fitness_expiry')->nullable();
            $table->enum('fuel_type', ['petrol', 'diesel', 'cng', 'electric', 'hybrid'])->default('diesel');
            $table->decimal('avg_mileage', 8, 2)->nullable();
            $table->enum('status', ['active', 'maintenance', 'repair', 'decommissioned'])->default('active');
            $table->text('description')->nullable();
            $table->string('photo')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transport_vehicles');
    }
};
