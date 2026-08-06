<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transport_maintenance_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('log_no', 50)->unique();
            $table->foreignId('vehicle_id')->nullable()->constrained('transport_vehicles')->nullOnDelete();
            $table->date('date');
            $table->enum('maintenance_type', ['oil_change', 'tyre', 'battery', 'repair', 'service', 'inspection', 'insurance', 'fitness', 'other'])->default('service');
            $table->text('description');
            $table->decimal('cost', 10, 2)->default(0);
            $table->string('vendor', 150)->nullable();
            $table->string('invoice_no', 100)->nullable();
            $table->date('next_due_date')->nullable();
            $table->integer('next_due_km')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['vehicle_id', 'maintenance_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transport_maintenance_logs');
    }
};
