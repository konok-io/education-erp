<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('library_branches', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('branch_code')->unique();
            $table->string('branch_name');
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->string('building')->nullable();
            $table->string('floor')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->integer('capacity')->nullable();
            $table->boolean('is_digital')->default(false);
            $table->boolean('is_active')->default(true);
            $table->foreignId('manager_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            $table->index('branch_code');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('library_branches');
    }
};
