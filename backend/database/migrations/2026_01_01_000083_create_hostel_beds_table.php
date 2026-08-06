<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hostel_beds', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('bed_number', 50);
            $table->foreignId('room_id')->constrained('hostel_rooms')->cascadeOnDelete();
            $table->enum('position', ['lower', 'middle', 'upper'])->default('lower');
            $table->enum('status', ['available', 'reserved', 'occupied', 'maintenance', 'blocked'])->default('available');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['room_id', 'bed_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hostel_beds');
    }
};
