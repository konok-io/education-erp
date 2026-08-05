<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('internships', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('internship_number')->unique();
            $table->foreignId('employer_id')->constrained()->cascadeOnDelete();
            $table->string('internship_title');
            $table->text('description')->nullable();
            $table->string('internship_type', 50);
            $table->string('department')->nullable();
            $table->string('location')->nullable();
            $table->string('country')->nullable();
            $table->integer('positions')->default(1);
            $table->text('requirements')->nullable();
            $table->text('responsibilities')->nullable();
            $table->string('duration')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->decimal('stipend', 10, 2)->nullable();
            $table->string('stipend_currency', 10)->default('USD');
            $table->boolean('is_paid')->default(false);
            $table->boolean('is_remote')->default(false);
            $table->boolean('is_active')->default(true);
            $table->string('status', 50)->default('open');
            $table->foreignId('posted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            
            $table->index('internship_number');
            $table->index('internship_type');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('internships');
    }
};
