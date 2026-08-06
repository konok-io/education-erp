<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('library_issue_rules', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->enum('member_type', ['student', 'teacher', 'staff', 'researcher', 'guest', 'alumni', 'all'])->default('all');
            $table->foreignId('category_id')->nullable()->constrained('library_categories')->nullOnDelete();
            $table->integer('max_books')->default(5);
            $table->integer('max_days')->default(14);
            $table->integer('max_renewals')->default(2);
            $table->boolean('allow_reservation')->default(true);
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('library_issue_rules');
    }
};
