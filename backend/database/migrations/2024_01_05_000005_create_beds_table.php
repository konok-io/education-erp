<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('beds', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('room_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('bed_number', 50);
            $table->string('bed_code', 50)->unique();
            $table->string('position', 20)->nullable();
            $table->string('status', 50)->default('available');
            $table->string('assignable_type', 100)->nullable();
            $table->unsignedBigInteger('assignable_id')->nullable();
            $table->date('allocation_date')->nullable();
            $table->date('checkout_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->unique(['room_id', 'bed_number']);
            $table->index('bed_code');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('beds');
    }
};
