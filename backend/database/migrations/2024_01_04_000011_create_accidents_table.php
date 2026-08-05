<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accidents', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('accident_no')->unique();
            $table->foreignId('vehicle_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('driver_id')->nullable()->constrained()->nullOnDelete();
            $table->date('accident_date');
            $table->time('accident_time')->nullable();
            $table->text('location')->nullable();
            $table->text('description')->nullable();
            $table->string('police_station')->nullable();
            $table->string('fir_number')->nullable();
            $table->integer('casualties')->default(0);
            $table->decimal('damage_cost', 12, 2)->nullable();
            $table->decimal('insurance_claim', 12, 2)->nullable();
            $table->string('claim_status', 50)->nullable();
            $table->string('status', 50)->default('reported');
            $table->foreignId('reported_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('remarks')->nullable();
            $table->timestamps();
            
            $table->index('accident_no');
            $table->index('vehicle_id');
            $table->index('accident_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accidents');
    }
};
