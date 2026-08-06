<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incidents', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('incident_no')->unique();
            $table->foreignId('vehicle_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('driver_id')->nullable()->constrained()->nullOnDelete();
            $table->date('incident_date');
            $table->string('incident_type', 50);
            $table->text('description')->nullable();
            $table->string('status', 50)->default('reported');
            $table->foreignId('reported_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('resolution')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            
            $table->index('incident_no');
            $table->index('vehicle_id');
            $table->index('incident_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incidents');
    }
};
