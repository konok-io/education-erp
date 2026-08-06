<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notices', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('notice_category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('campus_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('posted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->text('content');
            $table->string('type', 50)->default('general'); // general, academic, event, holiday, emergency
            $table->string('priority', 20)->default('normal'); // low, normal, high, urgent
            $table->date('publish_date');
            $table->date('expiry_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_pinned')->default(false);
            $table->json('attachments')->nullable();
            $table->json('target_audience')->nullable(); // all, students, parents, teachers, staff
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notices');
    }
};
