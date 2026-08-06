<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_knowledge_base', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('content');
            $table->text('summary')->nullable();
            $table->string('category', 100);
            $table->enum('type', ['article', 'faq', 'tutorial', 'video', 'documentation', 'policy'])->default('article');
            $table->string('video_url')->nullable();
            $table->string('thumbnail')->nullable();
            $table->json('tags')->nullable();
            $table->json('related_articles')->nullable();
            $table->foreignId('author_id')->constrained('users')->cascadeOnDelete();
            $table->boolean('is_published')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->boolean('requires_authentication')->default(false);
            $table->integer('view_count')->default(0);
            $table->integer('helpful_count')->default(0);
            $table->integer('not_helpful_count')->default(0);
            $table->foreignId('parent_id')->nullable()->constrained('crm_knowledge_base')->nullOnDelete();
            $table->integer('order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['category', 'is_published']);
            $table->index(['type', 'is_published']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_knowledge_base');
    }
};
