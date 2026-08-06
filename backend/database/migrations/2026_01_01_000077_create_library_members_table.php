<?php

declare(strict_types=1);

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
            $table->string('member_id', 50)->unique();
            $table->string('card_number', 50)->nullable()->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('photo')->nullable();
            $table->enum('member_type', ['student', 'teacher', 'staff', 'researcher', 'guest', 'alumni'])->default('student');
            $table->string('institution')->nullable();
            $table->string('department')->nullable();
            $table->text('address')->nullable();
            $table->date('join_date')->default(now());
            $table->date('expiry_date')->nullable();
            $table->date('card_issue_date')->nullable();
            $table->date('card_renewal_date')->nullable();
            $table->enum('status', ['active', 'expired', 'blocked', 'suspended'])->default('active');
            $table->integer('max_books')->default(5);
            $table->integer('current_issued')->default(0);
            $table->decimal('outstanding_fine', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['member_type', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('library_members');
    }
};
