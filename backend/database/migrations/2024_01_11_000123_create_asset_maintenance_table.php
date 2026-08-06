<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_maintenance', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('maintenance_no', 50)->unique();
            $table->foreignId('asset_id')->constrained('assets')->cascadeOnDelete();
            $table->enum('maintenance_type', ['preventive', 'corrective', 'inspection', 'calibration', 'upgrade'])->default('preventive');
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled'])->default('scheduled');
            $table->date('scheduled_date');
            $table->date('completed_date')->nullable();
            $table->foreignId('performed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('vendor_name')->nullable();
            $table->decimal('cost', 12, 2)->default(0);
            $table->text('description')->nullable();
            $table->text('work_done')->nullable();
            $table->text('next_maintenance_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['asset_id', 'status']);
            $table->index('scheduled_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_maintenance');
    }
};
