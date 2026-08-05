<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('library_reading_rooms', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('room_code')->unique();
            $table->string('room_name');
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->string('floor')->nullable();
            $table->integer('total_seats');
            $table->integer('available_seats');
            $table->boolean('has_computer')->default(false);
            $table->boolean('has_power_outlets')->default(false);
            $table->boolean('has_wifi')->default(true);
            $table->string('room_type', 50)->nullable();
            $table->string('operating_hours')->nullable();
            $table->decimal('hourly_rate', 10, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('room_code');
            $table->index('branch_id');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('library_reading_rooms');
    }
};
