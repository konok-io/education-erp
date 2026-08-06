<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transport_attendances', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('student_id')->nullable()->constrained('students')->nullOnDelete();
            $table->foreignId('route_id')->nullable()->constrained('transport_routes')->nullOnDelete();
            $table->foreignId('vehicle_id')->nullable()->constrained('transport_vehicles')->nullOnDelete();
            $table->date('date');
            $table->enum('trip_type', ['pickup', 'drop', 'both'])->default('both');
            $table->enum('pickup_status', ['on_time', 'late', 'absent', 'not_available'])->nullable();
            $table->enum('drop_status', ['on_time', 'late', 'absent', 'not_available'])->nullable();
            $table->time('pickup_time')->nullable();
            $table->time('drop_time')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->unique(['student_id', 'date']);
            $table->index(['route_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transport_attendances');
    }
};
