<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_transfers', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('transfer_number')->unique();
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            $table->string('from_location')->nullable();
            $table->string('to_location')->nullable();
            $table->string('from_user')->nullable();
            $table->string('to_user')->nullable();
            $table->date('transfer_date');
            $table->date('received_date')->nullable();
            $table->string('status', 50)->default('pending');
            $table->foreignId('requested_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('transfer_number');
            $table->index('asset_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_transfers');
    }
};
