<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transport_routes', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('route_code', 50)->unique();
            $table->string('name');
            $table->string('name_bn')->nullable();
            $table->decimal('distance', 8, 2)->default(0);
            $table->string('distance_unit', 20)->default('km');
            $table->integer('estimated_time')->default(30);
            $table->foreignId('vehicle_id')->nullable()->constrained('transport_vehicles')->nullOnDelete();
            $table->foreignId('driver_id')->nullable()->constrained('transport_drivers')->nullOnDelete();
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transport_routes');
    }
};
