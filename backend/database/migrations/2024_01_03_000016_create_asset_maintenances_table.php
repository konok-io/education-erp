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
            $table->string('maintenance_no')->unique();
            $table->foreignId('asset_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('maintenance_type', 50);
            $table->string('priority', 50)->default('normal');
            $table->date('scheduled_date')->nullable();
            $table->date('start_date')->nullable();
            $table->date('completion_date')->nullable();
            $table->string('vendor')->nullable();
            $table->string('technician_name')->nullable();
            $table->decimal('cost', 12, 2)->nullable();
            $table->text('description')->nullable();
            $table->text('work_done')->nullable();
            $table->string('status', 50)->default('scheduled');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            $table->index('maintenance_no');
            $table->index('asset_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_maintenances');
    }
};
