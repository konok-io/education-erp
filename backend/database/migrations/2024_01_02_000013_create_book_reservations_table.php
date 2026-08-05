<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('book_reservations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('reservation_no')->unique();
            $table->foreignId('member_id')->constrained('library_members')->cascadeOnDelete();
            $table->foreignId('book_id')->constrained()->cascadeOnDelete();
            $table->date('reservation_date');
            $table->date('expiry_date');
            $table->string('status', 50)->default('pending');
            $table->date('fulfilled_date')->nullable();
            $table->string('notify_status', 50)->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('reservation_no');
            $table->index('member_id');
            $table->index('book_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_reservations');
    }
};
