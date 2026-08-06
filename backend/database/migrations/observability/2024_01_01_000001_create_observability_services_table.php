<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('observability_services', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('type'); // api, frontend, database, queue, cache, storage
            $table->string('environment'); // production, staging, development
            $table->string('status')->default('healthy'); // healthy, degraded, down
            $table->json('metadata')->nullable();
            $table->json('tags')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['type', 'environment']);
            $table->index(['status', 'is_active']);
            $table->index('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('observability_services');
    }
};
