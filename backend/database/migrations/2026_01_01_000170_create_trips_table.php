<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('trip_no')->unique();
            $table->foreignId('vehicle_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('driver_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('route_id')->nullable()->constrained()->nullOnDelete();
            $table->string('trip_type', 50)->default('regular');
            $table->date('trip_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('start_odometer', 20)->nullable();
            $table->string('end_odometer', 20)->nullable();
            $table->decimal('distance', 8, 2)->nullable();
            $table->integer('passenger_count')->default(0);
            $table->string('status', 50)->default('scheduled');
            $table->text('remarks')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            $table->index('trip_no');
            $table->index('trip_date');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};
