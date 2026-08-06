<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('publications', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('publication_code')->unique();
            $table->string('title');
            $table->text('abstract')->nullable();
            $table->string('publication_type', 50);
            $table->foreignId('project_id')->nullable()->constrained('research_projects')->nullOnDelete();
            $table->string('journal_name')->nullable();
            $table->string('journal_issn')->nullable();
            $table->string('publisher')->nullable();
            $table->string('volume')->nullable();
            $table->string('issue')->nullable();
            $table->string('pages')->nullable();
            $table->string('doi')->nullable()->unique();
            $table->string('url')->nullable();
            $table->year('publication_year');
            $table->date('publication_date')->nullable();
            $table->json('authors')->nullable();
            $table->json('keywords')->nullable();
            $table->json('co_authors')->nullable();
            $table->string('orcid')->nullable();
            $table->string(' Scopus_id')->nullable();
            $table->string('wos_id')->nullable();
            $table->string('google_scholar_id')->nullable();
            $table->integer('citation_count')->default(0);
            $table->decimal('impact_factor', 5, 2)->nullable();
            $table->string('quartile')->nullable();
            $table->string('status', 50)->default('published');
            $table->string('conference_name')->nullable();
            $table->string('conference_venue')->nullable();
            $table->date('conference_date')->nullable();
            $table->string('isbn')->nullable();
            $table->string('book_publisher')->nullable();
            $table->text('book_chapters')->nullable();
            $table->string('pdf_document')->nullable();
            $table->boolean('is_open_access')->default(false);
            $table->boolean('is_peer_reviewed')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            $table->index('publication_code');
            $table->index('publication_type');
            $table->index('publication_year');
            $table->index('doi');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('publications');
    }
};
