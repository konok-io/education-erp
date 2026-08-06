<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hostel_buildings', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('building_code', 50)->unique();
            $table->string('name');
            $table->string('name_bn')->nullable();
            $table->foreignId('campus_id')->nullable()->constrained('campuses')->nullOnDelete();
            $table->enum('gender', ['male', 'female', 'mixed'])->default('mixed');
            $table->integer('total_floors')->default(1);
            $table->integer('total_rooms')->default(0);
            $table->integer('total_beds')->default(0);
            $table->text('address')->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'inactive', 'maintenance'])->default('active');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hostel_buildings');
    }
};
