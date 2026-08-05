<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('library_members', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('member_code')->unique();
            $table->string('membership_type', 50);
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('photo')->nullable();
            $table->string('department')->nullable();
            $table->string('designation')->nullable();
            $table->string('institution')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('gender', 20)->nullable();
            $table->text('address')->nullable();
            $table->date('membership_start_date');
            $table->date('membership_end_date')->nullable();
            $table->integer('max_books_allowed')->default(5);
            $table->integer('max_days_allowed')->default(14);
            $table->decimal('max_fine_amount', 10, 2)->nullable();
            $table->boolean('can_access_digital')->default(true);
            $table->boolean('can_reserve')->default(true);
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('member_code');
            $table->index('user_id');
            $table->index('membership_type');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('library_members');
    }
};
