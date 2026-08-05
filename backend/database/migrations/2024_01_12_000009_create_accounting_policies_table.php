<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_policies', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('policy_code')->unique();
            $table->string('policy_name');
            $table->text('description')->nullable();
            $table->string('policy_type', 50);
            $table->string('policy_value')->nullable();
            $table->boolean('is_system')->default(false);
            $table->boolean('is_active')->default(true);
            $table->foreignId('fiscal_year_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
            
            $table->index('policy_code');
            $table->index('policy_type');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_policies');
    }
};
