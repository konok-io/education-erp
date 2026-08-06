<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fuel_records', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('fuel_no')->unique();
            $table->foreignId('vehicle_id')->nullable()->constrained()->nullOnDelete();
            $table->date('fuel_date');
            $table->string('fuel_type', 50)->default('diesel');
            $table->decimal('quantity', 10, 2);
            $table->decimal('price_per_liter', 10, 2);
            $table->decimal('total_cost', 12, 2);
            $table->string('odometer_reading', 20)->nullable();
            $table->string('fuel_station')->nullable();
            $table->string('invoice_no')->nullable();
            $table->string('created_by')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            
            $table->index('fuel_no');
            $table->index('vehicle_id');
            $table->index('fuel_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fuel_records');
    }
};
