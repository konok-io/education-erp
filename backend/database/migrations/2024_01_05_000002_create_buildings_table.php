<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('buildings', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('hostel_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('building_name');
            $table->string('building_code', 50)->unique();
            $table->string('campus')->nullable();
            $table->text('address')->nullable();
            $table->integer('total_floors')->default(1);
            $table->integer('total_rooms')->default(0);
            $table->integer('total_beds')->default(0);
            $table->integer('occupied_beds')->default(0);
            $table->text('description')->nullable();
            $table->string('status', 50)->default('active');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('building_code');
            $table->index('hostel_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buildings');
    }
};
