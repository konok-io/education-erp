<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transport_allocations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('allocation_no', 50)->unique();
            $table->unsignedBigInteger('student_id')->nullable();
            $table->foreignId('route_id')->nullable()->constrained('transport_routes')->nullOnDelete();
            $table->foreignId('pickup_stop_id')->nullable()->constrained('transport_stops')->nullOnDelete();
            $table->foreignId('drop_stop_id')->nullable()->constrained('transport_stops')->nullOnDelete();
            $table->integer('seat_number')->nullable();
            $table->decimal('monthly_fee', 10, 2)->default(0);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->enum('status', ['active', 'paused', 'cancelled', 'expired'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['student_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transport_allocations');
    }
};
