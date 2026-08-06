<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('library_reading_history', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('member_id')->constrained('library_members')->cascadeOnDelete();
            $table->foreignId('book_id')->nullable()->constrained('library_books')->nullOnDelete();
            $table->foreignId('book_copy_id')->nullable()->constrained('library_book_copies')->nullOnDelete();
            $table->timestamp('access_time')->useCurrent();
            $table->enum('access_type', ['issue', 'return', 'read', 'download', 'browse'])->default('browse');
            $table->string('ip_address', 50)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['member_id', 'access_time']);
            $table->index('access_time');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('library_reading_history');
    }
};
