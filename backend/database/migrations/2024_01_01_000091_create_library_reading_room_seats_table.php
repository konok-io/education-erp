<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('library_reading_room_seats', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('seat_no', 20)->unique();
            $table->string('floor', 50)->default('Ground Floor');
            $table->string('zone', 50)->nullable();
            $table->enum('seat_type', ['individual', 'group', 'computer', 'silent'])->default('individual');
            $table->boolean('has_power')->default(false);
            $table->boolean('has_lamp')->default(false);
            $table->enum('status', ['available', 'maintenance', 'reserved'])->default('available');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('library_reading_room_seats');
    }
};
