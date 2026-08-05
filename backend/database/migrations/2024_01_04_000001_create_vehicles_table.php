<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('vehicle_number', 50)->unique();
            $table->string('registration_number', 50)->unique();
            $table->string('vehicle_type', 50);
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->year('manufacture_year')->nullable();
            $table->string('color')->nullable();
            $table->string('engine_number')->nullable();
            $table->string('chassis_number')->nullable();
            $table->integer('seat_capacity')->default(40);
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_cost', 14, 2)->nullable();
            $table->string('fuel_type', 50)->default('diesel');
            $table->decimal('tank_capacity', 8, 2)->nullable();
            $table->string('current_odometer', 20)->nullable();
            $table->string('status', 50)->default('active');
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('vehicle_number');
            $table->index('registration_number');
            $table->index('vehicle_type');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
