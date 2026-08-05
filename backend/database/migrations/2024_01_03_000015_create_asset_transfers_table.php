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
            $table->string('transfer_no')->unique();
            $table->foreignId('asset_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('from_holder_type')->nullable();
            $table->unsignedBigInteger('from_holder_id')->nullable();
            $table->string('from_holder_name')->nullable();
            $table->string('to_holder_type')->nullable();
            $table->unsignedBigInteger('to_holder_id')->nullable();
            $table->string('to_holder_name')->nullable();
            $table->string('from_location')->nullable();
            $table->string('to_location')->nullable();
            $table->date('transfer_date');
            $table->text('reason')->nullable();
            $table->string('status', 50)->default('pending');
            $table->foreignId('requested_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('transferred_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('transferred_at')->nullable();
            $table->timestamps();
            
            $table->index('transfer_no');
            $table->index('asset_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_transfers');
    }
};
