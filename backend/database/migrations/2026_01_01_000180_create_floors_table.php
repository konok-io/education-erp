<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('floors', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('building_id')->nullable()->constrained()->cascadeOnDelete();
            $table->integer('floor_number');
            $table->string('floor_name')->nullable();
            $table->integer('total_rooms')->default(0);
            $table->integer('total_beds')->default(0);
            $table->integer('occupied_beds')->default(0);
            $table->text('description')->nullable();
            $table->string('status', 50)->default('active');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('building_id');
            $table->index('floor_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('floors');
    }
};
