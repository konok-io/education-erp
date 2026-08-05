<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('journals', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('journal_name');
            $table->string('issn')->unique();
            $table->string('e_issn')->nullable()->unique();
            $table->string('publisher')->nullable();
            $table->string('country')->nullable();
            $table->string('website')->nullable();
            $table->string('email')->nullable();
            $table->text('description')->nullable();
            $table->decimal('impact_factor', 5, 2)->nullable();
            $table->string('quartile')->nullable();
            $table->string('category')->nullable();
            $table->boolean('is_indexed_scopus')->default(false);
            $table->boolean('is_indexed_wos')->default(false);
            $table->boolean('is_indexed_pubmed')->default(false);
            $table->string('frequency')->nullable();
            $table->decimal('apc', 10, 2)->nullable();
            $table->string('apc_currency', 10)->default('USD');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('issn');
            $table->index('quartile');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journals');
    }
};
