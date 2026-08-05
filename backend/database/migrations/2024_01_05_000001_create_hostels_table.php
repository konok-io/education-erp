<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hostels', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('hostel_name');
            $table->string('hostel_code', 50)->unique();
            $table->string('hostel_type', 50);
            $table->enum('gender', ['boys', 'girls', 'co-ed', 'mixed'])->default('co-ed');
            $table->foreignId('campus_id')->nullable()->constrained()->nullOnDelete();
            $table->string('manager_name')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->integer('total_buildings')->default(0);
            $table->integer('total_rooms')->default(0);
            $table->integer('total_beds')->default(0);
            $table->integer('occupied_beds')->default(0);
            $table->text('description')->nullable();
            $table->text('notes')->nullable();
            $table->string('status', 50)->default('active');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('hostel_code');
            $table->index('hostel_type');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hostels');
    }
};
