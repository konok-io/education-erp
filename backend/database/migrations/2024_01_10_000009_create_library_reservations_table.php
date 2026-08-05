<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('library_reservations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('reservation_number')->unique();
            $table->foreignId('book_id')->constrained()->cascadeOnDelete();
            $table->foreignId('member_id')->constrained('library_members')->cascadeOnDelete();
            $table->date('reservation_date');
            $table->date('expiry_date')->nullable();
            $table->date('pickup_date')->nullable();
            $table->string('status', 50)->default('pending');
            $table->boolean('is_notified')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('reservation_number');
            $table->index('book_id');
            $table->index('member_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('library_reservations');
    }
};
