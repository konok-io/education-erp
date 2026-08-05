<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('library_waiting_lists', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('book_id')->constrained()->cascadeOnDelete();
            $table->foreignId('member_id')->constrained('library_members')->cascadeOnDelete();
            $table->integer('position')->default(1);
            $table->date('request_date');
            $table->date('notification_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('status', 50)->default('waiting');
            $table->boolean('is_notified')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('book_id');
            $table->index('member_id');
            $table->index('position');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('library_waiting_lists');
    }
};
