<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('taxes', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('tax_code')->unique();
            $table->string('tax_name');
            $table->string('tax_type', 50);
            $table->string('country', 100)->default('Bangladesh');
            $table->decimal('rate', 5, 2)->default(0);
            $table->string('calculation_method', 50)->default('exclusive');
            $table->date('effective_date');
            $table->date('expiry_date')->nullable();
            $table->boolean('is_compound')->default(false);
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->json('rules')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            $table->index('tax_code');
            $table->index('tax_type');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('taxes');
    }
};
