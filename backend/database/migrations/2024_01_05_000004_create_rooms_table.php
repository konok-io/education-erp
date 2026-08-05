<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('hostel_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('building_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('floor_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('room_number', 50);
            $table->string('room_code', 50)->unique();
            $table->string('room_type', 50)->default('double');
            $table->integer('floor_number')->nullable();
            $table->integer('capacity')->default(2);
            $table->integer('occupied')->default(0);
            $table->decimal('monthly_fee', 10, 2)->default(0);
            $table->decimal('security_deposit', 10, 2)->default(0);
            $table->string('location')->nullable();
            $table->text('description')->nullable();
            $table->string('status', 50)->default('available');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->unique(['building_id', 'room_number']);
            $table->index('room_code');
            $table->index('room_type');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
