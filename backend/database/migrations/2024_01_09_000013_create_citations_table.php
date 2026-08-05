<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('citations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('publication_id')->constrained()->cascadeOnDelete();
            $table->string('cited_doi')->nullable();
            $table->string('cited_title')->nullable();
            $table->string('citing_source', 50);
            $table->string('source_name')->nullable();
            $table->string('citation_url')->nullable();
            $table->year('citation_year')->nullable();
            $table->date('cited_date')->nullable();
            $table->timestamps();
            
            $table->index('publication_id');
            $table->index('citing_source');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('citations');
    }
};
