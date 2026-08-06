<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hostel_rooms', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('room_number', 50);
            $table->foreignId('building_id')->constrained('hostel_buildings')->cascadeOnDelete();
            $table->integer('floor')->default(1);
            $table->enum('room_type', ['single', 'double', 'triple', 'four_seat', 'dormitory', 'vip', 'guest'])->default('double');
            $table->integer('capacity')->default(2);
            $table->integer('current_occupancy')->default(0);
            $table->decimal('rent', 10, 2)->default(0);
            $table->text('description')->nullable();
            $table->enum('status', ['available', 'full', 'maintenance', 'blocked'])->default('available');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['building_id', 'room_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hostel_rooms');
    }
};
