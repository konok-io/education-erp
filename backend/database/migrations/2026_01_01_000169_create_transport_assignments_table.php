<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transport_assignments', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('assignment_no')->unique();
            $table->string('assignable_type', 100);
            $table->unsignedBigInteger('assignable_id');
            $table->foreignId('route_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('vehicle_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('driver_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('pickup_stop_id')->nullable()->constrained('route_stops')->nullOnDelete();
            $table->foreignId('drop_stop_id')->nullable()->constrained('route_stops')->nullOnDelete();
            $table->decimal('monthly_fee', 10, 2)->default(0);
            $table->date('effective_date');
            $table->date('end_date')->nullable();
            $table->string('status', 50)->default('active');
            $table->text('remarks')->nullable();
            $table->timestamps();
            
            $table->index(['assignable_type', 'assignable_id']);
            $table->index('route_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transport_assignments');
    }
};
