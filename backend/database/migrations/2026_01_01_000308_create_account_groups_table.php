<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('account_groups', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('group_code')->unique();
            $table->string('group_name');
            $table->string('account_type', 50);
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('group_code');
            $table->index('account_type');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('account_groups');
    }
};
