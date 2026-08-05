<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_maintenances', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('maintenance_number')->unique();
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            $table->string('maintenance_type', 50);
            $table->string('priority', 50)->default('normal');
            $table->string('status', 50)->default('scheduled');
            $table->date('scheduled_date');
            $table->date('completed_date')->nullable();
            $table->decimal('cost', 10, 2)->nullable();
            $table->string('vendor')->nullable();
            $table->string('technician')->nullable();
            $table->text('description')->nullable();
            $table->text('work_done')->nullable();
            $table->text('parts_replaced')->nullable();
            $table->foreignId('performed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('maintenance_number');
            $table->index('asset_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_maintenances');
    }
};
