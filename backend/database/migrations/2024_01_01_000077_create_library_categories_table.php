<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('library_categories', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('name_bn')->nullable();
            $table->string('code', 50)->unique();
            $table->text('description')->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('library_categories')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->integer('lending_days')->default(14);
            $table->integer('lending_limit')->default(1);
            $table->boolean('is_reference_only')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['parent_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('library_categories');
    }
};
