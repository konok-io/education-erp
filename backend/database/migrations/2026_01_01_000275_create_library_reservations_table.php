<?php

declare(strict_types=1);

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
            $table->string('reservation_no', 50)->unique();
            $table->foreignId('member_id')->constrained('library_members')->cascadeOnDelete();
            $table->foreignId('book_copy_id')->nullable()->constrained('library_book_copies')->nullOnDelete();
            $table->foreignId('book_id')->constrained('library_books')->cascadeOnDelete();
            $table->integer('queue_position')->default(1);
            $table->date('reserved_date')->default(now());
            $table->date('expiry_date')->nullable();
            $table->date('pickup_date')->nullable();
            $table->enum('status', ['pending', 'ready', 'picked_up', 'cancelled', 'expired'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['book_id', 'status']);
            $table->index(['member_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('library_reservations');
    }
};
